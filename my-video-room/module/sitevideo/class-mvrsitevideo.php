<?php
/**
 * Addon functionality for Site Video Room.
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoControllers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoListeners;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Shortcode as Shortcode;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideo extends Shortcode {

		// Constants For Site Video Module.
		const MODULE_SITE_VIDEO_NAME           = 'site-video-module';
		const ROOM_NAME_SITE_VIDEO             = 'site-conference-room';
		const MODULE_SITE_VIDEO_ID             = \MyVideoRoomPlugin\Library\Dependencies::MODULE_SITE_VIDEO_ID;
		const MODULE_SITE_VIDEO_ADMIN_PAGE     = 'plugin-settings-sitevideo';
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

		// Listener for Handling Room Add. Listens for Room Adds- and Handles Form below.
		Factory::get_instance( MVRSiteVideoListeners::class )->site_videoroom_add_page();
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
	 * Render Security Admin Page.
	 */
	public function render_sitevideo_admin_page() {
		$active_tab = self::MODULE_SITE_VIDEO_NAME;
		$path       = Factory::get_instance( ModuleConfig::class )->get_module_admin_path( $active_tab );
		$render     = require WP_PLUGIN_DIR . '/my-video-room/' . $path;
		$messages   = array();
		//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Items already Sanitised.
		echo $render( $messages );
		return 'Powered by MyVideoRoom';
	}


	/**
	 * Get the setting section
	 */
	public function get_ajax_page_settings() {
		$post_id     = Factory::get_instance( Ajax::class )->get_text_parameter( 'postId' );
		$post_id_int = intval( $post_id );
		$input_type  = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );
		$render      = require WP_PLUGIN_DIR . '/my-video-room/module/sitevideo/views/view-management-rooms.php';
		// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - View already escaped.
		echo $render( $post_id_int, $input_type );
		die();
	}
}


