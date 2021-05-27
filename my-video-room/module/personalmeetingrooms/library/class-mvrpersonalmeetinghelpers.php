<?php
/**
 * Addon functionality for Personal Meetings Functions to assist Personal Meetings Classes.
 *
 * @package MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingHelpers
 */

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Setup\RoomAdmin as SetupRoomAdmin;

/**
 * Class MVRPersonalMeetingHelpers - Functions to assist Personal Meetings Classes
 */
class MVRPersonalMeetingHelpers {

	/**
	 * Room Type Friendly Name
	 *
	 * @param string $room_type .
	 * @return string name.
	 */
	public function conference_room_friendly_name( string $room_type ): string {
		switch ( $room_type ) {
			case MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME:
				if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID ) ) {
					return esc_html__( 'Module Disabled', 'myvideoroom' );
				} else {
					return MVRPersonalMeeting::MODULE_PERSONAL_MEETING_TABLE_DISPLAY;
				}
		}
		return $room_type;
	}

	/**
	 * Room Shortcode Transform
	 *
	 * @param ?string $input .
	 * @param ?string $room_type .
	 * @param int     $room_id - the room id.
	 * @param Object  $room_object .
	 * @return string name.
	 */
	public function conference_change_shortcode( ?string $input = null, ?string $room_type, int $room_id = null, $room_object ): ?string {
		if ( ! $room_type ) {
			return $input;
		}
		switch ( $room_type ) {
			case MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME:
				if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID ) ) {
					return esc_html__( 'Module Disabled', 'myvideoroom' );
				} else {
					$shortcode = preg_replace( '/[^a-zA-Z0-9\s_]/', '', MVRPersonalMeeting::ROOM_SHORTCODE_PERSONAL_MEETING );
					return $shortcode;
				}
		}

		return $input;
	}

	/**
	 * Regenerate Room Helper
	 *
	 * @param ?string $input .
	 * @param int     $room_id - the room id.
	 * @param ?string $room_object . Object with preferences.
	 * @return string CallBack.
	 */
	public function regenerate_personal_meeting_room( ?string $input = null, int $room_id, $room_object ): ?string {
		if ( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME === $room_object->room_type ){
			Factory::get_instance( ModuleConfig::class )->delete_room_mapping_by_id( intval ( $room_object->post_id ) );
			Factory::get_instance( self::class )->create_personal_meetingroom_page();
		}
		return $input;
	}

	/**
	 * Regenerate or create a Personal Meeting Centre page
	 *
	 * @param ?int       $original_room_id The original room id.
	 * @param ?\stdClass $room_object      The original room object.
	 *
	 * @return int
	 */
	public function create_personal_meetingroom_page( int $original_room_id = null, \stdClass $room_object = null ): int {
		if ( ! $room_object || MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING === $room_object->room_name ) {
			$new_id = Factory::get_instance( SetupRoomAdmin::class )->create_and_check_personal_meetingroom_page(
				MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
				get_bloginfo( 'name' ) . ' ' . MVRPersonalMeeting::ROOM_TITLE_PERSONAL_MEETING,
				MVRPersonalMeeting::ROOM_SLUG_PERSONAL_MEETING,
				MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME,
				MVRPersonalMeeting::ROOM_SHORTCODE_PERSONAL_MEETING,
				$original_room_id,
			);
		} else {
			$new_id = Factory::get_instance( SetupRoomAdmin::class )->create_and_check_personal_meetingroom_page(
				$room_object->room_name,
				$room_object->display_name,
				$room_object->slug,
				MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME,
				MVRPersonalMeeting::ROOM_SHORTCODE_PERSONAL_MEETING,
				$original_room_id
			);
		}

		return $new_id;
	}
	/**
	 * Render Site Video Admin Settings Page
	 *
	 * @param  array $input - the inbound menu.
	 * @return array - outbound menu.
	 */
	public function render_personalmeeting_admin_settings_page( $input = array() ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Personal Meetings', 'my-video-room' ),
			'personalmeeting',
			fn() => Factory::get_instance( self::class )->render_personalvideo_admin_page()
		);
		array_push( $input, $admin_tab );
		return $input;
	}

	/**
	 * Render Personal Rooms Admin Page.
	 */
	public function render_personalvideo_admin_page() {
		return ( require __DIR__ . '/../views/view-settings-personalvideo.php' )();
	}

}
