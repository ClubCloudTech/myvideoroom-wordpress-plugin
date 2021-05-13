<?php
/**
 * The entry point for the CustomPermissions module
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\CustomPermissions;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

/**
 * Class Module
 */
class Module {

	/**
	 * MonitorShortcode constructor.
	 */
	public function __construct() {
		add_filter( 'myvideoroom_shortcode_constructor', array( $this, 'modify_shortcode_constructor' ), 0, 2 );

		$roombuilder_is_active = Factory::get_instance( \MyVideoRoomPlugin\Library\Module::class )
										->is_module_active( 'roombuilder' );

		if ( $roombuilder_is_active ) {
			new RoomBuilder();
		}
	}

	/**
	 * Is the current user a host, based on the the string passed to the shortcode, and the current users id and groups
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function modify_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor ): AppShortcodeConstructor {
		$host = $shortcode_constructor->get_custom_string_param( 'host' );

		if (
			is_string( $host ) &&
			(
				strpos( $host, 'users:' ) === 0 ||
				strpos( $host, 'roles:' ) === 0
			)
		) {
			$host_types = explode( ';', $host );

			$host_users  = array();
			$host_groups = array();

			foreach ( $host_types as $host_type ) {
				$type_parts = explode( ':', $host_type );

				switch ( $type_parts[0] ) {
					case 'users':
						$host_users = explode( ',', $type_parts[1] );
						break;
					case 'roles':
						$host_groups = explode( ',', $type_parts[1] );
						break;
				}
			}

			$current_user = wp_get_current_user();

			if (
				0 !== $current_user->ID &&
				( $this->user_is_host( $host_users ) || $this->role_is_host( $host_groups ) )
			) {
				$shortcode_constructor->set_as_host();
			} else {
				$shortcode_constructor->set_as_guest();
			}
		}

		return $shortcode_constructor;
	}

	/**
	 * Is the current user a host
	 *
	 * @param array $host_users A list of ids or logins that are hosts.
	 *
	 * @return bool
	 */
	private function user_is_host( array $host_users ): bool {
		$current_user = wp_get_current_user();

		return in_array( (string) $current_user->ID, $host_users, true ) || in_array( $current_user->user_nicename, $host_users, true );
	}

	/**
	 * Does the current user belong to a group that is a host group
	 *
	 * @param array $host_groups A list of groups that are hosts.
	 *
	 * @return bool
	 */
	private function role_is_host( array $host_groups ): bool {
		$current_user = wp_get_current_user();

		return count( array_intersect( $current_user->roles, $host_groups ) ) > 0;
	}

}
