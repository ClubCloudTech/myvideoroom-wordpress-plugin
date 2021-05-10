<?php
/**
 * Monitor Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Factory::get_instance( Module::class )
			->register(
				'monitor',
				'Monitor',
				array(
					__(
						'Adds a WordPress shortcode to allow monitoring of the number of people in a room. Will show browser notifications when users join. The outputted text and format can be customised and translated.',
						'myvideoroom'
					),
				),
				fn() => new MyVideoRoomPlugin\Module\Monitor\Module()
			);
	}
);
