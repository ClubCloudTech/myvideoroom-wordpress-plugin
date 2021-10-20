<?php
/**
 * Addon functionality for Site Video Room. Support Room Creation and Management.
 *
 * @package MyVideoRoomPlugin\Modules\SiteVideo\Library\RoomHelpers
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\Admin;
use MyVideoRoomPlugin\Admin\AdminAjax;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\RoomAdmin as RoomAdminLibrary;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Module\Monitor\Module;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoRoomHelpers {

	/**
	 * Regenerate Room Helper
	 *
	 * @param ?string   $input       .
	 * @param int       $room_id     - the room id.
	 * @param \stdClass $room_object . Object with preferences.
	 *
	 * @return string CallBack.
	 */
	public function regenerate_sitevideo_meeting_room( ?string $input, int $room_id, \stdClass $room_object ): ?string {
		if ( MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $room_object->room_type ) {
			$new_room_id = $this->create_site_videoroom_page( $room_id, $room_object );
			Factory::get_instance( RoomMap::class )->update_room_post_id( $new_room_id, $room_object->room_name );
		}

		return $input;
	}

	/**
	 * Regenerate Room Helper
	 *
	 * @param ?string   $input       .
	 * @param int       $room_id     - the room id.
	 * @param \stdClass $room_object . Object with preferences.
	 *
	 * @return string CallBack.
	 */
	public function regenerate_redirect_room( ?string $input, int $room_id, \stdClass $room_object ): ?string {
		if ( MVRSiteVideo::ROOM_NAME_REDIRECT === $room_object->room_type ) {
			$new_room_id = $this->create_site_redirect_page( $room_id, $room_object );
			Factory::get_instance( RoomMap::class )->update_room_post_id( $new_room_id, $room_object->room_name );
		}

		return $input;
	}

	/**
	 * Create or Regenerate a page
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
	 * Create or Regenerate a Redirect page
	 *
	 * @param ?int       $original_room_id The original room id.
	 * @param ?\stdClass $room_object      The original room object.
	 *
	 * @return int
	 */
	public function create_site_redirect_page( int $original_room_id = null, \stdClass $room_object = null ): int {
		if ( ! $room_object || MVRSiteVideo::ROOM_NAME_REDIRECT === $room_object->room_name ) {
			$new_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				MVRSiteVideo::ROOM_NAME_REDIRECT,
				get_bloginfo( 'name' ) . ' ' . MVRSiteVideo::ROOM_TITLE_REDIRECT,
				MVRSiteVideo::ROOM_SLUG_REDIRECT,
				MVRSiteVideo::ROOM_NAME_REDIRECT,
				MVRSiteVideo::SHORTCODE_REDIRECT,
				$original_room_id,
				true
			);
		} else {
			$new_id = Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
				$room_object->room_name,
				$room_object->display_name,
				$room_object->slug,
				MVRSiteVideo::ROOM_NAME_REDIRECT,
				MVRSiteVideo::SHORTCODE_REDIRECT,
				$original_room_id,
				true
			);
		}

		return $new_id;
	}
	/**
	 * Room Shortcode Transform
	 *
	 * @param ?string   $input       .
	 * @param ?string   $room_type   .
	 * @param int|null  $room_id     - the room id.
	 * @param \stdClass $room_object .
	 *
	 * @return string name.
	 */
	public function conference_change_shortcode( ?string $input, ?string $room_type, ?int $room_id, \stdClass $room_object ): ?string {
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
	 * @param array $input - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_admin_settings_page( array $input ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Conference Center', 'myvideoroom' ),
			MVRSiteVideo::ROOM_SLUG_SITE_VIDEO,
			fn() => $this->get_sitevideo_admin_page()
		);
		array_push( $input, $admin_tab );
		\wp_enqueue_script( 'myvideoroom-sitevideo-add-room-js' );
		return $input;
	}

	/**
	 * Get sitevideo admin page - returns admin page
	 *
	 * @return string
	 */
	private function get_sitevideo_admin_page(): string {
		return ( require __DIR__ . '/../views/admin/module-admin.php' )();
	}

	/**
	 * Create the site conference page - through form posts.
	 * (deprecated - site moving to Ajax construction due to pre-validation)
	 *
	 * @param bool $shortcode - inbound shortcode name to use if needed.
	 * @return string
	 */
	public function create_site_conference_page( bool $shortcode = null ): string {
		$details_section = null;
		\wp_enqueue_script( 'myvideoroom-sitevideo-add-room-js' );
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
			// Render Scripts to Manage Room Add.
			Factory::get_instance( Module::class )->enqueue_monitor_scripts();
			\wp_enqueue_script( 'myvideoroom-sitevideo-add-room-js' );
			if ( ! $room_object ) {
				$details_section = null;
			} else {
				switch ( $action ) {
					case 'delete':
						if ( $delete_confirmed ) {
							\check_admin_referer( 'delete_room_confirmation_' . $room_id );
							$this->delete_room_and_post( $room_object );
							$details_section = ( require __DIR__ . '/../views/shortcode/room-deleted.php' )( $room_object, 'normal' );
						} else {
							\check_admin_referer( 'delete_room_' . $room_id );

							return ( require __DIR__ . '/../views/shortcode/room-delete-confirmation.php' )( $room_object );
						}
						break;

					case 'regenerate':
						\check_admin_referer( 'regenerate_room_' . $room_id );
						$room_object->id      = $this->regenerate_room( $room_id, $room_object );
						$room_object->post_id = $room_object->id;

						$details_section = ( require __DIR__ . '/../views/admin/view-management-rooms.php' )( $room_object, 'normal' );
						break;

					default:
						$details_section = ( require __DIR__ . '/../views/admin/view-management-rooms.php' )( $room_object, 'normal' );
				}
			}
		}

		if ( $shortcode ) {
			\wp_enqueue_script( 'myvideoroom-sitevideo-settings-js' );
			return ( require __DIR__ . '/../views/shortcode/shortcode-reception.php' )( $details_section );
		} else {
			return ( require __DIR__ . '/../views/admin/site-conference-center.php' )( $details_section );
		}
	}

	/**
	 * Delete the room and the associated post
	 *
	 * @param \stdClass $room_object The room object to delete.
	 *
	 * @return bool
	 */
	public function delete_room_and_post( \stdClass $room_object ): bool {
		Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_object->room_name );
		\wp_delete_post( $room_object->id, true );

		return true;
	}

	/**
	 * Regenerate a deleted room
	 *
	 * @param int       $room_id     The room id.
	 * @param \stdClass $room_object The room object.
	 *
	 * @return int
	 */
	private function regenerate_room( int $room_id, \stdClass $room_object ): int {
		// Modules Register this Filter to Handle Regeneration as per their logic.
		apply_filters( 'myvideoroom_room_manager_regenerate', '', $room_id, $room_object );

		return true;
	}

	/**
	 * Get the list of current rooms
	 *
	 * @param string $room_type     Category of Room if used.
	 *
	 * @return array
	 */
	public function get_rooms( string $room_type = null ): array {
		$available_rooms = Factory::get_instance( RoomMap::class )->get_all_post_ids_of_rooms( $room_type );

		return array_map(
			function ( $room_id ) {
				$room = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

				$room->url        = Factory::get_instance( RoomAdminLibrary::class )->get_room_url( $room->room_name );
				$room->post_title = Factory::get_instance( RoomAdminLibrary::class )->get_room_url( $room->room_name, true );
				$room->type       = Factory::get_instance( RoomAdminLibrary::class )->get_room_type( $room->room_name );

				return $room;
			},
			$available_rooms
		);
	}

	/**
	 * Get the list of current rooms
	 *
	 * @param string    $room_type     Category of Room if used.
	 * @param \stdClass $room The room object.
	 *
	 * @return ?string
	 */
	public function conference_check_reception_status( string $room_type = null, \stdClass $room ): ?string {
		$room_name   = Factory::get_instance( SiteDefaults::class )->room_map( 'sitevideo', $room->display_name );
		$text_single = esc_html__( 'One Guest Waiting', 'myvideoroom' );
		$text_plural = esc_html__( 'Guests Waiting', 'myvideoroom' );
		$monitor     = \do_shortcode( '[myvideoroom_monitor name="' . $room_name . '" text-single="' . $text_single . '" text-plural="' . $text_plural . '" ]' );

		return $monitor;
	}

	/**
	 * Render Site Video Room Setting Tab.
	 *
	 * @param array $input   - the inbound menu.
	 * @param int   $room_id - the room identifier.
	 *
	 * @return array - outbound menu.
	 */
	public function render_sitevideo_roomsetting_tab( array $input, int $room_id ): array {
		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
		if ( $room_object ) {
			$room_name = $room_object->room_name;
		} else {
			$room_name = MVRSiteVideo::ROOM_NAME_SITE_VIDEO;
		}

		$base_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM_SETTINGS ),
			'videosettings',
			fn() => Factory::get_instance( UserVideoPreference::class )
						->choose_settings(
							$room_id,
							$room_name
						)
		);
		array_push( $input, $base_menu );

		return $input;
	}

	/**
	 * Render Default Settings Admin Page.
	 */
	public function render_sitevideo_admin_page() {
		return ( require __DIR__ . '/../views/admin/module-admin.php' )();
	}

	/**
	 * Render Default Video Appearance Tab
	 *
	 * @param array $input - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_advanced_video_admin_tab( array $input ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Advanced', 'myvideoroom' ),
			'settingsadvanced',
			fn() => $this->render_advanced_video_appearance_screen()
		);
		array_push( $input, $admin_tab );

		return $input;
	}

	/**
	 * Default Advanced Video Screen Handler.
	 */
	public function render_advanced_video_appearance_screen() {
		return Factory::get_instance( Admin::class )->create_advanced_settings_page();
	}

	/**
	 * Render Advanced Video Settings Tab
	 *
	 * @param array $input - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_default_video_appearance_tab( array $input ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Default Video Appearance', 'myvideoroom' ),
			'videoappearance',
			fn() => $this->render_default_video_appearance_screen()
		);
		array_push( $input, $admin_tab );

		return $input;
	}

	/**
	 * Default Video Appearance Screen Handler.
	 */
	public function render_default_video_appearance_screen() {
		return ( require __DIR__ . '/../views/admin/view-settings-video-default.php' )();
	}

	/**
	 * Render Advanced Video Settings Tab
	 *
	 * @param array $input - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_login_tab( array $input ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Login Tab Settings', 'myvideoroom' ),
			'logintab',
			fn() => $this->render_login_screen()
		);
		array_push( $input, $admin_tab );

		return $input;
	}

	/**
	 * Default Video Appearance Screen Handler.
	 */
	public function render_login_screen() {
		return ( require __DIR__ . '/../views/login/view-login-settings.php' )();
	}

}
