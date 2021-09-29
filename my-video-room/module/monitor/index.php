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
			'monitor',
			\esc_html__( 'Monitor', 'myvideoroom' ),
			array(
				\esc_html__(
					'Adds a WordPress shortcode to allow monitoring of the number of people in a room. Will show browser notifications when users join. The outputted text and format can be customised and translated.',
					'myvideoroom'
				),
			),
			fn() => new Monitor()
		);
	}
);
