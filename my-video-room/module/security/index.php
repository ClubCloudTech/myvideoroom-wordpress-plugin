<?php
/**
 * Security Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/Security
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Module::register(
			'advancedpermissions',
			'Advanced Room Permissions',
			array(
				esc_html__(
					'MyVideoRoom includes a Security and Permissions module which allows users, to precisely control the type of permissions they would like for their room. For example users can select logged in users, specific site roles, disable rooms entirely, or work in conjunction with other modules (like groups and friends in Buddypress). The module also provides central enforcement and override capability which allows central control of specific room settings, and configuration.',
					'myvideoroom'
				),
			),
			fn() => Factory::get_instance( Security::class )->init()
		)
		->add_activation_hook(
			fn () => Factory::get_instance( Security::class )->activate_module()
		)
		->add_admin_page_hook(
			fn () => Factory::get_instance( Security::class )->render_security_admin_page()
		)
		->add_deactivation_hook(
			fn () => Factory::get_instance( Security::class )->de_activate_module()
		);

		/**
		 * Example of adding hooks
		 * ->add_compatibility_hook( fn () => true )
		 */
	}
);
