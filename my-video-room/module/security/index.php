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
			Security::MODULE_SECURITY_NAME,
			\esc_html__( 'Room Permissions and Hosts', 'myvideoroom' ),
			array(
				esc_html__(
					'The Room Permissions and Hosts module allows users, to precisely control the type of access permissions they would like for their room. For example room owners can select anonymous/logged in users, specific site roles, disable rooms entirely, or work in conjunction with other modules (like groups and friends in Buddypress). The module also provides central enforcement and override capability which allows central control of specific room settings, availability and configuration.',
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
