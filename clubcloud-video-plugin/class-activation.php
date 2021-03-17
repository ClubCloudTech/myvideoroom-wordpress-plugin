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

	/**
	 * Activate the plugin
	 * Creates role and caps
	 */
	public static function activate() {
		add_role(
			'clubcloud-video-admin',
			'Video Admin',
			array( 'video-admin' => true )
		);
	}

	/**
	 * Remove the plugin
	 * Remove roles and caps
	 */
	public static function deactivate() {
		global $wp_roles;

		$all_roles = $wp_roles->roles;

		foreach ( $all_roles as $role ) {
			$role->remove_cap( 'video-admin' );
		}
	}
}
