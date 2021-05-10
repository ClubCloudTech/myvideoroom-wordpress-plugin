<?php
/**
 * BuddyPress Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/BuddyPress
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Factory::get_instance( Module::class )
			->register(
				'buddypress',
				__( 'BuddyPress', 'myvideoroom' ),
				array(
					__(
						'Integrates BuddyPress and MyVideoRoom - adding video rooms to the BuddyPress user profile pages and to group pages. Users get their own personal video room rendered in the their BuddyPress Profile page as a separate video meeting tab, and are given control of their own video room settings and permissions - including whether to show the room to non-friends. Guests viewing a user profile in BuddyPress can enter a video room straight from the user’s profile page. Owners and moderators of BuddyPress groups can enable or disable the video room for the group, as well as control their layouts, templates, room permissions and reception settings, including creating members only groups.',
						'myvideoroom'
					),
				),
			);
	}
);
