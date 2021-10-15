<?php
/**
 * Addon functionality for BuddyPress -Video Room Handlers for BuddyPress
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressVideo.php
 */

namespace MyVideoRoomPlugin\Module\BuddyPress\Library;

use \MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Library\VideoHelpers;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;
use MyVideoRoomPlugin\Module\BuddyPress\Views\BuddyPressViews;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingViews;
use MyVideoRoomPlugin\Module\Security\Library\SecurityEngine;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Shortcode\App;

/**
 * Class BuddyPress
 */
class BuddyPressVideo {
	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_';
	/**
	 * Provide Runtime
	 */
	public function init() {
		add_shortcode( self::SHORTCODE_TAG . 'bpboardroomguest', array( $this, 'bp_boardroom_video_guest' ) );
		add_shortcode( self::SHORTCODE_TAG . 'bpboardroomswitch', array( $this, 'bp_boardroom_video_host' ) );
	}

	/**
	 * A Shortcode for the User Meeting Room to be rendered on BuddyPress profile pages
	 * This is used for the Guest entry
	 *
	 * @return string|null
	 */
	public function bp_boardroom_video_guest() {
		// Escape dependencies.
		if ( ! Factory::get_instance( BuddyPress::class )->can_module_be_activated() ) {
			return null;
		}

		// Shortcode Initialise Hooks/Filters.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();
		$room_name = MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING;

		// Establish who is host.
		$user_id    = \bp_displayed_user_id();
		$my_user_id = get_current_user_id();

		// Check if user is looking at own profile page - return host mode if they do, guest if they dont.
		if ( $user_id === $my_user_id ) {
			return $this->bp_boardroom_video_host();
		}

		// Security Engine - blocks room rendering if another setting has blocked it ( eg upgrades, site lockdown, or other feature ).

		$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $user_id, 'bppbrguest', BuddyPress::MODULE_BUDDYPRESS_ID, $room_name );
		if ( $render_block ) {
			return $render_block;
		}

		// Get Room Layout and Reception Settings.
		$reception_setting     = Factory::get_instance( VideoHelpers::class )->get_enable_reception_state( $user_id, $room_name );
		$reception_template    = Factory::get_instance( VideoHelpers::class )->get_reception_template( $user_id, $room_name );
		$video_template        = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $user_id, $room_name );
		$video_reception_state = Factory::get_instance( VideoHelpers::class )->get_video_reception_state( $user_id, $room_name );
		$video_reception_url   = Factory::get_instance( VideoHelpers::class )->get_video_reception_url( $user_id, $room_name );

		// Build Shortcode.

		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'userbr', $user_id ) )
			->set_original_room_name( $room_name )
			->set_layout( $video_template );

		// Check Floorplan Status.
		$show_floorplan = Factory::get_instance( VideoHelpers::class )->get_show_floorplan( $user_id, $room_name );

		if ( $show_floorplan ) {
			$myvideoroom_app->enable_floorplan();
		}

		if ( $reception_setting && $reception_template ) {
			$myvideoroom_app->enable_reception()->set_reception_id( $reception_template );

			if ( $video_reception_state ) {

				$myvideoroom_app->set_reception_video_url( $video_reception_url );
			}
		}
		// Construct Shortcode Template - and execute.
		$header        = Factory::get_instance( MVRPersonalMeetingViews::class )->meet_guest_header( $user_id );
		$output_object = array();
		$host_status   = false;

		$host_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM ),
			'videoroom',
			fn() => \do_shortcode( $myvideoroom_app->output_shortcode_text() ),
			'mvr-video'
		);
		array_push( $output_object, $host_menu );

		return Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $user_id, $room_name, $host_status );

	}

	/**
	 * Function for User Meeting Room to be rendered on BuddyPress profile pages
	 * This is used for the Host entry (with logic for auto redirects to guest if not in own profile)
	 *
	 * @return string|null
	 */
	public function bp_boardroom_video_host():string {
		// Escape dependencies.
		if ( ! Factory::get_instance( BuddyPress::class )->can_module_be_activated() ) {
			return null;
		}

		// Shortcode Initialise Hooks/Filters.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();
		$room_name = MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING;

		// Adding Listeners for Update.
		Factory::get_instance( UserVideoPreference::class )->check_for_update_request();
		Factory::get_instance( SecurityVideoPreference::class )->check_for_update_request();

		// Establish who is host.
		$user_id    = \bp_displayed_user_id();
		$my_user_id = get_current_user_id();

		// Check if user is looking at own profile page - continue if they do, redirect to guest if they dont.
		if ( $user_id !== $my_user_id ) {
			return $this->bp_boardroom_video_guest();
		}

		// Security Engine - blocks room rendering if another setting has blocked it ( eg upgrades, site lockdown, or other feature ).

		$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $user_id, 'bppbrhost', BuddyPress::MODULE_BUDDYPRESS_ID, $room_name );
		if ( $render_block ) {
			return $render_block;
		}

		// Get Room Parameters. Build Room.
		$video_template = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $user_id, $room_name );

		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'userbr', $user_id ) )
			->set_layout( $video_template )
			->set_original_room_name( $room_name )
			->set_as_host();

		// Check Floorplan Status.
		$show_floorplan = Factory::get_instance( VideoHelpers::class )->get_show_floorplan( $user_id, $room_name );
		if ( $show_floorplan ) {
			$myvideoroom_app->enable_floorplan();
		}
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
	 * Function to switch Group Meeting Templates to Room Hosts or Guests
	 * Custom BP logic used for distinguishing hosts.
	 *
	 * @return string
	 */
	public function groupmeet_switch(): string {

		// Adding Listeners for Update.
		Factory::get_instance( UserVideoPreference::class )->check_for_update_request();
		Factory::get_instance( SecurityVideoPreference::class )->check_for_update_request();

		if ( Factory::get_instance( BuddyPressHelpers::class )->bp_can_host_group( get_current_user_id() ) ) {
			return $this->bp_group_video_host();
		} else {
			return $this->bp_group_video_guest();
		}
	}


	/**
	 * Bp_group_video_host.
	 * Provides Group Host Function for Buddypress
	 *
	 * @return string The shortcode output
	 */
	public function bp_group_video_host() {
		global $bp;
		// Escape dependencies.
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
			return 'BuddyPress not active';
		}

		// Shortcode Initialise Hooks/Filters.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();

		// Establish who is host -set group creator as base Group ID for Room - Security Check already done in Switch that calls this function.
		$user_id    = $bp->groups->current_group->creator_id;
		$my_user_id = get_current_user_id();
		$room_name  = $bp->groups->current_group->slug;

		// Checking Permissions of for Host Status of Group.
		if ( ! Factory::get_instance( BuddyPressHelpers::class )->bp_can_host_group( $my_user_id ) ) {
			$this->bp_group_video_guest();
		}

		// Security Engine - blocks room rendering if another setting has blocked it ( eg upgrades, site lockdown, or other feature ).
		$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $user_id, 'bpgrouphost', BuddyPress::MODULE_BUDDYPRESS_ID, BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS );
		if ( $render_block ) {
			return $render_block;
		}

		// Get Room Layout and Reception Settings.
		$video_template = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $user_id, $room_name );
		// Build the Room.
		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'group', $user_id ) )
			->set_layout( $video_template )
			->set_original_room_name( $room_name )
			->set_as_host();

		// Floorplan Status from DB (applies to hosts as well as guests).
		$show_floorplan = Factory::get_instance( VideoHelpers::class )->get_show_floorplan( $user_id, $room_name );
		if ( $show_floorplan ) {
			$myvideoroom_app->enable_floorplan();
		}

		// Construct Shortcode Template - and execute.
		$header        = Factory::get_instance( BuddyPressViews::class )->bp_group_host_template( $user_id );
		$output_object = array();
		$host_status   = true;
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
						$room_name
					)
				)
			);
		array_push( $output_object, $admin_menu );
		return Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $user_id, $room_name, $host_status );

	}

	/**
	 * BP Groups - Guest render.
	 *
	 * @return string Returns the Shortcode call.
	 */
	public function bp_group_video_guest() {
		// Escape dependencies.
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
			return null;
		}
		global $bp;

		// Shortcode Initialise Hooks/Filters.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();

		// Establish who is host -set group creator as base Group ID for Room - Security Check already done in Switch that calls this function.
		$user_id    = $bp->groups->current_group->creator_id;
		$my_user_id = get_current_user_id();

		// Checking Permissions of for Host Status of Group.
		if ( Factory::get_instance( BuddyPressHelpers::class )->bp_can_host_group( $my_user_id ) ) {
			$this->bp_group_video_host();
		}
		// Security Engine - blocks room rendering if another setting has blocked it ( eg upgrades, site lockdown, or other feature ).

		$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $user_id, 'bpgroupguest', BuddyPress::MODULE_BUDDYPRESS_ID, BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS );
		if ( $render_block ) {
			return $render_block;
		}

		// Get Room Layout and Reception Settings.
		$room_name             = $bp->groups->current_group->slug;
		$reception_setting     = Factory::get_instance( VideoHelpers::class )->get_enable_reception_state( $user_id, $room_name );
		$reception_template    = Factory::get_instance( VideoHelpers::class )->get_reception_template( $user_id, $room_name );
		$video_template        = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $user_id, $room_name );
		$video_reception_state = Factory::get_instance( VideoHelpers::class )->get_video_reception_state( $user_id, $room_name );
		$video_reception_url   = Factory::get_instance( VideoHelpers::class )->get_video_reception_url( $user_id, $room_name );

		// Build the Room.
		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'group', $user_id ) )
			->set_original_room_name( $room_name )
			->set_layout( $video_template );

		// Check Floorplan Status.
		$show_floorplan = Factory::get_instance( VideoHelpers::class )->get_show_floorplan( $user_id, $room_name );
		if ( $show_floorplan ) {
			$myvideoroom_app->enable_floorplan();
		}

		// Reception Settings.
		if ( $reception_setting ) {
			$myvideoroom_app->enable_reception()->set_reception_id( $reception_template );

			if ( $video_reception_state ) {

				$myvideoroom_app->set_reception_video_url( $video_reception_url );
			}
		}
		// Construct Shortcode Template - and execute.
		$header        = Factory::get_instance( BuddyPressViews::class )->bp_group_guest_template( $user_id );
		$output_object = array();
		$host_status   = false;
		$host_menu     = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM ),
			'videoroom',
			fn() => \do_shortcode( $myvideoroom_app->output_shortcode_text() ),
			'mvr-video'
		);
		array_push( $output_object, $host_menu );

		//phpcs:ignore --WordPressOutputNotEscaped - the elements are already sanitised.
		echo Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $user_id, $room_name, $host_status );
		return null;
	}
}
