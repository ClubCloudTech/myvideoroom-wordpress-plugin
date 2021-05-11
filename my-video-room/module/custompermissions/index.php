<?php
/**
 * Monitor Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\CustomPermissions;

use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\CustomPermissions\Module as CustomPermissions;

\add_action(
	'myvideoroom_init',
	function () {
		Module::register(
			'custompermissions',
			\esc_html__( 'Custom Permissions', 'myvideoroom' ),
			array(
				\esc_html__(
					'Updates the main shortcode to allow more granular permissions, allowing permissions to be granted to specific WordPress groups or users on a per shortcode basis.',
					'myvideoroom'
				),
			),
			fn() => new CustomPermissions()
		);
	}
);
