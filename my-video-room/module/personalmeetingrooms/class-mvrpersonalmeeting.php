<?php
/**
 * Main Module Control Class Personal Meeting Rooms.
 *
 * @package MyVideoRoomPlugin\Modules\MVRPersonalMeetingRoom
 */

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference as UserVideoPreference;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingControllers;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingHelpers;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Shortcode\App;

/**
 * Class MVRPersonalMeetingRoom - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRPersonalMeeting {
		const SHORTCODE_TAG = App::SHORTCODE_TAG . '_';
		// Constants For Site Video Module.
		const MODULE_PERSONAL_MEETING_NAME            = 'personal-meeting-module';
		const SITE_PAGE_MEETING_CENTER                = 'meet-center';
		const ROOM_NAME_PERSONAL_MEETING              = Dependencies::ROOM_NAME_PERSONAL_MEETING;
		const ROOM_NAME_PERSONAL_MEETING_SITE_DEFAULT = 'site-default-personal-boardroom';
		const MODULE_PERSONAL_MEETING_ID              = 1065;
		const MODULE_PERSONAL_MEETING_ADMIN_LOCATION  = '/module/personalmeetingrooms/views/view-settings-personalvideo.php';
		const MODULE_PERSONAL_MEETING_DISPLAY         = 'Personal Meeting Room';
		const MODULE_PERSONAL_MEETING_TABLE_DISPLAY   = 'Personal Meeting Room Reception';
		const ROOM_TITLE_PERSONAL_MEETING             = ' Video Meetings';
		const ROOM_SLUG_PERSONAL_MEETING              = 'meet';
		const ROOM_SHORTCODE_PERSONAL_MEETING         = 'myvideoroom_meetswitch';

	/**
	 * Initialise On Module Activation
	 * Once off functions for activating Module
	 */
	public function activate_module() {
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_PERSONAL_MEETING_NAME, self::MODULE_PERSONAL_MEETING_ID, true, self::MODULE_PERSONAL_MEETING_ADMIN_LOCATION );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_PERSONAL_MEETING_ID, true );

		// Generate Personal Meeting Room Centre.
		Factory::get_instance( MVRPersonalMeetingHelpers::class )->create_personal_meetingroom_page();
	}
	/**
	 * Required for Normal Module Operation.
	 */
	public function init() {
		// Register Shortcodes.
		add_shortcode( self::SHORTCODE_TAG . 'meetswitch', array( Factory::get_instance( MVRPersonalMeetingControllers::class ), 'meet_switch_shortcode' ) );
		add_shortcode( self::SHORTCODE_TAG . 'personalmeetinghost', array( Factory::get_instance( MVRPersonalMeetingControllers::class ), 'personal_meeting_host_shortcode' ) );
		add_shortcode( self::SHORTCODE_TAG . 'personalmeetingguest', array( Factory::get_instance( MVRPersonalMeetingControllers::class ), 'personal_meeting_guest_shortcode' ) );
		add_shortcode( self::SHORTCODE_TAG . 'personalmeetinghostsettings', array( Factory::get_instance( MVRPersonalMeetingControllers::class ), 'personal_meeting_settings_shortcode' ) );

		// Update Listeners for Room Preference Forms.
		\add_action(
			'myvideoroom_admin_init',
			function () {
				Factory::get_instance( UserVideoPreference::class )->check_for_update_request();
				Factory::get_instance( SecurityVideoPreference::class )->check_for_update_request();
			}
		);

		add_filter( 'myvideoroom_room_manager_menu', array( Factory::get_instance( MVRPersonalMeetingHelpers::class ), 'create_personalmeeting_admin_settings_tab' ), 20, 1 );
		add_filter( 'myvideoroom_room_type_display_override', array( Factory::get_instance( MVRPersonalMeetingHelpers::class ), 'conference_room_friendly_name' ), 10, 1 );
		add_filter( 'myvideoroom_room_manager_shortcode_display', array( Factory::get_instance( MVRPersonalMeetingHelpers::class ), 'conference_change_shortcode' ), 10, 4 );
		add_filter( 'myvideoroom_room_manager_regenerate', array( Factory::get_instance( MVRPersonalMeetingHelpers::class ), 'regenerate_personal_meeting_room' ), 10, 3 );

	}
}
