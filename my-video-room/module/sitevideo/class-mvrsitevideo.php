<?php
/**
 * Addon functionality for Site Video Room.
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo;

use MyVideoRoomPlugin\Admin\Page;
use MyVideoRoomPlugin\Admin\PageList;
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
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\Shortcode as Shortcode;


/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideo extends Shortcode {


	const PAGE_SLUG_SITE_CONFERENCE = PageList::PAGE_SLUG_DEFAULT . '-site-conference';

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
	const ROOM_NAME_TABLE                  = 'Conference Center Room';

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
		Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_site_videoroom_page();

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

		add_filter( 'myvideoroom_sitevideo_admin_page_menu', array( Factory::get_instance( MVRSiteVideoRoomHelpers::class ), 'render_sitevideo_roomsetting_tab' ), 21, 2 );

		\add_action(
			'myvideoroom_admin_menu',
			function ( callable $add_to_menu ) {
				$add_to_menu(
					new Page(
						self::PAGE_SLUG_SITE_CONFERENCE,
						\esc_html__( 'Room Manager', 'myvideoroom' ),
						array( Factory::get_instance( MVRSiteVideoRoomHelpers::class ), 'create_site_conference_page' ),
					),
					1
				);
			},
			9
		);
		// Add Config Page to Main Room Manager.
		add_filter( 'myvideoroom_room_manager_menu', array( Factory::get_instance( MVRSiteVideoRoomHelpers::class ), 'render_sitevideo_admin_settings_page' ), 10, 1 );
		
		// Filters for Room Manager Table.
		add_filter( 'myvideoroom_room_type_display_override', array( Factory::get_instance( MVRSiteVideoViews::class ), 'conference_room_friendly_name' ), 10, 1 );
		add_filter( 'myvideoroom_room_manager_shortcode_display', array( Factory::get_instance( MVRSiteVideoRoomHelpers::class ), 'conference_change_shortcode' ), 10, 3 );

		// Regenerate Filter.
		add_filter( 'myvideoroom_room_manager_regenerate', array( Factory::get_instance( MVRSiteVideoRoomHelpers::class ), 'regenerate_sitevideo_meeting_room' ), 10, 3 );
	}

	/**
	 * Get Site Video Ajax Data
	 */
	public function get_ajax_page_settings() {
		$room_id    = (int) Factory::get_instance( Ajax::class )->get_text_parameter( 'roomId' );
		$input_type = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );

		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

		// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, $input_type );

		die();
	}
}
