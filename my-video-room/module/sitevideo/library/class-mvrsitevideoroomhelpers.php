<?php
/**
 * Addon functionality for Site Video Room. Support Room Creation and Management.
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo\Library\RoomHelpers
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\RoomAdmin as RoomAdminLibrary;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoRoomHelpers {

	/**
	 * Regenerate Room Helper
	 *
	 * @param ?string $input .
	 * @param int     $room_id - the room id.
	 * @param ?string $room_object . Object with preferences.
	 * @return string CallBack.
	 */
	public function regenerate_sitevideo_meeting_room( ?string $input = null, int $room_id, $room_object ): ?string {
		if ( MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $room_object->room_type ) {
			Factory::get_instance( ModuleConfig::class )->delete_room_mapping_by_id( intval( $room_object->post_id ) );
			$this->create_site_videoroom_page( $room_id, $room_object );
		}
		return $input;
	}

	/**
	 * Room Shortcode Transform
	 *
	 * @param ?string $input .
	 * @param ?string $room_type .
	 * @param int     $room_id - the room id.
	 * @param Object  $room_object .
	 *
	 * @return string name.
	 */
	public function conference_change_shortcode( ?string $input = null, ?string $room_type, int $room_id = null, $room_object ): ?string {
		if ( ! $room_type ) {
			return $input;
		}
		switch ( $room_type ) {
			case MVRSiteVideo::ROOM_NAME_SITE_VIDEO:
				if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
					return esc_html__( 'Module Disabled', 'myvideoroom' );
				} elseif ( null === $room_object->url ) {
					return 'Page Has Been Deleted - Please Regenerate';
				}
		}

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
		$page = require __DIR__ . '/../views/module-admin.php';
		return $page();
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
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
				MVRSiteVideo::SHORTCODE_SITE_VIDEO
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
							$details_section = ( require __DIR__ . '/../views/room-deleted.php' )( $room_object, 'normal' );
						} else {
							\check_admin_referer( 'delete_room_' . $room_id );
							return ( require __DIR__ . '/../views/room-delete-confirmation.php' )( $room_object );
						}
						break;

					case 'regenerate':
						\check_admin_referer( 'regenerate_room_' . $room_id );
						$room_object->id      = $this->regenerate_room( $room_id, $room_object );
						$room_object->post_id = $room_object->id;

						$details_section = ( require __DIR__ . '/../views/view-management-rooms.php' )( $room_object, 'normal' );
						break;

					default:
						$details_section = ( require __DIR__ . '/../views/view-management-rooms.php' )( $room_object, 'normal' );
				}
			}
		}

		return ( require __DIR__ . '/../views/site-conference-center.php' )(
			$this->get_rooms(),
			$details_section
		);
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
			$room_name = MVRSiteVideo::ROOM_NAME_SITE_VIDEO;
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
	 * Regenerate a page
	 *
	 * @param ?int       $original_room_id The original room id.
	 * @param ?\stdClass $room_object      The original room object.
	 *
	 * @return int
	 */
	public function create_site_videoroom_page( int $original_room_id = null, \stdClass $room_object = null ): int {
		if ( ! $room_object || MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $room_object->room_name ) {
			$new_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
				get_bloginfo( 'name' ) . ' ' . MVRSiteVideo::ROOM_TITLE_SITE_VIDEO,
				MVRSiteVideo::ROOM_SLUG_SITE_VIDEO,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
				MVRSiteVideo::SHORTCODE_SITE_VIDEO,
				$original_room_id,
			);
		} else {
			$new_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				$room_object->room_name,
				$room_object->display_name,
				$room_object->slug,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
				MVRSiteVideo::SHORTCODE_SITE_VIDEO,
				$original_room_id
			);
		}

		return $new_id;
	}
	/**
	 * Render Site Video Admin Page.
	 */
	public function render_default_settings_admin_page() {
		return ( require __DIR__ . '/../views/view-settings-video-default.php' )();
	}

	/**
	 * Render Default Settings Admin Page.
	 */
	public function render_sitevideo_admin_page() {
		return ( require __DIR__ . '/../views/module-admin.php' )();
	}

	/**
	 * Get the list of current rooms
	 *
	 * @return array
	 */
	private function get_rooms(): array {
		$available_rooms = Factory::get_instance( RoomMap::class )->get_all_post_ids_of_rooms();

		return array_map(
			function ( $room_id ) {
				$room = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

				$room->url  = Factory::get_instance( RoomAdminLibrary::class )->get_room_url( $room->room_name );
				$room->type = Factory::get_instance( RoomAdminLibrary::class )->get_room_type( $room->room_name );

				return $room;
			},
			$available_rooms
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
		// Modules Register this Filter to Handle Regeneration as per their logic.
		apply_filters( 'myvideoroom_room_manager_regenerate', '', $room_id, $room_object );
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
	 * Render Default Video Settings Page
	 *
	 * @param  array $input - the inbound menu.
	 * @return array - outbound menu.
	 */
	public function render_default_video_admin_settings_page( $input = array() ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Default Video Appearance', 'my-video-room' ),
			'videoappearance',
			fn() => $this->render_default_settings_admin_page()
		);
		array_push( $input, $admin_tab );
		return $input;
	}
}
