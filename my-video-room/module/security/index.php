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
use MyVideoRoomPlugin\Plugin;

add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			Security::MODULE_SECURITY_NAME,
			\esc_html__( 'Room Security and Host Control Pack', 'myvideoroom' ),
			array(
				esc_html__(
					'The Room Security and Host Control Pack allows users to precisely control the type of access permissions they would like for their room. For example room owners can select anonymous/logged in users, specific site roles, disable rooms entirely, or work in conjunction with other modules (like groups and friends in Buddypress).',
					'myvideoroom'
				),
				esc_html__(
					'The module also provides central enforcement and override capability of room permissions which allows central control of specific room settings, availability and configuration.',
					'myvideoroom'
				),
			),
			fn() => Factory::get_instance( Security::class )->init()
		)
			->add_activation_hook(
				fn() => Factory::get_instance( Security::class )->activate_module()
			)
			->add_admin_page_hook(
				fn() => Factory::get_instance( Security::class )->render_security_admin_page()
			)
			->add_deactivation_hook(
				fn() => Factory::get_instance( Security::class )->de_activate_module()
			);
	}
);
