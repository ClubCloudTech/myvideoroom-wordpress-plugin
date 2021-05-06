<?php
/**
 * Installs and uninstalls the plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

/**
 * Class Activation
 */
class Activation {

	const ROLE_GLOBAL_HOST = 'myvideoroom_video_host';

	/**
	 * Activate the plugin
	 * Creates role and caps
	 */
	public static function activate() {
		add_role(
			self::ROLE_GLOBAL_HOST,
			esc_html__( 'Video Default Host', 'myvideoroom' ),
			array( Plugin::CAP_GLOBAL_HOST => true )
		);

		global $wp_roles;
		$default_admins = array( 'administrator' );

		foreach ( $default_admins as $role ) {
			$wp_roles->add_cap( $role, Plugin::CAP_GLOBAL_HOST );
		}
	}

	/**
	 * Remove the plugin
	 * Remove roles and caps
	 */
	public static function deactivate() {
		global $wp_roles;

		foreach ( array_keys( $wp_roles->roles ) as $role ) {
			$wp_roles->remove_cap( $role, Plugin::CAP_GLOBAL_HOST );
		}

		remove_role( self::ROLE_GLOBAL_HOST );
	}
}
