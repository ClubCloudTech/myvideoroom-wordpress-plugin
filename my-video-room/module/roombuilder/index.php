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
				'roombuilder',
				'Room Builder',
				array(
					'A tool to explore the different options provided by MyVideoRoom, and to generate the correct app shortcode to output the room.',
				),
				fn() => new MyVideoRoomPlugin\Module\RoomBuilder\Module()
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
