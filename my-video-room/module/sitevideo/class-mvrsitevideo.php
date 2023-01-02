<?php
/**
 * Addon functionality for Site Video Room.
 *
 * @package MyVideoRoomPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo;

use MyVideoRoomPlugin\Admin;
use MyVideoRoomPlugin\Admin\Page;
use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\DAO\Setup;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference as UserVideoPreference;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Library\Maintenance;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingHelpers;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoAjax;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoControllers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRedirect;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\Shortcode\Reception;
use MyVideoRoomPlugin\Shortcode\App;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideo {
	const PAGE_SLUG_SITE_CONFERENCE = PageList::PAGE_SLUG_DEFAULT . '-site-conference';

	// Constants For Site Video Module.
	const MODULE_SITE_VIDEO_NAME        = 'site-video-module';
	const ROOM_NAME_SITE_VIDEO          = 'site-conference-room';
	const MODULE_SITE_VIDEO_ID          = Dependencies::MODULE_SITE_VIDEO_ID;
	const MODULE_ROOM_MANAGEMENT_NAME   = 'site-video-multi-room-module';
	const MODULE_ROOM_MANAGEMENT_ID     = 435;
	const MODULE_SITE_VIDEO_DESCRIPTION = 'Meeting Center';
	const ROOM_TITLE_SITE_VIDEO         = 'Main Conference Room';
	const ROOM_SLUG_SITE_VIDEO          = 'conference';
	const SHORTCODE_SITE_VIDEO          = App::SHORTCODE_TAG . '_sitevideoroom';
	const SHORTCODE_REDIRECT            = App::SHORTCODE_TAG . '_redirect';
	const ROOM_NAME_REDIRECT            = 'site-redirect';
	const ROOM_TITLE_REDIRECT           = 'MyVideoRoom Redirect Handler';
	const ROOM_SLUG_REDIRECT            = 'myvideoroomredirect';
	const RECEPTION_ROOM_FLAG           = 'reception_room';
	const USER_STATE_INFO               = 'user_state_info';
	const SETTING_HOST                  = '434331';
	const SETTING_GUEST                 = '331434';
	const SETTING_TEMPLATE_OVERRIDE     = 'myvideoroom-setting-template-override';

	/**
	 * Initialise On Module Activation
	 * Once off functions for activating Module
	 */
	public function activate_module() {
		Factory::get_instance( Setup::class )->initialise_default_video_settings();
		Factory::get_instance( ModuleConfig::class )->register_module_in_db(
			self::MODULE_SITE_VIDEO_NAME,
			self::MODULE_SITE_VIDEO_ID,
			true
		);
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SITE_VIDEO_ID, true );

		Factory::get_instance( ModuleConfig::class )->register_module_in_db(
			self::MODULE_ROOM_MANAGEMENT_NAME,
			self::MODULE_ROOM_MANAGEMENT_ID,
			true
		);
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_ROOM_MANAGEMENT_ID, true );

		// Generate Site Video Room Page.
		Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_site_videoroom_page();

		// Generate Site redirect iframe page.
		Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_site_redirect_page();

		// Configure Default Category Settings for Room.
		Factory::get_instance( RoomAdmin::class )->initialise_default_sitevideo_settings();

		// Activate Default Settings for DB Maintenance.
		Factory::get_instance( Maintenance::class )->activate();

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

		Factory::get_instance( RoomInfo::class )->init();
		Factory::get_instance( Reception::class )->init();
		Factory::get_instance( MVRSiteVideoRedirect::class )->init();

		add_shortcode( self::SHORTCODE_SITE_VIDEO, array( $site_video_controller, 'sitevideo_shortcode' ) );

		\add_action(
			'myvideoroom_admin_init',
			function () {
				Factory::get_instance( UserVideoPreference::class )->check_for_update_request();
				Factory::get_instance( SecurityVideoPreference::class )->check_for_update_request();
			}
		);

		// Add Welcome Tabs to Template.
		add_filter(
			'myvideoroom_main_template_render',
			array(
				$this,
				'render_sitevideo_welcome_tabs',
			),
			5,
			6
		);
		// Reception Center Tab.
		if ( current_user_can( 'administrator' ) ) {
			add_filter(
				'myvideoroom_main_template_render',
				array(
					Factory::get_instance( MVRSiteVideoViews::class ),
					'render_reception_tab_welcome',
				),
				6,
				6
			);
		}

		// Shortcode Admin Page Rename.
		add_filter( 'myvideoroom_room_manager_shortcode_display', array( Factory::get_instance( MVRPersonalMeetingHelpers::class ), 'conference_change_shortcode' ), 10, 4 );

		if ( ! \is_user_logged_in() ) {
			add_filter(
				'myvideoroom_welcome_page',
				array(
					Factory::get_instance( MVRSiteVideoViews::class ),
					'render_login_tab_welcome',
				),
				5,
				6
			);
		}

		// Ajax Handler for SiteVideo.
		\add_action( 'wp_ajax_myvideoroom_sitevideo_settings', array( Factory::get_instance( MVRSiteVideoAjax::class ), 'get_ajax_page_settings' ), 10, 2 );
		\add_action( 'wp_ajax_myvideoroom_base_ajax', array( Factory::get_instance( MVRSiteVideoAjax::class ), 'file_upload_handler' ), 10, 2 );
		\add_action( 'wp_ajax_nopriv_myvideoroom_base_ajax', array( Factory::get_instance( MVRSiteVideoAjax::class ), 'file_upload_handler' ), 10, 2 );

		// Initialise PHPSESSION to track logged out users.
		$this->start_php_session();

		\wp_register_script(
			'myvideoroom-sitevideo-settings-js',
			\plugins_url( '/js/settings.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version() . \wp_rand( 1, 2000 ),
			true
		);

		\wp_localize_script(
			'myvideoroom-sitevideo-settings-js',
			'myvideoroom_sitevideo_settings',
			array(
				'ajax_url' => \admin_url( 'admin-ajax.php' ),
				'security' => \wp_create_nonce( 'settings_ajax' ),
			)
		);

		// Register Script Ajax Upload.
		\wp_register_script(
			'myvideoroom-webcam-stream-js',
			\plugins_url( '/../../js/mvr-stream.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version() . \wp_rand( 40, 30000 ),
			true
		);
		\wp_enqueue_script( 'myvideoroom-webcam-stream-js' );

		// Localize script Ajax Upload.
			$script_data_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'handle_picture_upload' ),

			);
			// Register Script Iframe Handling.
			\wp_register_script(
				'myvideoroom-iframe-handler',
				\plugins_url( '/../../js/iframe-manage.js', \realpath( __FILE__ ) ),
				array( 'jquery' ),
				Factory::get_instance( Version::class )->get_plugin_version() . \wp_rand( 40, 30000 ),
				true
			);
			wp_localize_script(
				'myvideoroom-webcam-stream-js',
				'myvideoroom_base_ajax',
				$script_data_array
			);

		\wp_register_script(
			'myvideoroom-sitevideo-add-room-js',
			\plugins_url( '/js/add-room.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version(),
			true
		);

		add_filter(
			'myvideoroom_sitevideo_admin_page_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_sitevideo_roomsetting_tab',
			),
			21,
			2
		);

		\add_action(
			'wp_enqueue_scripts',
			function () {
				\wp_enqueue_style(
					'myvideoroom-frontend-css',
					\plugins_url( '/css/frontend.css', \realpath( __DIR__ . '/../' ) ),
					false,
					Factory::get_instance( Version::class )->get_plugin_version(),
					'(min-width: 640px)'
				);
			},
		);
		\add_action(
			'wp_enqueue_scripts',
			function () {
				\wp_enqueue_style(
					'myvideoroom-frontend-mobile-css',
					\plugins_url( '/css/frontend-mobile.css', \realpath( __DIR__ . '/../' ) ),
					false,
					Factory::get_instance( Version::class )->get_plugin_version(),
					'(max-width: 640px)'
				);
			},
		);
		// Add Menu Page to Main Plugin.
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
		add_filter(
			'myvideoroom_room_manager_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_sitevideo_admin_settings_page',
			),
			10,
			1
		);

		// Add Video Appearance Page to Default Settings.
		add_filter(
			'myvideoroom_permissions_manager_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_default_video_appearance_tab',
			),
			10,
			1
		);

		// Add Maintenance Tab to Default Settings.
		add_filter(
			'myvideoroom_permissions_manager_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_maintenance_tab',
			),
			90,
			1
		);

		// Add Login Page Settings to Default Settings.
		add_filter(
			'myvideoroom_permissions_manager_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_login_tab',
			),
			10,
			1
		);

		// Add Video Advanced Page to Default Settings.
		add_filter(
			'myvideoroom_permissions_manager_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_advanced_video_admin_tab',
			),
			11,
			1
		);

		// Filters for Room Manager Table.
		add_filter(
			'myvideoroom_room_type_display_override',
			array(
				Factory::get_instance( MVRSiteVideoViews::class ),
				'conference_room_friendly_name',
			),
			10,
			1
		);
		add_filter(
			'myvideoroom_room_manager_shortcode_display',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'conference_change_shortcode',
			),
			10,
			4
		);
		add_filter(
			'myvideoroom_conference_room_type_column_field',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'conference_check_reception_status',
			),
			10,
			2
		);
		// Regenerate Filter Site Video Page.
		add_filter(
			'myvideoroom_room_manager_regenerate',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'regenerate_sitevideo_meeting_room',
			),
			10,
			3
		);

		// Regenerate Filter Site Video Page.
		add_filter(
			'myvideoroom_room_manager_regenerate',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'regenerate_redirect_room',
			),
			10,
			3
		);

		\add_action(
			Admin::ACTION_SHORTCODE_REFERENCE,
			function ( callable $add_reference ) {
				$add_reference( ( new ShortcodeReference() )->get_shortcode_reference() );
			}
		);
		\wp_enqueue_script( 'jquery' );
		\wp_register_script(
			'socket-io-3.1.0',
			\plugins_url( '/../monitor/third-party/socket.io.js', __FILE__ ),
			array(),
			'3.1.0',
			true
		);

		\wp_register_script(
			'myvideoroom-monitor',
			\plugins_url( '/../monitor/js/monitor.js', __FILE__ ),
			array( 'jquery', 'socket-io-3.1.0' ),
			Factory::get_instance( Version::class )->get_plugin_version(),
			true
		);
		\wp_localize_script(
			'myvideoroom-monitor',
			'myvideoroom_monitor_texts',
			array(
				'reception' => array(
					'textEmpty'  => \esc_html__( 'Nobody is currently waiting', 'myvideoroom' ),
					'textSingle' => \esc_html__( 'One person is waiting in reception', 'myvideoroom' ),
					'textPlural' => \esc_html__( '{{count}} people are waiting in reception', 'myvideoroom' ),
				),
				'seated'    => array(
					'textEmpty'  => \esc_html__( 'Nobody is currently seated', 'myvideoroom' ),
					'textSingle' => \esc_html__( 'One person is seated', 'myvideoroom' ),
					'textPlural' => \esc_html__( '{{count}} people are seated', 'myvideoroom' ),
				),
				'all'       => array(
					'textEmpty'  => \esc_html__( 'Nobody is currently in this room', 'myvideoroom' ),
					'textSingle' => \esc_html__( 'One person is currently in this room', 'myvideoroom' ),
					'textPlural' => \esc_html__( '{{count}} people are currently in this room', 'myvideoroom' ),
				),
			)
		);

		$style_override = get_option( self::SETTING_TEMPLATE_OVERRIDE );

		if ( $style_override ) {
			\wp_enqueue_style(
				'myvideoroom-frontend-override',
				\plugins_url( '/css/frontend-override.css', \realpath( __DIR__ . '/../' ) ),
				false,
				Factory::get_instance( Version::class )->get_plugin_version(),
			);
		}
	}

	/**
	 * Render SiteVideo Welcome Tab.
	 *
	 * @param array $input   - the inbound menu.
	 * @param int   $room_id - the room identifier.
	 *
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_welcome_tabs( array $input, int $room_id, $host_status = null, $header = null, array $null = null, bool $in_ajax = null ): array {

		if ( $in_ajax ) {
			return $input;
		}

		$host_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_INFO_WELCOME ),
			'welcomepage',
			fn() => $this->render_welcome_tab(),
			'mvr-welcome-page'
		);

		// Change Order and add tab first for signed out users.
		if ( ! \is_user_logged_in() ) {
			array_unshift( $input, $host_menu );
		} else {
			array_push( $input, $host_menu );
		}

		return $input;
	}

	/**
	 * Controller Function to Render Welcome Page in Main Shortcode.
	 *
	 * @return ?string - outbound menu.
	 */
	public function render_welcome_tab(): ?string {
		$render = require __DIR__ . '/views/header/view-welcometab.php';

		return $render();

	}

	/**
	 * Start PHP Session
	 * Starts PHP Session Cookie in case user is signed out.
	 *
	 * @return void
	 */
	public function start_php_session() {

		if ( ! session_id() ) {
			session_start();
		}
	}

}
