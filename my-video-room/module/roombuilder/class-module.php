<?php
/**
 * Allow user to Visualise Shortcodes with specific video preferences
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder;

use MyVideoRoomPlugin\Admin\Page;
use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Shortcode\App;
use MyVideoRoomPlugin\ValueObject\GettingStarted;

/**
 * Class Module
 */
class Module {

	const SHORTCODE_TAG     = App::SHORTCODE_TAG . '_room_builder';
	const PAGE_SLUG_BUILDER = PageList::PAGE_SLUG_DEFAULT . '-room-builder';

	/**
	 * Install the shortcode
	 */
	public function __construct() {
		\add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );

		\add_action(
			'wp_enqueue_scripts',
			fn() => $this->enqueue_scripts_and_styles(),
		);

		$this->add_admin_actions_and_filters();
	}

	/**
	 * Enqueue required scripts and styles
	 *
	 * @param bool $admin If this is an admin setting.
	 */
	private function enqueue_scripts_and_styles( bool $admin = false ) {
		$plugin_version = Factory::get_instance( Version::class )->get_plugin_version();

		\wp_enqueue_style(
			'myvideoroom-room-builder-css',
			\plugins_url( '/css/roombuilder.css', \realpath( __FILE__ ) ),
			false,
			$plugin_version,
		);

		\wp_enqueue_style(
			'myvideoroom-shared-css',
			\plugins_url( '/css/shared.css', \realpath( __DIR__ . '/../' ) ),
			false,
			$plugin_version,
		);

		\wp_enqueue_script(
			'myvideoroom-room-builder',
			\plugins_url( '/js/roombuilder.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			$plugin_version,
			true
		);

		if ( $admin ) {
			\wp_enqueue_style(
				'myvideoroom-room-builder-admin-css',
				\plugins_url( '/css/roombuilder-admin.css', \realpath( __FILE__ ) ),
				false,
				$plugin_version,
			);
		} else {
			wp_enqueue_style(
				'myvideoroom-room-builder-public-css',
				plugins_url( '/css/roombuilder-public.css', realpath( __FILE__ ) ),
				false,
				$plugin_version,
			);
		}
	}

	/**
	 * Add actions and filters for admin area
	 */
	private function add_admin_actions_and_filters() {
		\add_action(
			'admin_enqueue_scripts',
			fn() => $this->enqueue_scripts_and_styles( true ),
		);

		\add_action(
			'myvideoroom_admin_menu',
			function ( callable $add_to_menu ) {
				$add_to_menu(
					new Page(
						self::PAGE_SLUG_BUILDER,
						\esc_html__( 'Room Builder', 'myvideoroom' ),
						array( new Admin(), 'create_room_builder_page' ),
					),
					1
				);
			}
		);

		\add_action(
			'myvideoroom_admin_getting_started_steps',
			function ( GettingStarted $steps ) {
				$steps->get_step( 2 )->set_description(
					\sprintf(
					/* translators: %s is the text "room builder" and links to the Room Builder Section */
						\esc_html__(
							'Use the visual %s to plan your room interactively, and learn about receptions and layouts.',
							'myvideoroom'
						),
						'<a href="' . \esc_url( \menu_page_url( self::PAGE_SLUG_BUILDER, false ) ) . '">' .
						\esc_html__( 'room builder', 'myvideoroom' ) .
						'</a>'
					)
				);
			}
		);

		\add_action(
			'myvideoroom_shortcode_reference',
			function ( callable $add_reference ) {
				$add_reference( ( new Reference() )->get_shortcode_reference() );
			}
		);
	}

	/**
	 * Show a configuration page for user to visualise shortcodes
	 *
	 * @param array|string $attributes Attributes passed from the shortcode to this function.
	 *
	 * @return string
	 */
	public function output_shortcode( $attributes = array() ): string {
		if ( ! $attributes ) {
			$attributes = array();
		}

		$post_library = Factory::get_instance( HttpPost::class );

		$available_layouts = Factory::get_instance( AvailableScenes::class )->get_available_layouts();
		if ( ! $available_layouts ) {
			return \esc_html__( 'No Layouts Found', 'myvideoroom' );
		}

		$available_receptions = Factory::get_instance( AvailableScenes::class )->get_available_receptions();

		$shortcode_constructor = null;

		if (
			$this->is_initial_preview_enabled( $attributes ) ||
			$post_library->is_post_request( 'show_roombuilder_preview' )
		) {
			$shortcode_constructor = \apply_filters( 'myvideoroom_roombuilder_create_shortcode', $this->create_shortcode_constructor() );
		}

		$output = ( require __DIR__ . '/views/settings.php' )(
			$available_layouts,
			$available_receptions,
			$shortcode_constructor
		);

		// --
		// If we have a config, then use it to render out the preview.

		if ( $shortcode_constructor ) {
			if (
				$post_library->is_post_request( 'show_roombuilder_preview' ) &&
				! $post_library->is_nonce_valid( 'show_roombuilder_preview' )
			) {
				$output .= $this->generate_nonce_error();
			} else {
				$output .= $this->generate_preview( $shortcode_constructor );
			}
		}

		return $output;
	}

	/**
	 * Is the initial preview enabled, is on by default unless explicitly disabled
	 *
	 * @param array $attributes Params passed from the shortcode to this function.
	 *
	 * @return bool
	 */
	private function is_initial_preview_enabled( array $attributes ): bool {
		return ! isset( $attributes['initial_preview'] ) || 'false' !== $attributes['initial_preview'];
	}

	/**
	 * Create the shortcode constructor
	 *
	 * @return AppShortcodeConstructor
	 */
	private function create_shortcode_constructor(): AppShortcodeConstructor {
		$post_library = Factory::get_instance( HttpPost::class );

		$room_name           = $post_library->get_string_parameter( 'room_builder_room_name' );
		$video_template      = $post_library->get_string_parameter( 'room_builder_layout_id_preference' );
		$reception_template  = $post_library->get_string_parameter( 'room_builder_reception_id_preference' );
		$video_reception_url = $post_library->get_string_parameter( 'room_builder_reception_waiting_video_url' );

		$disable_floorplan       = $post_library->get_checkbox_parameter( 'room_builder_disable_floorplan_preference', true );
		$enable_guest_reception  = $post_library->get_checkbox_parameter( 'room_builder_reception_enabled_preference', true );
		$use_multiple_shortcodes = $post_library->get_radio_parameter( 'room_builder_room_permissions_preference' ) === 'shortcode_pair';

		// if the reception url value is not a YouTube ID - then escape it.
		if ( ! \preg_match( '/^[A-Za-z0-9_\-]{11}$/', $video_reception_url ) ) {
			$video_reception_url = \esc_url( $video_reception_url );
		}

		$shortcode_constructor = AppShortcodeConstructor::create_instance();

		if ( $room_name ) {
			$shortcode_constructor->set_name( $room_name );
		}

		if ( $video_template ) {
			$shortcode_constructor->set_layout( $video_template );
		}

		if ( $use_multiple_shortcodes ) {
			$shortcode_constructor->set_as_host();
		}

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

		return $shortcode_constructor;
	}

	/**
	 * Generate an error page for the nonce being invalid
	 *
	 * @return string
	 */
	private function generate_nonce_error(): string {
		// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself.
		return ( require __DIR__ . '/views/error.php' )();
	}

	/**
	 * Generate the preview
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The settings for the preview.
	 *
	 * @return string
	 */
	private function generate_preview( AppShortcodeConstructor $shortcode_constructor ): string {
		$seed = \wp_generate_uuid4();

		$host_shortcode_constructor  = ( clone $shortcode_constructor );
		$guest_shortcode_constructor = ( clone $shortcode_constructor );

		$host_shortcode_visual_constructor = ( clone $host_shortcode_constructor )
			->set_user_name( 'Host' )
			->set_as_host()
			->set_seed( $seed );

		$guest_shortcode_visual_text_constructor = ( clone $guest_shortcode_constructor )
			->set_user_name( 'Guest' )
			->set_as_guest()
			->set_seed( $seed );

		$host_shortcode_text_constructor  = ( clone $host_shortcode_constructor );
		$guest_shortcode_text_constructor = ( clone $guest_shortcode_constructor );

		if ( $shortcode_constructor->is_host() ) {
			$guest_shortcode_text_constructor->set_as_guest();
		}

		// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself.
		return ( require __DIR__ . '/views/preview.php' )(
			$host_shortcode_visual_constructor,
			$guest_shortcode_visual_text_constructor,
			$host_shortcode_text_constructor,
			$guest_shortcode_text_constructor,
		);
	}
}
