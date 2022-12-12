<?php
/**
 * Monitor Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Monitor;

use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\Monitor\Module as Monitor;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			Reference::MODULE_MONITOR_NAME,
			\esc_html__( 'Room Reception Monitor', 'myvideoroom' ),
			array(
				\esc_html__(
					'Provides support for an automatic room monitoring and reception monitoring engine. Also provides a shortcode to allow monitoring of the number of people in a room. This system Will show browser notifications when users join, and provides a knock on the door sound. The outputted text and format can be customised.',
					'myvideoroom'
				),
			),
			fn() => new Monitor()
		);
	}
);
