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
			\esc_html__( 'Visual Room Designer Tool', 'myvideoroom' ),
			array(
				\esc_html__(
					'Provides a tool for administrators to visually explore the different room and reception options, and generate the correct app shortcodes for hosts and guests to output custom rooms quickly and easily.',
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
