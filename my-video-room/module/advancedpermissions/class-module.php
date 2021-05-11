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
		add_filter(
			'myvideoroom_is_host',
			function ( bool $is_host = null, string $host_string = null ) {
				if ( $host_string && null === $is_host ) {
					$host_types = explode( ';', $host_string );

					$allowed_users  = array();
					$allowed_groups = array();

					foreach ( $host_types as $host_type ) {
						$type_parts = explode( ':', $host_type );

						switch ( $type_parts[0] ) {
							case 'users':
								$allowed_users = explode( ',', $type_parts[1] );
								break;
							case 'groups':
								$allowed_groups = explode( ',', $type_parts[1] );
								break;
						}
					}

					$current_user = wp_get_current_user();

					if ( 0 !== $current_user->ID ) {
						if ( in_array( (string) $current_user->ID, $allowed_users, true ) ) {
							return true;
						}

						if ( in_array( $current_user->user_login, $allowed_users, true ) ) {
							return true;
						}

						if ( count( array_intersect( $current_user->roles, $allowed_groups ) ) > 0 ) {
							return true;
						}
					}

					return false;
				}

				return null;
			},
			0,
			2
		);
	}

}
