<?php
/**
 * Allow user to Visualise Shortcodes with specific video preferences
 *
 * @package MyVideoRoomPlugin\ShortcodeRoomVisualiser
 */

namespace MyVideoRoomPlugin\Visualiser;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\Host;
use MyVideoRoomPlugin\Shortcode;
use MyVideoRoomPlugin\Visualiser\UserVideoPreference as UserVideoPreferenceEntity;

/**
 * Class UserVideoPreference
 * Allows the user to select their room display (note NOT Security) display parameters.
 */
class ShortcodeRoomVisualiser extends Shortcode {

	const SHORTCODE_TAG = 'myvideoroom_visualizer';

	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Install the shortcode
	 */
	public function init() {
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'visualiser_shortcode' ) );
		add_action( 'admin_head', fn() => do_action( 'myvideoroom_head' ) );

		wp_register_script( 'mvr-frametab', plugins_url( '../js/mvr-frametab.js', __FILE__ ), array(), $this->get_plugin_version(), true );
		wp_register_style( 'visualiser', plugins_url( '../css/visualiser.css', __FILE__ ), array(), $this->get_plugin_version() . '43', 'all' );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array $params List of shortcode params.
	 *
	 * @return string
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
	 */
	public function visualiser_worker( string $room_name, array $allowed_tags = array() ): string {
		// we only enqueue the scripts if the shortcode is called to prevent it being added to all admin pages.
		do_action( 'myvideoroom_enqueue_scripts' );

		// --
		// First Section - Handle Data from Inbound Form, and process it.

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'myvideoroom_visualiser_nonce', 'nonce' );
		}

		$room_name      = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_room_name'] ?? '' ) );
		$video_template = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_layout_id_preference'] ?? null ) );
		$disable_floorplan = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_disable_floorplan_preference'] ?? '' ) ) === 'on';

		$reception_setting  = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_enabled_preference'] ?? '' ) ) === 'on';
		$reception_template = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_id_preference'] ?? null ) );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, - esc_url raw does the appropriate sanitisation.
		$video_reception_url = esc_url_raw( $_POST['myvideoroom_visualiser_reception_waiting_video_url'] ?? '' );

		if ( $disable_floorplan ) {
			$reception_setting = true;
		}

		$rendered_room_name = $room_name;

		if ( ! $rendered_room_name ) {
			$rendered_room_name = Factory::get_instance( Host::class )->get_host();
		}

		$app_config = null;

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD']
		) {
			$app_config = MyVideoRoomApp::create_instance(
				$room_name,
				$video_template,
			);

			if ( $reception_setting ) {
				$app_config->enable_reception();
				$app_config->set_reception_id( $reception_template );

				if ( $app_config ) {
					$app_config->set_reception_video_url( $video_reception_url );
				}
			}

			if ( $disable_floorplan ) {
				$app_config->disable_floorplan();
			}
		}

		$available_layouts = Factory::get_instance( AvailableScenes::class )->get_available_layouts();
		if ( ! $available_layouts ) {
			return esc_html__( 'No Layouts Found', 'myvideoroom' );
		}

		$available_receptions = Factory::get_instance( AvailableScenes::class )->get_available_receptions();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All upstream variables have already been sanitised in their function.
		echo ( require __DIR__ . '/../views/visualiser/settings.php' )( $available_layouts, $available_receptions, $app_config, self::$id_index++ );

		// --
		// Second Section - Handle Rendering of Inbound Shortcodes for correct construction.
		// Use Entered Form Data to Build a Host and Guest Shortcode Pair.

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD']
		) {
			// Guest Section.
			$myvideoroom_guest = MyVideoRoomApp::create_instance(
				$rendered_room_name,
				$video_template,
			);

			// Reception Handling.
			if ( $reception_setting ) {
				$myvideoroom_guest->enable_reception();

				if ( $reception_template ) {
					$myvideoroom_guest->set_reception_id( $reception_template );
				}

				if ( $video_reception_url ) {
					$myvideoroom_guest->set_reception_video_url( $video_reception_url );
				}
			}

			// Guest Floorplan option.
			if ( $disable_floorplan ) {
				$myvideoroom_guest->disable_floorplan();
			}

			// Host Section Shortcode.
			$myvideoroom_host = MyVideoRoomApp::create_instance(
				$rendered_room_name,
				$video_template,
			)->enable_admin();

			// Construct Shortcode Template - and execute.

			$shortcode_host       = $myvideoroom_host->output_shortcode();
			$shortcode_guest      = $myvideoroom_guest->output_shortcode();
			$text_shortcode_host  = $myvideoroom_host->output_shortcode( 'shortcode-view-only' );
			$text_shortcode_guest = $myvideoroom_guest->output_shortcode( 'shortcode-view-only' );

			// --
			// Third Section - Render Result Post Submit

			$render = require __DIR__ . '/../views/visualiser/view-visualiser-output.php';
			// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself.
			echo $render( $shortcode_host, $shortcode_guest, $text_shortcode_host, $text_shortcode_guest );
		}

		return '';
	}
}
