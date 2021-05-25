<?php
/**
 * Addon functionality for Filtering Users from Accessing Rooms
 *
 * @package Class PageFilters.
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\UserRoles;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;

/**
 * PageFilters - Security Filter Defaults for Renderblock Function.
 */
class PageFilters {
	/**
	 * Runtime Filters- Provides Execution and Registration of default MVR Room Filters.
	 *
	 * @return void
	 */
	public function runtime_filters() {

	}
	/**
	 * This function Checks a Module is Active to allow it to render Video
	 * Used only in admin pages of plugin
	 *
	 * @param  int $module_id - the Module ID from DB.
	 * @return string|null depending on status.
	 */
	public function block_disabled_module_video_render( int $module_id ) {
		// Check Actions.
		$module_block = do_action( 'myvideoroom_security_block_disabled_module', $module_id );
		if ( $module_block ) {
			return $module_block;
		}

		// Normal Check.
		$is_module_enabled = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( $module_id );

		if ( ! $is_module_enabled ) {
			return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_site();
		}
	}

	/**
	 * This function Checks a Module is Active to allow it to render Video
	 * Used only in admin pages of plugin
	 *
	 * @param  int    $user_id      UserID.
	 * @param  string $room_name    The room name.
	 * @param  bool   $host_status  If used.
	 *
	 * @return null|string depending.
	 */
	public function block_disabled_room_video_render( int $user_id, string $room_name, bool $host_status ) {
		// Check Module Override State.
		$permissions   = Factory::get_instance( SecurityVideoPreferenceDAO::class )->check_security_settings( $user_id, $room_name );
		$site_override = $permissions['sitedefault']->site_override_enabled;
		// Override Control Check.
		if ( $site_override && true === $permissions['sitedefault']->room_disabled ) {
			return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id );
		}

		// Site Default Settings Section.
		$does_room_record_exist = $permissions['user']->room_name;
		if ( ! $does_room_record_exist ) {
			$is_room_disabled = $permissions['sitedefault']->room_disabled;
		} else {
			$is_room_disabled = $permissions['user']->room_disabled;
		}

		// Is Disable setting active ?
		if ( $is_room_disabled ) {
			if ( $host_status ) {
				// If user is a host return their control panel.
				return Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
					$user_id,
					$room_name
				);
			} else {
				// For guests return the blocked template.
				return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id );
			}
		}
	}

	/**
	 * This function Checks The Disable Anonymous Setting is/not on - and enforces result
	 * Used by all rooms
	 *
	 * @param  int    $user_id      UserID.
	 * @param  string $room_name    The room name.
	 *
	 * @return null|string depending.
	 */
	public function block_anonymous_room_video_render( int $user_id, string $room_name ): ?string {
		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

		// Override Control Check.
		if ( $site_override ) {
			$user_id   = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		}

		// Site Default Settings Section.
		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $user_id, $room_name, 'room_name' );

		if ( ! $does_room_record_exist ) {
			$is_room_disabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'anonymous_enabled' );
		} else {
			$is_room_disabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		}

		// If the restrict room setting is enabled fire the block.
		if ( $is_room_disabled ) {
			return Factory::get_instance( SecurityTemplates::class )->anonymous_blocked_by_user( $user_id );
		}

		return null;
	}

	/**
	 * This function Checks The Role Based Configuration Settings of a Given Room- and Returns Host or Guest Status for the Room.
	 * Used by all rooms
	 *
	 * @param  int $owner_id UserID of owner.
	 *
	 * @return string - host or guest status.
	 */
	public function allowed_roles_host( int $owner_id ): string {
		$room_name = '';

		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $owner_id );
		if ( $room_object ) {
			$room_name = $room_object->room_name . Security::MULTI_ROOM_HOST_SUFFIX;
		}

		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

		// Override Control Check.
		if ( $site_override ) {
			$owner_id  = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		}

		// Site Default Settings Section.
		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $owner_id, $room_name, 'room_name' );

		if ( ! $does_room_record_exist ) {
			$room_control_enabled_state = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'allow_role_control_enabled' );
		} else {
			$room_control_enabled_state = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $owner_id, $room_name, 'allow_role_control_enabled' );
		}

		// Handling Anonymous Users.
		$anonymous_hosts_allowed = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $owner_id, $room_name, 'anonymous_enabled' );

		if ( $anonymous_hosts_allowed && ! \is_user_logged_in() ) {
			return true;
		} elseif ( ! $anonymous_hosts_allowed && ! is_user_logged_in() ) {
			return false;
		}

		// Exit Filter if calling from Host Functions ( that pass host in as variable ) or if Module is Disabled.
		if ( ! $room_control_enabled_state ) {
			$host = current_user_can( Plugin::CAP_GLOBAL_HOST );
			if ( $host ) {
				return true;
			} else {
				return false;
			}
		}

		// Decide whether to allow or block.
		// Site Default Settings Section.
		if ( ! $does_room_record_exist ) {
			$allow_to_block_switch = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'block_role_control_enabled' );
		} else {
			$allow_to_block_switch = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( $owner_id, $room_name, 'block_role_control_enabled' );
		}

		// Reject Anonymous Users ( as have no role ).
		if ( ! is_user_logged_in() && ! $allow_to_block_switch ) {
			return false;
		}
		// Get List of Allowed/Blocked Roles from DB.

		if ( ! $does_room_record_exist ) {
			$preference = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				Security::PERMISSIONS_TABLE
			);
		} else {
			$preference = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id(
				$owner_id,
				$room_name
			);
		}

		$allowed_db_roles_configuration = array();
		if ( $preference ) {
			$allowed_db_roles_configuration = $preference->get_allowed_roles_array();
		}

		if ( ( ! $allowed_db_roles_configuration ) && ( ! $allow_to_block_switch ) ) {
			return false;
		}

		// Retrieve Users Roles.
		global $wp_roles;
		$user_roles = Factory::get_instance( UserRoles::class )->get_user_roles();

		// Retrieve Allowed/Blocked Roles.
		// User Roles May be multiple ( so check each role ).
		$role_match = false;
		foreach ( $user_roles as $user_role ) {
			$role_name = translate_user_role( $wp_roles->roles[ $user_role ]['name'] );
			foreach ( $allowed_db_roles_configuration as $db_role ) {
				if ( $db_role === $role_name ) {
					$role_match = true;
				}
			}
		}// End per role Check.

		// Fire Block if Flag to block is on.
		if ( ! $role_match && $allow_to_block_switch ) {
			return true;
		} elseif ( ! $role_match && ! $allow_to_block_switch ) {
			return false;
		} elseif ( $role_match && ! $allow_to_block_switch ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * * This function Checks The Role Based Configuration Settings - and enforces result
	 * Used by all rooms
	 *
	 * @param  int    $owner_id     UserID of owner.
	 * @param  string $room_name    The room name.
	 * @param  string $host_status  If used.
	 * @param  string $room_type    Class of room.
	 *
	 * @return null|string depending.
	 */
	public function allowed_roles_room_video_render( int $owner_id, string $room_name, string $host_status, string $room_type = null ) {
		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

		// Override Control Check.
		if ( $site_override ) {
			$owner_id  = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		}
		// Site Default Settings Section.

		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $owner_id, $room_name, 'room_name' );

		if ( ! $does_room_record_exist ) {
			$room_control_enabled_state = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'allow_role_control_enabled' );
		} else {
			$room_control_enabled_state = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( $owner_id, $room_name, 'allow_role_control_enabled' );
		}

		// Exit Filter if calling from Host Functions ( that pass host in as variable ) or if Module is Disabled.
		if ( ! $room_control_enabled_state || ( $host_status ) ) {
			return null;
		}

		// Decide whether to allow or block.
		// Site Default Settings Section.
		if ( ! $does_room_record_exist ) {
			$allow_to_block_switch = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'block_role_control_enabled' );
		} else {
			$allow_to_block_switch = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( $owner_id, $room_name, 'block_role_control_enabled' );
		}

		// Reject Anonymous Users ( as have no role ).
		if ( ! is_user_logged_in() && ! $allow_to_block_switch ) {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
		}

		if ( ! $does_room_record_exist ) {
			$preference = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				Security::PERMISSIONS_TABLE
			);
		} else {
			$preference = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id(
				$owner_id,
				$room_name
			);
		}

		$allowed_db_roles_configuration = array();
		if ( $preference ) {
			$allowed_db_roles_configuration = $preference->get_allowed_roles_array();
		}

		if ( ( ! $allowed_db_roles_configuration ) && ( ! $allow_to_block_switch ) ) {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
		}

		// Retrieve Users Roles.
		global $wp_roles;
		$user_roles = Factory::get_instance( UserRoles::class )->get_user_roles();

		// Retrieve Allowed/Blocked Roles.
		// User Roles May be multiple ( so check each role ).
		$role_match = false;

		foreach ( $user_roles as $user_role ) {
			$role_name = translate_user_role( $wp_roles->roles[ $user_role ]['name'] );
			foreach ( $allowed_db_roles_configuration as $db_role ) {
				// transform user role to Display format.

				if ( $db_role === $role_name ) {
					$role_match = true;
				}
			}
		}// End per role Check.

		// Fire Block if Flag to block is on.
		if ( ! $role_match && $allow_to_block_switch ) {
			return null;
		} elseif ( ! $role_match && ! $allow_to_block_switch ) {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
		} elseif ( $role_match && ! $allow_to_block_switch ) {
			return null;
		} else {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
		}
	}
}
