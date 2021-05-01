<?php
/**
 * Allow user to Visualise Shortcodes with specific video preferences
 *
 * @package MyVideoRoomPlugin\ShortcodeRoomVisualiser
 */

namespace MyVideoRoomPlugin\Visualiser;

use MyVideoRoomPlugin\Shortcode;
use MyVideoRoomPlugin\Visualiser\UserVideoPreference as UserVideoPreferenceEntity;

/**
 * Class UserVideoPreference
 * Allows the user to select their room display (note NOT Security) display parameters.
 */
class ShortcodeRoomVisualiser extends Shortcode {
	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	const DEFAULT_ROOM_NAME = 'Your Room Name';

	/**
	 * Install the shortcode
	 */
	public function init() {
		add_shortcode( 'visualizer', array( $this, 'visualiser_shortcode' ) );
		add_action( 'admin_head', fn() => do_action( 'myvideoroom_head' ) );
		wp_register_script( 'mvr-frametab', plugins_url( '../js/mvr-frametab.js', __FILE__ ), array(), $this->get_plugin_version(), true );
		wp_register_style( 'visualiser', plugins_url( '../css/visualiser.css', __FILE__ ), array(), $this->get_plugin_version() . '41', 'all' );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array $params List of shortcode params.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function visualiser_shortcode( $params = array() ): string {

		$room_name    = $params['room'] ?? 'default';
		$allowed_tags = array_map( 'trim', explode( ',', $params['tags'] ?? '' ) );
		// Not strictly needed as its a demo render- but preserving consistent structure with main Video Function.

		return $this->visualiser_worker( $room_name, $allowed_tags );
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param string $room_name The room name to fetch.
	 * @param array  $allowed_tags List of tags to allow.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function visualiser_worker( string $room_name, array $allowed_tags = array() ) {
		// we only enqueue the scripts if the shortcode is called to prevent it being added to all admin pages.
		do_action( 'myvideoroom_enqueue_scripts' );

		/*
			First Section - Handle Data from Inbound Form, and process it.

		*/
		$show_floorplan = false;
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'myvideoroom_extras_update_user_video_preference', 'nonce' );
		}
			$room_name             = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_room_name'] ?? self::DEFAULT_ROOM_NAME ) );
			$video_template        = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_layout_id_preference'] ?? null ) );
			$reception_template    = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_id_preference'] ?? null ) );
			$reception_setting     = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_enabled_preference'] ?? '' ) ) === 'on';
			$video_reception_state = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_video_enabled_preference'] ?? '' ) ) === 'on';
			$show_floorplan        = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_show_floorplan_preference'] ?? '' ) ) === 'on';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, - esc_url raw does the appropriate sanitisation.
			$video_reception_url = esc_url_raw( $_POST['myvideoroom_visualiser_reception_waiting_video_url'] ?? '' );

		if ( $show_floorplan ) {
				$show_floorplan    = true;
				$reception_setting = true;
		}
				$current_user_setting = new UserVideoPreferenceEntity(
					$room_name,
					$video_template,
					$reception_template,
					$reception_setting,
					$video_reception_state,
					$video_reception_url,
					$show_floorplan
				);

		$available_layouts    = $this->get_available_layouts( array( 'basic', 'premium' ) );
		$available_receptions = $this->get_available_receptions( array( 'basic', 'premium' ) );
		$render               = require __DIR__ . '/../views/visualiser/view-visualiser.php';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All upstream variables have already been sanitised in their function.
		echo $render( $available_layouts, $available_receptions, $current_user_setting, $room_name, self::$id_index++, $video_reception_url );

		/*
			Second Section - Handle Rendering of Inbound Shortcodes for correct construction.

			Use Entered Form Data to Build a Host and Guest Shortcode Pair
		*/

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD']
		) {
			// Guest Section.
				$myvideoroom_guest = MyVideoRoomApp::create_instance(
					$room_name,
					$video_template,
				);
			// Reception Handling.
			if ( $reception_setting && $reception_template ) {
				$myvideoroom_guest->enable_reception()->set_reception_id( $reception_template );

				if ( $video_reception_state && $video_reception_url ) {
					$myvideoroom_guest->set_reception_video_url( $video_reception_url );
				}
			}
			// Guest Floorplan option.
			if ( $show_floorplan ) {
				$myvideoroom_guest->enable_floorplan();
			}

			// Host Section Shortcode.
			$myvideoroom_host = MyVideoRoomApp::create_instance(
				$room_name,
				$video_template,
			)->enable_admin();

			// Construct Shortcode Template - and execute.

			$shortcode_host       = $myvideoroom_host->output_shortcode();
			$shortcode_guest      = $myvideoroom_guest->output_shortcode();
			$text_shortcode_host  = $myvideoroom_host->output_shortcode( 'shortcode-view-only' );
			$text_shortcode_guest = $myvideoroom_guest->output_shortcode( 'shortcode-view-only' );

			/*
				Third Section - Render Result Post Submit

			*/
			$render = require __DIR__ . '/../views/visualiser/view-visualiser-output.php';
			// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself. 
			echo $render( $shortcode_host, $shortcode_guest, $text_shortcode_host, $text_shortcode_guest );
		}

		return null;
	}

	/**
	 * Get a list of available layouts from MyVideoRoom
	 *
	 * @param array $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_layouts( array $allowed_tags = array( 'basic' ) ): array {
		$scenes = $this->get_available_scenes( 'layouts', $allowed_tags );
		if ( $scenes ) {
			return $scenes;
		} else {
			return array( esc_html_e( 'No Layouts Found', 'myvideoroom' ) );
		}
	}

	/**
	 * Get a list of available receptions from MyVideoRoom
	 *
	 * @param array $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_receptions( array $allowed_tags = array( 'basic' ) ): array {
		return $this->get_available_scenes( 'receptions', $allowed_tags );
	}

	/**
	 * Get a list of available scenes from MyVideoRoom
	 *
	 * @param string         $uri The type of scene (layouts/receptions).
	 * @param array|string[] $allowed_tags List of tags to fetch.
	 *
	 * @return array The Available Scenes.
	 */
	public function get_available_scenes( string $uri, array $allowed_tags = array( 'basic' ) ): array {
		$url     = 'https://rooms.clubcloud.tech/' . $uri;
		$tag_uri = array();

		foreach ( $allowed_tags as $allowed_tag ) {
			$tag_uri[] = 'tag[]=' . $allowed_tag;
		}

		if ( $tag_uri ) {
			$url .= '?' . implode( '&', $tag_uri );
		}

		$request = \wp_remote_get( $url );

		if ( \is_wp_error( $request ) ) {
			return array();
		}

		$body = \wp_remote_retrieve_body( $request );

		return \json_decode( $body );
	}
}
