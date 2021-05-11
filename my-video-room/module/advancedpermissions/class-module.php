<?php
/**
 * The entry point for the AdvancedPermissions module
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\AdvancedPermissions;

/**
 * Class Module
 */
class Module {

	/**
	 * MonitorShortcode constructor.
	 */
	public function __construct() {
		add_filter( 'myvideoroom_is_host', array( $this, 'is_host' ), 0, 2 );

		new RoomBuilder();
	}

	/**
	 * Is the current user a host, based on the the string passed to the shortcode, and the current users id and groups
	 *
	 * @param ?bool   $is_host      If the host already determined.
	 * @param ?string $host_string  The host string to parse by this function.
	 *
	 * @return ?bool
	 */
	public function is_host( bool $is_host = null, string $host_string = null ): ?bool {
		if ( $host_string && null === $is_host ) {
			$host_types = explode( ';', $host_string );

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
				return true;
			}

			return false;
		}

		return null;
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

		return in_array( (string) $current_user->ID, $host_users, true ) || in_array( $current_user->user_login, $host_users, true );
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
