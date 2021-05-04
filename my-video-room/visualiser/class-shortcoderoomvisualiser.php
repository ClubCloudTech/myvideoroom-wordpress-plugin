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

		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_enqueue_style(
					'myvideoroom-visualiser-css',
					plugins_url( '/css/visualiser.css', realpath( __DIR__ . '/' ) ),
					false,
					$this->get_plugin_version(),
				);

				wp_enqueue_style(
					'myvideoroom-shared-css',
					plugins_url( '/css/shared.css', realpath( __DIR__ . '/' ) ),
					false,
					$this->get_plugin_version(),
				);
			},
		);

		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_style(
					'myvideoroom-visualiser-css',
					plugins_url( '/css/visualiser.css', realpath( __DIR__ . '/' ) ),
					false,
					$this->get_plugin_version(),
				);

				wp_enqueue_style(
					'myvideoroom-shared-css',
					plugins_url( '/css/shared.css', realpath( __DIR__ . '/' ) ),
					false,
					$this->get_plugin_version(),
				);

				wp_enqueue_style(
					'myvideoroom-visualiser-admin-css',
					plugins_url( '/css/visualiser-admin.css', realpath( __DIR__ . '/' ) ),
					false,
					$this->get_plugin_version(),
				);
			}
		);
	}

	/**
	 * Show a configuration page for user to visualise shortcodes
	 *
	 * @return string
	 */
	public function output_shortcode(): string {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			wp_verify_nonce( 'myvideoroom_visualiser_nonce', 'myvideoroom_visualiser_nonce' );
		}

		$available_layouts = Factory::get_instance( AvailableScenes::class )->get_available_layouts();
		if ( ! $available_layouts ) {
			return esc_html__( 'No Layouts Found', 'myvideoroom' );
		}

		$available_receptions = Factory::get_instance( AvailableScenes::class )->get_available_receptions();

		$shortcode_constructor = null;

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD']
		) {

			$room_name         = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_room_name'] ?? '' ) );
			$video_template    = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_layout_id_preference'] ?? null ) );
			$disable_floorplan = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_disable_floorplan_preference'] ?? '' ) ) === 'on';

			$enable_guest_reception = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_enabled_preference'] ?? '' ) ) === 'on';
			$reception_template     = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_id_preference'] ?? null ) );

			$video_reception_url = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_waiting_video_url'] ?? '' ) );

			/**
			 * If we have passed a YouTube video ID instead of the full url, then convert into a YouTube url
			 *
			 * @TODO - Move this into the app to make the shortcode simpler?
			 */
			if ( preg_match( '/[A-Za-z0-9_\-]{11}/', $video_reception_url ) ) {
				$video_reception_url = sprintf( self::YOUTUBE_EMBED_URL, $video_reception_url );
			}

			$video_reception_url = esc_url( $video_reception_url );

			$shortcode_constructor = AppShortcodeConstructor::create_instance(
				$room_name,
				$video_template,
			);

			if ( $disable_floorplan || $enable_guest_reception ) {
				$shortcode_constructor->enable_reception();

				if ( $reception_template ) {
					$shortcode_constructor->set_reception_id( $reception_template );
				}

				if ( $video_reception_url ) {
					$shortcode_constructor->set_reception_video_url( $video_reception_url );
				}
			}

			if ( $disable_floorplan ) {
				$shortcode_constructor->disable_floorplan();
			}
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All upstream variables have already been sanitised in their function.
		$output = ( require __DIR__ . '/../views/visualiser/settings.php' )(
			$available_layouts,
			$available_receptions,
			$shortcode_constructor,
			self::$id_index++
		);

		// --
		// If we have a config, then use it to render out the visualiser.

		if ( $shortcode_constructor ) {
			if ( ! $shortcode_constructor->get_name() ) {
				$shortcode_constructor->set_name( Factory::get_instance( Host::class )->get_host() );
			}

			$seed = \wp_generate_uuid4();

			$host_shortcode_constructor  = ( clone $shortcode_constructor )->enable_admin();
			$guest_shortcode_constructor = ( clone $shortcode_constructor )->disable_admin();

			$host_shortcode_visual_constructor = ( clone $host_shortcode_constructor )
				->set_user_name( 'Host' )
				->set_seed( $seed );

			$guest_shortcode_visual_text_constructor = ( clone $guest_shortcode_constructor )
				->set_user_name( 'Guest' )
				->set_seed( $seed );

			$host_shortcode_text_constructor  = ( clone $host_shortcode_constructor );
			$guest_shortcode_text_constructor = ( clone $guest_shortcode_constructor );

			// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself.
			$output .= (require __DIR__ . '/../views/visualiser/output.php')(
				$host_shortcode_visual_constructor,
				$guest_shortcode_visual_text_constructor,
				$host_shortcode_text_constructor,
				$guest_shortcode_text_constructor,
			);
		}

		return $output;
	}
}
