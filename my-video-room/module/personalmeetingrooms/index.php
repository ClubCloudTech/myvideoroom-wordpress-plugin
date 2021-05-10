<?php
/**
 * PersonalMeetingRooms Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/PersonalMeetingRooms
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Module::register(
			'personal-meeting-rooms',
			__( 'Personal Meeting Rooms', 'myvideoroom' ),
			array(
				__(
					'A personal meeting room is an individually controlled meeting room with its own reception area, room layout selection, privacy, and room permissions. A reception page is automatically configured to handle guest arrival. A WordPress user is the host of their own room, and everyone else is a guest. Users can send invites by email, or by special unique invite code.',
					'myvideoroom'
				),
			),
		);
	}
);

