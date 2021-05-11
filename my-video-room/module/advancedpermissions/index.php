<?php
/**
 * Monitor Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Module::register(
			'advancedpermissions',
			__( 'Advanced Permissions', 'myvideoroom' ),
			array(
				__(
					'Updates the main shortcode to allow more granular permissions, allowing permissions to be granted to specific WordPress groups or users on a per shortcode basis.',
					'myvideoroom'
				),
			),
			fn() => new MyVideoRoomPlugin\Module\AdvancedPermissions\Module()
		);
	}
);
