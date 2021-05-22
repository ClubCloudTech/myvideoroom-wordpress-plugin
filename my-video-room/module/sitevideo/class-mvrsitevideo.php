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

		\add_action(
			'myvideoroom_admin_menu',
			function ( callable $add_to_menu ) {
				$add_to_menu(
					new Page(
						self::PAGE_SLUG_SITE_CONFERENCE,
						\esc_html__( 'Room Manager', 'myvideoroom' ),
						array( $this, 'create_site_conference_page' ),
					),
					1
				);
			},
			9
		);
		// Add Config Filter to Main Room Manager.
		add_filter( 'myvideoroom_room_manager_menu', array( $this, 'render_sitevideo_admin_settings_page' ), 10, 1 );
		// Name Override Filter for Room Manager Table.
		add_filter( 'myvideoroom_room_type_display_override', array( Factory::get_instance( MVRSiteVideoViews::class ), 'conference_room_friendly_name' ), 10, 1 );
	}

	/**
	 * Regenerate a page
	 *
	 * @param ?int       $original_room_id The original room id.
	 * @param ?\stdClass $room_object      The original room object.
	 *
	 * @return int
	 */
	public function create_site_videoroom_page( int $original_room_id = null, \stdClass $room_object = null ): int {
		if ( ! $room_object || self::ROOM_NAME_SITE_VIDEO === $room_object->room_name ) {
			$new_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				self::ROOM_NAME_SITE_VIDEO,
				get_bloginfo( 'name' ) . ' ' . self::ROOM_TITLE_SITE_VIDEO,
				self::ROOM_SLUG_SITE_VIDEO,
				self::ROOM_NAME_SITE_VIDEO,
				'[' . self::ROOM_SHORTCODE_SITE_VIDEO . ' id="' . $post_id . '"]',
				$original_room_id,
			);
		} else {
			$new_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				$room_object->room_name,
				$room_object->display_name,
				$room_object->slug,
				self::ROOM_NAME_SITE_VIDEO,
				self::ROOM_SHORTCODE_SITE_VIDEO,
				$original_room_id
			);
		}

		return $new_id;
	}

	/**
	 * Create the site conference page
	 *
	 * @return string
	 */
	public function create_site_conference_page(): string {
		$details_section = null;

		$http_post_library = Factory::get_instance( HttpPost::class );
		$http_get_library  = Factory::get_instance( HttpGet::class );

		$room_id = $http_get_library->get_integer_parameter( 'room_id' );

		if ( $http_post_library->is_admin_post_request( 'add_room' ) ) {
			$display_title = $http_post_library->get_string_parameter( 'site_conference_center_new_room_title' );
			$room_slug     = $http_post_library->get_string_parameter( 'site_conference_center_new_room_slug' );

			$room_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				strtolower( str_replace( ' ', '-', trim( $display_title ) ) ),
				$display_title,
				$room_slug,
				self::ROOM_NAME_SITE_VIDEO,
				self::ROOM_SHORTCODE_SITE_VIDEO
			);
		}

		if ( $room_id ) {
			$http_get_library = Factory::get_instance( HttpGet::class );
			$action           = $http_get_library->get_string_parameter( 'action' );
			$delete_confirmed = $http_get_library->get_string_parameter( 'confirm' );

			$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

			if ( ! $room_object ) {
				$details_section = \esc_html__( 'Room does not exist', 'myvideoroom' );
			} else {
				switch ( $action ) {
					case 'delete':
						if ( $delete_confirmed ) {
							\check_admin_referer( 'delete_room_confirmation_' . $room_id );
							$this->delete_room_and_post( $room_object );
							$details_section = ( require __DIR__ . '/views/room-deleted.php' )( $room_object, 'normal' );
						} else {
							\check_admin_referer( 'delete_room_' . $room_id );
							return ( require __DIR__ . '/views/room-delete-confirmation.php' )( $room_object );
						}
						break;

					case 'regenerate':
						\check_admin_referer( 'regenerate_room_' . $room_id );
						$room_object->id      = $this->regenerate_room( $room_id, $room_object );
						$room_object->post_id = $room_object->id;

						$details_section = ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, 'normal' );
						break;

					default:
						$details_section = ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, 'normal' );

				}
			}
		}

		return ( require __DIR__ . '/views/site-conference-center.php' )(
			$this->get_rooms(),
			$details_section
		);
	}

	/**
	 * Regenerate a deleted room
	 *
	 * @param int       $room_id     The room id.
	 * @param \stdClass $room_object The room object.
	 *
	 * @return integer
	 */
	private function regenerate_room( int $room_id, \stdClass $room_object ): int {
		apply_filters( 'myvideoroom_room_manager_regenerate', '', $room_id, $room_object );
		/*if ( $regenerate_callback ) {
			return $regenerate_callback();
		} else {
			return $this->create_site_videoroom_page( $room_id, $room_object );
		}*/
		return true;
	}

	/**
	 * Delete the room and the associated post
	 *
	 * @param \stdClass $room_object The room object to delete.
	 *
	 * @return bool
	 */
	private function delete_room_and_post( \stdClass $room_object ): bool {
		Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_object->room_name );
		\wp_delete_post( $room_object->id, true );

		return true;
	}

	/**
	 * Get the list of current rooms
	 *
	 * @return array
	 */
	private function get_rooms(): array {
		$available_rooms = Factory::get_instance( RoomMap::class )->get_room_list();

		return array_map(
			function ( $room_id ) {
				$room = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

				$room->url = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room->room_name, 'url' );

				$room->type = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room->room_name, 'type' );

				return $room;
			},
			$available_rooms
		);
	}

	/**
	 * Render Site Video Admin Page.
	 */
	public function render_sitevideo_admin_page() {
		return ( require __DIR__ . '/views/module-admin.php' )();
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

	/**
	 * Render Site Video Room Setting Tab.
	 *
	 * @param  array $input - the inbound menu.
	 * @param  int   $room_id - the room identifier.
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_roomsetting_tab( array $input, int $room_id ): array {
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

	/**
	 * Render Site Video Admin Settings Page
	 *
	 * @param  array $input - the inbound menu.
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_admin_settings_page( $input = array() ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Conference Center', 'my-video-room' ),
			'conferencecenter',
			fn() => Factory::get_instance( self::class )->get_sitevideo_admin_page()
		);
		array_push( $input, $admin_tab );
		return $input;
	}

	/**
	 * Get_sitevideo_admin_page - returns admin page
	 *
	 * @return string
	 */
	private function get_sitevideo_admin_page() {
		$page = require __DIR__ . '/views/module-admin.php';
		return $page();
	}
}


