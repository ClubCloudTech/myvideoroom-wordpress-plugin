<?php
/**
 * BuddyPress Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\BuddyPress;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\BuddyPress\Module as BuddyPress;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress as BuddyPressClass;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			'buddypress',
			\esc_html__( 'BuddyPress Integration Pack', 'myvideoroom' ),
			array(
				\esc_html__(
					'Integrates to BuddyPress users and groups- adding video rooms to the BuddyPress user profile pages and to group pages. Users get their own personal video room rendered in the their BuddyPress Profile page as a separate video meeting tab, and are given control of their own video room settings and permissions - including whether to show the room to non-friends. Guests viewing a user profile in BuddyPress can enter a video room straight from the user’s profile page. Owners and moderators of BuddyPress groups can enable or disable the video room for the group, as well as control their layouts, templates, room permissions and reception settings, including creating members only groups.',
					'myvideoroom'
				),
			),
			fn() => Factory::get_instance( BuddyPressClass::class )->init()
		)->add_activation_hook(
			fn () => Factory::get_instance( BuddyPressClass::class )->activate_module()
		)->add_admin_page_hook(
			fn() => Factory::get_instance( BuddyPressClass::class )->render_buddypress_admin_page()
		);
	}
);
/*
->add_compatibility_hook(
			fn() => Factory::get_instance( Module::class )->is_module_active( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME )*/
