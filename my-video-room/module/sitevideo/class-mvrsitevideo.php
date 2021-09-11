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
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoControllers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\Shortcode\Reception;
use MyVideoRoomPlugin\Shortcode\App;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideo {
	const PAGE_SLUG_SITE_CONFERENCE = PageList::PAGE_SLUG_DEFAULT . '-site-conference';

	// Constants For Site Video Module.
	const MODULE_SITE_VIDEO_NAME        = 'site-video-module';
	const ROOM_NAME_SITE_VIDEO          = 'site-conference-room';
	const MODULE_SITE_VIDEO_ID          = \MyVideoRoomPlugin\Library\Dependencies::MODULE_SITE_VIDEO_ID;
	const MODULE_ROOM_MANAGEMENT_NAME   = 'site-video-multi-room-module';
	const MODULE_ROOM_MANAGEMENT_ID     = 435;
	const MODULE_SITE_VIDEO_DESCRIPTION = 'Meeting Center';
	const ROOM_TITLE_SITE_VIDEO         = 'Main Conference Room';
	const ROOM_SLUG_SITE_VIDEO          = 'conference';
	const SHORTCODE_SITE_VIDEO          = App::SHORTCODE_TAG . '_sitevideoroom';
	const RECEPTION_ROOM_FLAG           = 'reception_room';

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

		Factory::get_instance( RoomInfo::class )->init();
		Factory::get_instance( Reception::class )->init();

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
			5
		);
		if ( ! \is_user_logged_in() ) {
			add_filter(
				'myvideoroom_welcome_page',
				array(
					Factory::get_instance( MVRSiteVideoViews::class ),
					'render_login_tab_welcome',
				),
				5,
				5
			);
		}
		if ( ! \is_user_admin() ) {
			add_filter(
				'myvideoroom_main_template_render',
				array(
					Factory::get_instance( MVRSiteVideoViews::class ),
					'render_reception_tab_welcome',
				),
				6,
				5
			);
		}

		// Ajax Handler for SiteVideo Room.
		\add_action( 'wp_ajax_myvideoroom_sitevideo_settings', array( $this, 'get_ajax_page_settings' ), 10, 2 );

		\wp_enqueue_script(
			'myvideoroom-sitevideo-settings-js',
			\plugins_url( '/js/settings.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version(),
			true
		);

		\wp_localize_script(
			'myvideoroom-sitevideo-settings-js',
			'myvideoroom_sitevideo_settings',
			array( 'ajax_url' => \admin_url( 'admin-ajax.php' ) )
		);

		\wp_enqueue_script(
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
					Factory::get_instance( Version::class )->get_plugin_version() . '18',
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
					Factory::get_instance( Version::class )->get_plugin_version() . '3',
					'(max-width: 640px)'
				);
			},
		);

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

		// Add Config Page to Default Settings.
		add_filter(
			'myvideoroom_permissions_manager_menu',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'render_default_video_admin_settings_page',
			),
			10,
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
		// Regenerate Filter.
		add_filter(
			'myvideoroom_room_manager_regenerate',
			array(
				Factory::get_instance( MVRSiteVideoRoomHelpers::class ),
				'regenerate_sitevideo_meeting_room',
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
		\wp_enqueue_script(
			'socket-io-3.1.0',
			\plugins_url( '/../monitor/third-party/socket.io.js', __FILE__ ),
			array(),
			'3.1.0',
			true
		);

		\wp_enqueue_script(
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
	}

	/**
	 * Get Site Video Ajax Data
	 */
	public function get_ajax_page_settings() {

		$room_id    = (int) Factory::get_instance( Ajax::class )->get_text_parameter( 'roomId' );
		$input_type = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );

		// Case Room Render for Reception Shortcode.

		if ( self::RECEPTION_ROOM_FLAG === $input_type ) {
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped.
			echo Factory::get_instance( MVRSiteVideoControllers::class )->site_videoroom_host_function( $room_id );

		} elseif ( SiteDefaults::USER_ID_SITE_DEFAULTS === \intval( $room_id ) && self::ROOM_NAME_SITE_VIDEO === $input_type ) {
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( require __DIR__ . '/views/view-settings-conference-center-default.php' )();

		} else {
			$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, $input_type );
		}
		die();
	}


	/**
	 * Render SiteVideo Welcome Tab.
	 *
	 * @param array $input   - the inbound menu.
	 * @param int   $room_id - the room identifier.
	 *
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_welcome_tabs( array $input, int $room_id, $host_status = null, $header ): array {
		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

		if ( ! $room_object ) {
			return $input;
		}

		// Host Menu Tab - rendered in Security as its a module feature of Security.
		$host_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_INFO_WELCOME ),
			'welcomepage',
			fn() => $this->render_welcome_tab( $room_id, $host_status, $header )
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
	 * @param int          $room_id     - the user or entity identifier.
	 * @param bool         $host_status - whether function is for a host type.
	 * @param string|array $header - Data Object with Header Info.
	 *
	 * @return string - outbound menu.
	 */
	public function render_welcome_tab( int $room_id, bool $host_status = null, $header ): string {
		$html_library = Factory::get_instance( HTML::class, array( 'welcometab' ) );
		$tabs         = \apply_filters( 'myvideoroom_welcome_page', array(), $room_id, $host_status, $header );
		$render       = require __DIR__ . '/views/header/view-welcometab.php';

		return $render( $tabs, $html_library, $room_id, $host_status, $header );

	}
}
