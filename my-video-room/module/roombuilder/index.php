<?php
/**
 * Monitor Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder;

use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\RoomBuilder\Module as RoomBuilder;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			'roombuilder',
			\esc_html__( 'Room Builder', 'myvideoroom' ),
			array(
				\esc_html__(
					'A tool to explore the different options provided by MyVideoRoom, and to generate the correct app shortcode to output the room.',
					'myvideoroom'
				),
			),
			fn() => new RoomBuilder()
		);

		/**
		 * Example of adding hooks
		 * ->add_compatibility_hook( fn () => true )
		 * ->add_admin_page_hook( fn () => 'The room builder was successfully activated' )
		 * ->add_activation_hook( fn () => false )
		 * ->add_deactivation_hook( fn () => false );
		 */
	}
);
