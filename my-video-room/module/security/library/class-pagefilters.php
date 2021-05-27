<?php
/**
 * Addon functionality for Filtering Users from Accessing Rooms
 *
 * @package Class PageFilters.
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\UserRoles;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference as SecurityVideoPreferenceShortcode;

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
	public function block_disabled_module_video_render( int $module_id ): ?string {
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
		return null;
	}

	/**
	 * This function Checks a Module is Active to allow it to render Video
	 * Used only in admin pages of plugin
	 *
	 * @param  int                     $user_id      UserID.
	 * @param  string                  $room_name    The room name.
	 * @param  bool                    $host_status  If used.
	 * @param  SecurityVideoPreference $user_permissions - Object with user Permissions.
	 * @param  SecurityVideoPreference $site_override_permissions - Object with Site Enforcement settings.
	 * @param  SecurityVideoPreference $security_default_permissions - Object with Default (no user preference yet applied) settings.
	 *
	 * @return ?string depending.
	 */
	public function block_disabled_room_video_render( int $user_id, string $room_name, bool $host_status, SecurityVideoPreference $user_permissions = null, SecurityVideoPreference $site_override_permissions = null, SecurityVideoPreference $security_default_permissions = null ): ?string {
		// Site Default Settings Flag.
		if ( $user_permissions && $user_permissions->get_room_name() ) {
			$does_room_record_exist = $user_permissions->get_room_name();
		} else {
			$does_room_record_exist = false;
		}

		if ( ! $does_room_record_exist ) {
			$room_disabled = $security_default_permissions->is_room_disabled();

		} else {
			$room_disabled = $user_permissions->is_room_disabled();
		}

		if ( $site_override_permissions && $site_override_permissions->is_site_override_enabled() ) {
			$room_disabled = $site_override_permissions->is_room_disabled();
		}

		// Blocking Return Content Decision.
		if ( $room_disabled ) {
			if ( $host_status ) {
				// If user is a host return their control panel.
				return Factory::get_instance( SecurityVideoPreferenceShortcode::class )->choose_settings(
					$user_id,
					$room_name
				);

			} else {
				// For guests return the blocked template.
				return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id );
			}
		}
		return null;
	}

	/**
	 * This function Checks The Disable Anonymous Setting is/not on - and enforces result
	 * Used by all rooms
	 *
	 * @param  int                     $user_id - the UserID.
	 * @param  SecurityVideoPreference $user_permissions - Object with user Permissions.
	 * @param  SecurityVideoPreference $site_override_permissions - Object with Site Enforcement settings.
	 * @param  SecurityVideoPreference $security_default_permissions - Object with Default (no user preference yet applied) settings.
	 *
	 * @return null|string depending.
	 */
	public function block_anonymous_room_video_render( int $user_id, SecurityVideoPreference $user_permissions = null, SecurityVideoPreference $site_override_permissions = null, SecurityVideoPreference $security_default_permissions = null ): ?string {
		if ( ! $user_permissions && ! $site_override_permissions && ! $security_default_permissions ) {
			return null;
		}
		// Site Default Settings Flag.

		if ( $user_permissions && $user_permissions->get_room_name() ) {
			$does_room_record_exist = $user_permissions->get_room_name();
		} else {
			$does_room_record_exist = false;
		}

		if ( ! $does_room_record_exist ) {
			$anonymous_setting = $security_default_permissions->is_anonymous_enabled();

		} else {
			$anonymous_setting = $user_permissions->is_anonymous_enabled();
		}

		if ( $site_override_permissions->is_site_override_enabled() ) {
			$anonymous_setting = $site_override_permissions->is_anonymous_enabled();
		}

		// If the restrict room setting is enabled fire the block.
		if ( $anonymous_setting ) {
			return Factory::get_instance( SecurityTemplates::class )->anonymous_blocked_by_user( $user_id );
		}

		return null;
	}

	/**
	 * Is the current user a host or a guest
	 *
	 * @param bool $default_value The default setting for the user a host or guest.
	 * @param int  $owner_id      UserID of owner.
	 *
	 * @return bool
	 */
	public function current_user_is_host( bool $default_value, int $owner_id ): bool {
		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $owner_id );

		if ( ! $room_object ) {
			return $default_value;
		}

		$room_name = $room_object->room_name . Security::MULTI_ROOM_HOST_SUFFIX;

		$room_custom_permissions = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id(
			$owner_id,
			$room_name
		);

		if ( ! $room_custom_permissions ) {
			return $default_value;
		}

		if ( ! \is_user_logged_in() ) {
			return $room_custom_permissions->is_anonymous_enabled();
		}

		$target_roles          = $room_custom_permissions->get_roles();
		$target_roles_are_host = $room_custom_permissions->is_block_role_control_enabled();

		if ( ! $target_roles && $target_roles_are_host ) {
			return true;
		}

		$roles_intersect = $this->do_roles_intersect(
			Factory::get_instance( UserRoles::class )->get_user_roles(),
			$target_roles
		);

		if ( $target_roles_are_host ) {
			return $roles_intersect;
		} else {
			return ! $roles_intersect;
		}
	}

	/**
	 * Do the roles intersect?
	 *
	 * @param array    $user_roles   The list of WordPress user roles.
	 * @param string[] $target_roles An array of role slugs.
	 *
	 * @return bool
	 */
	private function do_roles_intersect( array $user_roles, array $target_roles ): bool {
		global $wp_roles;

		$roles_intersect = false;

		foreach ( $user_roles as $user_role ) {
			$role_name = translate_user_role( $wp_roles->roles[ $user_role ]['name'] );
			foreach ( $target_roles as $db_role ) {
				if ( $db_role === $role_name ) {
					$roles_intersect = true;
				}
			}
		}

		return $roles_intersect;
	}

	/**
	 * * This function Checks The Role Based Configuration Settings - and enforces result
	 * Used by all rooms
	 *
	 * @param  int     $owner_id     UserID of owner.
	 * @param  string  $room_name    Name of Room to filter.
	 * @param  string  $host_status  If used.
	 * @param  ?string $room_type    Class of room.
	 *
	 * @return ?string depending.
	 */
	public function allowed_roles_room_video_render( int $owner_id, string $room_name, string $host_status, string $room_type = null ): ?string {
		// Exit if Host.
		if ( $host_status ) {
			return null;
		}
		// Get Permissions Objects.
		$site_override_permissions = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT );
		$preference                = null;

		// Force Override if Set.
		if ( $site_override_permissions && $site_override_permissions->is_site_override_enabled() ) {
			$room_control_enabled_state = $site_override_permissions->is_allow_role_control_enabled();
			$anonymous_hosts_blocked    = $site_override_permissions->is_anonymous_enabled();
			$allow_to_block_switch      = $site_override_permissions->is_block_role_control_enabled();
			$preference                 = $site_override_permissions;
		}

		// Try User Permissions if set.
		if ( ! $preference ) {
			$user_permissions = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id( $owner_id, $room_name );
			if ( $user_permissions ) {
				$room_control_enabled_state = $user_permissions->is_allow_role_control_enabled();
				$anonymous_hosts_blocked    = $user_permissions->is_anonymous_enabled();
				$allow_to_block_switch      = $user_permissions->is_block_role_control_enabled();
				$preference                 = $user_permissions;
			}
		}

		// Try Default Security Permissions if Set.
		if ( ! $preference ) {
			$security_default_permissions = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE );
			if ( $security_default_permissions ) {
				$room_control_enabled_state = $security_default_permissions->is_allow_role_control_enabled();
				$anonymous_hosts_blocked    = $security_default_permissions->is_anonymous_enabled();
				$allow_to_block_switch      = $security_default_permissions->is_block_role_control_enabled();
				$preference                 = $security_default_permissions;
			}
		}

		// If there is no setting exit.
		if ( ! $preference ) {
			return null;
		}

		// Handling Anonymous Users.

		if ( true === $anonymous_hosts_blocked && ! is_user_logged_in() ) {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
		}
		if ( ! is_user_logged_in() && false === $allow_to_block_switch && true === $room_control_enabled_state ) {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
		}

		// Exit if No Room Control is Enabled.
		if ( false === $room_control_enabled_state ) {
			return null;
		}
		$no_allowed_roles_configuration = false;
		if ( $preference ) {
			$allowed_db_roles_configuration = $preference->get_roles();
			$no_allowed_roles_configuration = false;
		} else {
			$allowed_db_roles_configuration = array();
			$no_allowed_roles_configuration = true;
		}

		if ( ( true === $no_allowed_roles_configuration ) && ( false === $allow_to_block_switch ) ) {
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

		} elseif ( ! $role_match && false === $allow_to_block_switch ) {
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );

		} elseif ( $role_match && false === $allow_to_block_switch ) {
			return null;
		}

		return Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
	}
}
