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

	private const YOUTUBE_EMBED_URL = 'https://www.youtube-nocookie.com/embed/%s?autoplay=1&modestbranding=1&mute=1&controls=0&cc_load_policy=1';

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
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );
		add_action( 'admin_head', fn() => do_action( 'myvideoroom_head' ) );

		wp_register_script( 'mvr-frametab', plugins_url( '../js/mvr-frametab.js', __FILE__ ), array(), $this->get_plugin_version(), true );
		wp_register_style( 'visualiser', plugins_url( '../css/visualiser.css', __FILE__ ), array(), $this->get_plugin_version() . '43', 'all' );
	}

	/**
	 * Show a configuration page for user to visualise shortcodes
	 *
	 * @return string
	 */
	public function output_shortcode(): string {
		// --
		// First Section - Handle Data from Inbound Form, and process it.

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'myvideoroom_visualiser_nonce', 'nonce' );
		}

		$room_name         = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_room_name'] ?? '' ) );
		$video_template    = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_layout_id_preference'] ?? null ) );
		$disable_floorplan = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_disable_floorplan_preference'] ?? '' ) ) === 'on';

		$enable_guest_reception = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_enabled_preference'] ?? '' ) ) === 'on';
		$reception_template     = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_id_preference'] ?? null ) );

		$video_reception_url = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_waiting_video_url'] ?? '' ) );

		if ( preg_match( '/[A-Za-z0-9_\-]{11}/', $video_reception_url ) ) {
			$video_reception_url = sprintf( self::YOUTUBE_EMBED_URL, $video_reception_url );
		}

		$video_reception_url = esc_url( $video_reception_url );

		if ( $disable_floorplan ) {
			$enable_guest_reception = true;
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

			if ( $enable_guest_reception ) {
				$app_config->enable_reception();

				if ( $reception_template ) {
					$app_config->set_reception_id( $reception_template );
				}

				if ( $video_reception_url ) {
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
		$output = ( require __DIR__ . '/../views/visualiser/settings.php' )( $available_layouts, $available_receptions, $app_config, self::$id_index++ );

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
			if ( $enable_guest_reception ) {
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

			$render = require __DIR__ . '/../views/visualiser/output.php';
			// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself.
			$output .= $render( $shortcode_host, $shortcode_guest, $text_shortcode_host, $text_shortcode_guest );
		}

		return $output;
	}
}
