<?php
/**
 * BuddyPress Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\StoredRooms;

use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\StoredRooms\Module as StoredRooms;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			'storedrooms',
			\__( 'Stored Rooms*', 'myvideoroom' ),
			array(
				\__(
					'Saves your room config in the database, allowing you to modify the settings.',
					'myvideoroom'
				),
			),
			fn() => new StoredRooms()
		)
		->add_activation_hook( fn() => ( new Activation() )->activate() )
		->add_uninstall_hook( fn() => ( new Activation() )->uninstall() );
	}
);
