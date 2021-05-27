<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library;

use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\MeetingIdGenerator;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;

/**
 * Class MVRPersonalMeeting - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRPersonalMeetingViews {

	// ---
	// Meet Center Template Section.

	/**
	 * Render Guest /Meet Page Template for no invite and no username -
	 * Template used for Trapping no input into meet centre and asking user for invite, or username
	 *
	 * @return string
	 */
	public function meet_select_host_reception_template() {
		\wp_enqueue_style( 'myvideoroom-frontend-css' );
		$render = require __DIR__ . '/../views/view-reception-template.php';
		return $render();
	}

	/**
	 * Render Meeting Center Host template
	 */
	public function meet_host_template() {
		$html_library = Factory::get_instance( HTML::class, array( 'meetcentre' ) );
		$inbound_tabs = array();
		$render       = require __DIR__ . '/../views/view-meetcentre.php';
		$host_page    = new MenuTabDisplay(
			esc_html__( 'Host a Meeting', 'myvideoroom' ),
			'hostmeeting',
			fn() => Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_host_shortcode()
		);

		/**
		 * A list of tabs to show
		 *
		 * @var \MyVideoRoomPlugin\Entity\MenuTabDisplay[] $tabs
		 */
		$tabs = apply_filters( 'myvideoroom_meet_centre_host_menu', $inbound_tabs );
		array_push( $tabs, $host_page );
		return $render( $tabs, $html_library );

	}

	/**
	 * Render Meeting Center Guest Template
	 */
	public function meet_guest_template() {
		$html_library = Factory::get_instance( HTML::class, array( 'meetcentre' ) );
		$inbound_tabs = array();
		$render       = require __DIR__ . '/../views/view-meetcentre.php';

		/**
		 * A list of tabs to show
		 *
		 * @var \MyVideoRoomPlugin\Entity\MenuTabDisplay[] $tabs
		 */
		$tabs = apply_filters( 'myvideoroom_meet_centre_guest_menu', $inbound_tabs );
		return $render( $tabs, $html_library );

	}

	/**
	 * Render guest header template for meetings - used above guest room video shortcodes - provides meeting invite links, name, owner etc
	 *
	 * @param int $host_id - the Host ID of the room.
	 * @return string
	 */
	public function meet_guest_header( $host_id ): string {
		$module_id    = MVRPersonalMeeting::MODULE_PERSONAL_MEETING_DISPLAY;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$user         = get_user_by( 'id', $host_id );
		$name_output  = esc_html__( 'Visiting ', 'my-video-room' ) . $user->user_nicename;
		$is_guest     = true;
		$meeting_link = Factory::get_instance( MeetingIdGenerator::class )->invite_menu_shortcode( array( 'user_id' => $user_id ) );

		return $render( $module_id, $name_output, $host_id, MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, $is_guest, $meeting_link );
	}

	/**
	 * Render Host header template for meetings - used above Host room video shortcodes - provides meeting invite links, name, owner etc
	 *
	 * @return string
	 */
	public function meet_host_header(): string {

		$module_id    = MVRPersonalMeeting::MODULE_PERSONAL_MEETING_DISPLAY;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$user         = wp_get_current_user();
		$user_id      = \get_current_user_id();
		$name_output  = esc_html__( 'Host ', 'my-video-room' ) . $user->user_nicename;
		$is_guest     = false;
		$meeting_link = Factory::get_instance( MeetingIdGenerator::class )->invite_menu_shortcode( array( 'user_id' => $user_id ) );

		return $render( $module_id, $name_output, $user_id, MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, $is_guest, $meeting_link );
	}
}
