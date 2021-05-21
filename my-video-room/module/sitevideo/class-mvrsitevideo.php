<?php
/**
 * Addon functionality for Site Video Room.
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo;

use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference as UserVideoPreference;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoControllers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoListeners;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideo extends Shortcode {

		// Constants For Site Video Module.
		const MODULE_SITE_VIDEO_NAME           = 'site-video-module';
		const ROOM_NAME_SITE_VIDEO             = 'site-conference-room';
		const MODULE_SITE_VIDEO_ID             = \MyVideoRoomPlugin\Library\Dependencies::MODULE_SITE_VIDEO_ID;
		const MODULE_SITE_VIDEO_ADMIN_LOCATION = '/module/sitevideo/views/view-settings-sitevideo.php';
		const MODULE_ROOM_MANAGEMENT_NAME      = 'site-video-multi-room-module';
		const MODULE_ROOM_MANAGEMENT_ID        = 435;
		const MODULE_ROOM_MANAGEMENT_PATH      = '/module/sitevideo/views/view-management-rooms.php';
		const MODULE_SITE_VIDEO_DISPLAY        = 'Site Conference Rooms';
		const MODULE_SITE_VIDEO_DESCRIPTION    = 'Meeting Center';
		const ROOM_TITLE_SITE_VIDEO            = 'Main Conference Room';
		const ROOM_SLUG_SITE_VIDEO             = 'conference';
		const ROOM_SHORTCODE_SITE_VIDEO        = 'myvideoroom_sitevideoroom';

	/**
	 * Initialise On Module Activation
	 * Once off functions for activating Module
	 */
	public function activate_module() {
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_SITE_VIDEO_NAME, self::MODULE_SITE_VIDEO_ID, true, self::MODULE_SITE_VIDEO_ADMIN_LOCATION );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SITE_VIDEO_ID, true );

		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_ROOM_MANAGEMENT_NAME, self::MODULE_ROOM_MANAGEMENT_ID, true, self::MODULE_ROOM_MANAGEMENT_PATH );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_ROOM_MANAGEMENT_ID, true );

		// Generate Site Video Room Page.
		$this->create_site_videoroom_page();

		// Configure Default Category Settings for Room.
		Factory::get_instance( RoomAdmin::class )->initialise_default_sitevideo_settings();

	}
	/**
	 * De-Initialise On Module De-activation.
	 * Once off functions for activating Module.
	 */
	public function de_activate_module() {
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SITE_VIDEO_ID, false );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_ROOM_MANAGEMENT_ID, false );
	}

	/**
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function init() {
		$site_video_controller = Factory::get_instance( MVRSiteVideoControllers::class );
		$this->add_shortcode( 'sitevideoroom', array( $site_video_controller, 'sitevideo_shortcode' ) );
		$this->site_videoroom_menu_setup();

		\add_action(
			'myvideoroom_admin_init',
			function () {
				Factory::get_instance( UserVideoPreference::class )->check_for_update_request();
				Factory::get_instance( SecurityVideoPreference::class )->check_for_update_request();
			}
		);

		\add_action( 'wp_ajax_myvideoroom_sitevideo_settings', array( $this, 'get_ajax_page_settings' ) );

		\wp_enqueue_script(
			'myvideoroom-sitevideo-settings-js',
			\plugins_url( '/js/settings.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version(),
			true
		);

		\wp_enqueue_script(
			'myvideoroom-sitevideo-add-room-js',
			\plugins_url( '/js/add-room.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version(),
			true
		);

		\wp_localize_script(
			'myvideoroom-sitevideo-settings-js',
			'myvideoroom_sitevideo_settings',
			array( 'ajax_url' => \admin_url( 'admin-ajax.php' ) )
		);

		add_filter( 'myvideoroom_sitevideo_admin_page_menu', array( $this, 'render_sitevideo_roomsetting_tab' ), 21, 2 );
	}

	/**
	 * Setup of Module Menu
	 */
	public function site_videoroom_menu_setup() {
		add_action( 'mvr_module_submenu_add', array( $this, 'site_videoroom_menu_button' ) );
	}

	/**
	 * Render Module Menu.
	 */
	public function site_videoroom_menu_button() {
		$name = self::MODULE_SITE_VIDEO_DISPLAY;
		$slug = self::MODULE_SITE_VIDEO_NAME;
		//phpcs:ignore --WordPress.WP.I18n.NonSingularStringLiteralText - $name is a constant text literal already.
		$display = esc_html__( $name, 'myvideoroom' );
		echo '<a class="mvr-menu-header-item" href="?page=my-video-room-extras&tab=' . esc_html( $slug ) . '">' . esc_html( $display ) . '</a>';
	}

	/**
	 * Create Site VideoRoom Handler
	 *
	 * @param  int $old_post_id - ID - the PostID that needs to be updated.
	 * @return null as its a database function.
	 */
	public function create_site_videoroom_page( $old_post_id = null ) {
		return Factory::get_instance( RoomAdmin::class )->create_and_check_page(
			self::ROOM_NAME_SITE_VIDEO,
			get_bloginfo( 'name' ) . ' ' . self::ROOM_TITLE_SITE_VIDEO,
			self::ROOM_SLUG_SITE_VIDEO,
			self::ROOM_SHORTCODE_SITE_VIDEO,
			$old_post_id,
		);
	}

	/**
	 * Render Site Video Admin Page.
	 */
	public function render_sitevideo_admin_page() {
		$room_settings = null;

		$deleted = false;

		$http_post_library = Factory::get_instance( HttpPost::class );
		$http_get_library  = Factory::get_instance( HttpGet::class );

		$delete         = $http_get_library->get_string_parameter( 'delete' );
		$delete_confirm = $http_get_library->get_string_parameter( 'confirm' );
		$room_id        = $http_get_library->get_integer_parameter( 'room_id' );

		if ( $http_post_library->is_admin_post_request( 'add_room' ) ) {
			$display_title = $http_post_library->get_string_parameter( 'add_room_title' );
			$room_slug     = $http_post_library->get_string_parameter( 'add_room_slug' );

			$room_id = Factory::get_instance( RoomAdmin::class )->create_and_check_page(
				strtolower( str_replace( ' ', '-', trim( $display_title ) ) ),
				$display_title,
				$room_slug,
				self::ROOM_SHORTCODE_SITE_VIDEO
			);
		}

		if ( null !== $room_id ) {
			$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

			if ( ! $room_object ) {
				return ( require __DIR__ . '/views/view-settings-sitevideo.php' )(
					\esc_html__( 'Room does not exist', 'myvideoroom' ),
					false
				);
			}

			if ( $delete_confirm ) {
				\check_admin_referer( 'delete_room_confirmation_' . $room_id );
				$room_name = $room_object->room_name;
				Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_name );
				\wp_delete_post( $room_id, true );
				$deleted = true;
			} elseif ( $delete ) {
				\check_admin_referer( 'delete_room_' . $room_id );
				return ( require __DIR__ . '/views/room-delete-confirmation.php' )( $room_object );
			} else {
				$room_settings = ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, 'normal' );
			}
		}

		return ( require __DIR__ . '/views/view-settings-sitevideo.php' )( $room_settings, $deleted );
	}

	/**
	 * Get Site Video Ajax Data
	 */
	public function get_ajax_page_settings() {
		$room_id    = (int) Factory::get_instance( Ajax::class )->get_text_parameter( 'roomId' );
		$input_type = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );

		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

		// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - View already escaped.
		echo ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, $input_type );
		die();
	}

	/**
	 * Render Site Video Room Setting Tab.
	 *
	 * @param  array $input - the inbound menu.
	 * @param  int   $room_id - the room identifier.
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_roomsetting_tab( $input = array(), int $room_id ): array {
		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
		$room_name   = $room_object->room_name;
		if ( ! $room_object ) {
			$room_name = self::ROOM_NAME_SITE_VIDEO;
		}
		$base_menu = new MenuTabDisplay(
			esc_html__( 'Video Settings', 'my-video-room' ),
			'videosettings',
			fn() => Factory::get_instance( UserVideoPreference::class )
			->choose_settings(
				$room_id,
				$room_name,
				array( 'basic', 'premium' )
			)
		);
		array_push( $input, $base_menu );
		return $input;
	}
}


