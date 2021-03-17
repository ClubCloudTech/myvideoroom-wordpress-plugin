<?php
/**
 * Installs and uninstalls the plugin
 *
 * @package ClubCloudVideoPlugin\Admin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

/**
 * Class Activation
 */
class Activation {

	const ROLE_GLOBAL_ADMIN = 'clubcloud_video_admin';

	/**
	 * Activate the plugin
	 * Creates role and caps
	 */
	public static function activate() {
		add_role(
			self::ROLE_GLOBAL_ADMIN,
			__( 'Video Admin', 'clubcloud-video-admin-role' ),
			array( Plugin::CAP_GLOBAL_ADMIN => true )
		);
	}

	/**
	 * Remove the plugin
	 * Remove roles and caps
	 */
	public static function deactivate() {
		global $wp_roles;

		foreach ( array_keys( $wp_roles->roles ) as $role ) {
			$wp_roles->remove_cap( $role, Plugin::CAP_GLOBAL_ADMIN );
		}

		remove_role( self::ROLE_GLOBAL_ADMIN );
	}
}
