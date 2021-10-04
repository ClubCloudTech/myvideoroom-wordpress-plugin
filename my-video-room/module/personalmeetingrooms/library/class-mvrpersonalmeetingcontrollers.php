<?php

/**
 * Addon functionality for Personal Meetings.
 *
 * @package MyVideoRoomPlugin\Modules\PersonalMeetingControllers
 */

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library;

use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomAdmin;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Library\VideoHelpers;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Library\MeetingIdGenerator;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingViews;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Module\Security\Library\SecurityEngine;

/**
 * Class MVRPersonalMeeting - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRPersonalMeetingControllers {

	/**
	 * A Shortcode for Personal Meetings Center- Host Entrance
	 * This is used for the Member Backend entry pages to access their preferred Video Layout - it is paired with the personalmeetingguest shortcode
	 * This depends on ultimate membership pro
	 *
	 * @return string
	 */
	public function personal_meeting_host_shortcode() {
		// Shortcode Initialise Hooks/Filters.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();
		$room_name = MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING;

		// Establish who is host.
		if ( \is_user_logged_in() ) {
			$user    = \wp_get_current_user();
			$user_id = $user->ID;
		}
		// Reject Invalid Users or Hosts not found/logged it etc.
		if ( ! $user_id ) {
			echo 'User Not Logged In - Can not host meeting <br>';
			return Factory::get_instance( MVRPersonalMeetingViews::class )->meet_guest_template();
		}
		// Security Engine - blocks room rendering if another setting has blocked it (eg upgrades, site lockdown, or other feature).

		$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $user_id, 'pbrhost', MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID, $room_name );
		if ( $render_block ) {
			return $render_block;
		}

		// Get Room Parameters.
		$video_template = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $user_id, $room_name );
		// Build the Room.
		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'userbr', $user_id ) )
			->set_layout( $video_template )
			->set_original_room_name( $room_name )
			->set_as_host();

		// Construct Shortcode Template - and execute.
		$header        = Factory::get_instance( MVRPersonalMeetingViews::class )->meet_host_header( $user_id );
		$host_status   = true;
		$output_object = array();
		$host_menu     = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM ),
			'videoroom',
			fn() => \do_shortcode( $myvideoroom_app->output_shortcode_text() ),
			'mvr-video'
		);
		array_push( $output_object, $host_menu );
		$admin_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM_SETTINGS ),
			'adminpage',
			fn() => \do_shortcode(
				Factory::get_instance( UserVideoPreference::class )->choose_settings(
					$user_id,
					$room_name,
				)
			)
		);
		array_push( $output_object, $admin_menu );

		return Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $user_id, $room_name, $host_status );
	}

	/**
	 * A Shortcode for the Boardroom View to Switch by Database Setting - Guest
	 * This is used for the Guest entry or Switch pages to access the Member Selected Video Layout - it is paired with the personalmeetinghost shortcode
	 * It accepts hostname as an argument which it gets from the Guest page URL get request parameter
	 *
	 * @param array $params -- host and invite both passed from users in form at reception. This function passes upstream to Main guest video function.
	 *
	 * @return string
	 */
	public function personal_meeting_guest_shortcode( $params = array() ) {
		$host   = $params['host'] ?? sanitize_text_field( wp_unslash( $_GET['host'] ?? '' ) );
		$invite = $params['invite'] ?? sanitize_text_field( wp_unslash( $_GET['invite'] ?? '' ) );

		return $this->boardroom_video_guest( $host, $invite );
	}

	/**
	 * Personal Boardroom Video - Guest entrance.
	 *
	 * @param  string $host - optional user name from parameter passed as url in personal meeting guest shortcode function.
	 * @param  string $invite - optional - invite code from hash generation function.
	 * @return string - the shorcode object.
	 */
	public function boardroom_video_guest( string $host, string $invite ): string {
		// Shortcode Initialise Hooks/Filters.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();
		$room_name = MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING;

		// Reject Blank Input.
		if ( ! ( $host ) && ! ( $invite ) ) {
			return Factory::get_instance( MVRPersonalMeetingViews::class )->meet_select_host_reception_template();
		}
		// Establish who is host.
		if ( $invite ) {
			$user_id = Factory::get_instance( MeetingIdGenerator::class )->invite( $invite, 'in', null );
		} else {
			$user    = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_identifier_string( $host );
			$user_id = $user->ID;
		}
		// Filter out invalid users.
		if ( ! $user_id ) {
			echo 'No Such User or Invite - Please Try Again<br>';
			return Factory::get_instance( MVRPersonalMeetingViews::class )->meet_select_host_reception_template();
		}
		// Security Engine - blocks room rendering if another setting has blocked it (eg upgrades, site lockdown, or other feature).
		$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $user_id, 'pbrguest', MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID, $room_name );
		if ( $render_block ) {
			return $render_block;
		}
		// Filter out users trying log into own room as guest.
		$user_checksum = \get_current_user_id();
		if ( \is_user_logged_in() ) {
			if ( $user_checksum === $user_id ) {
				$meet_page = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, 'url' );
				wp_safe_redirect( $meet_page );
				exit();
			}
		}

		// Get Room Layout and Reception Settings.
		$reception_setting     = Factory::get_instance( VideoHelpers::class )->get_enable_reception_state( $user_id, $room_name );
		$reception_template    = Factory::get_instance( VideoHelpers::class )->get_reception_template( $user_id, $room_name );
		$video_template        = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $user_id, $room_name );
		$video_reception_state = Factory::get_instance( VideoHelpers::class )->get_video_reception_state( $user_id, $room_name );
		$video_reception_url   = Factory::get_instance( VideoHelpers::class )->get_video_reception_url( $user_id, $room_name );
		$show_floorplan        = Factory::get_instance( VideoHelpers::class )->get_show_floorplan( $user_id, $room_name );

		// Base Room.
		$myvideoroom_app = AppShortcodeConstructor::create_instance()
		->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'userbr', $user_id ) )
		->set_original_room_name( $room_name )
		->set_layout( $video_template );

		// Reception.
		if ( $reception_setting && $reception_template ) {
			$myvideoroom_app->enable_reception()->set_reception_id( $reception_template );

			if ( $video_reception_state && $video_reception_url ) {

				$myvideoroom_app->set_reception_video_url( $video_reception_url );
			}
		}
		// Floorplan.
		if ( $show_floorplan ) {
			$myvideoroom_app->enable_floorplan();
		}

		// Construct Shortcode Template - and execute.
		$host_status   = false;
		$header        = Factory::get_instance( MVRPersonalMeetingViews::class )->meet_guest_header( $user_id );
		$output_object = array();
		$host_menu     = new MenuTabDisplay(
			esc_html__( 'Video Room', 'my-video-room' ),
			'videoroom',
			fn() => \do_shortcode( $myvideoroom_app->output_shortcode_text() ),
			'mvr-video'
		);

		array_push( $output_object, $host_menu );
		return Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $user_id, $room_name, $host_status );
	}
	/**
	 * A shortcode to switch The  Meet Centre
	 * the /meet room works from this logic
	 */
	public function meet_switch_shortcode() {

		if ( is_user_logged_in() ) {
			return Factory::get_instance( MVRPersonalMeetingViews::class )->meet_host_template();
		} else {
			return Factory::get_instance( MVRPersonalMeetingViews::class )->meet_guest_template();
		}
	}
	/**
	 * Personal_meeting_settings_shortcode - Renders a control setting for Personal Meeting rooms For the current user.
	 *
	 * @return null
	 */
	public function personal_meeting_settings_shortcode() {

		// Rejecting Logged out Users.
		if ( ! is_user_logged_in() ) {
			return null;
		}
		// Get User ID.
		$user_id = get_current_user_id();

		?>
	<table style="width:70%; border: 1px solid black;"  >
	</table>
		<h1><?php esc_html_e( 'Personal Meeting Video Room Settings', 'my-video-room' ); ?></h1>
		<p><?php esc_html_e( 'The Personal Video Room is private to each user. Use these settings to update your room configuration, privacy, and video layouts', 'my-video-room' ); ?><br>	</p>
		<?php
		$layout_setting = Factory::get_instance( \MyVideoRoomPlugin\Shortcode\UserVideoPreference::class )->choose_settings(
			$user_id,
			MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
			array( 'basic', 'premium' )
		);
		//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
		echo $layout_setting;
		?>
	</table>
		<?php
	}

	/**
	 * Meet_center_slug - returns meet center slug
	 *
	 * @return string - the meet center slug
	 */
	public function meet_center_slug() {
		$post_id = Factory::get_instance( RoomMap::class )->read();
		$slug    = get_post_field( 'post_name', $post_id );
		return $slug;
	}
}
