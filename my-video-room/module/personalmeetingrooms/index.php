<?php
/**
 * PersonalMeetingRooms Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/PersonalMeetingRooms
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms;

use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Module as PersonalMeetingRooms;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			'personal-meeting-rooms',
			\esc_html__( 'Personal Meeting Rooms', 'myvideoroom' ),
			array(
				\esc_html__(
					'A personal meeting room is an individually controlled meeting room with its own reception area, room layout selection, privacy, and room permissions. A reception page is automatically configured to handle guest arrival. A WordPress user is the host of their own room, and everyone else is a guest. Users can send invites by email, or by special unique invite code.',
					'myvideoroom'
				),
			),
			fn() => new PersonalMeetingRooms(),
		);
	}
);

