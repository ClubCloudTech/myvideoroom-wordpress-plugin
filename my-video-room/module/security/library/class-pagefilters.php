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
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Library\UserRoles;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;

/**
 * PageFilters - Security Filter Defaults for Renderblock Function.
 */
class PageFilters extends Shortcode {
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
		// Check BuddyPress as both itself and Personal Video Scenarios.

		if (
			Factory::get_instance( ModuleConfig::class )->read_enabled_status( Dependencies::MODULE_BUDDYPRESS_ID ) &&
			Factory::get_instance( Dependencies::class )->is_buddypress_active()
		) {
			if ( Dependencies::MODULE_BUDDYPRESS_ID === $module_id ) {
				$is_module_enabled = Factory::get_instance( ModuleConfig::class )->read_enabled_status( $module_id );

				if ( ! $is_module_enabled ) {
					return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_site();
				} else {
					$module_id = Dependencies::MODULE_PERSONAL_MEETING_ID;
				}
			}
			// Normal Check.

			$is_module_enabled = Factory::get_instance( ModuleConfig::class )->read_enabled_status( $module_id );
			if ( ! $is_module_enabled ) {
				return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_site();
			}
		}
	}
	/**
	 * This function Checks a Module is Active to allow it to render Video
	 * Used only in admin pages of plugin
	 *
	 * @param  int    $user_id - userID.
	 * @param  string $room_name - the room name.
	 * @param  string $host_status - if used.
	 * @param  string $room_type - class of room.
	 * @return null|string depending.
	 */
	public function block_disabled_room_video_render( int $user_id, string $room_name, $host_status, $room_type = null ) {
		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
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
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'room_disabled' );

		} else {
			$is_room_disabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $user_id, $room_name, 'room_disabled' );
		}

		// Is Disable setting active ?
		if ( $is_room_disabled ) {
			if ( $host_status ) {
				// If user is a host return their control panel.
				$permissions_page = Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
					$user_id,
					$room_name
				);
				return $permissions_page;
			} else {
				// For guests return the blocked template.
				$blocked_display = Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id, $room_type );
				return $blocked_display;
			}
		}
	}
	/**
	 * This function Checks The Disable Anonymous Setting is/not on - and enforces result
	 * Used by all rooms
	 *
	 * @param  int    $user_id - userID.
	 * @param  string $room_name - the room name.
	 * @param  string $host_status - if used.
	 * @param  string $room_type - class of room.
	 * @return null|string depending.
	 */
	public function block_anonymous_room_video_render( int $user_id, string $room_name, $host_status, $room_type = null ) {
		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
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
			$blocked_display = Factory::get_instance( SecurityTemplates::class )->anonymous_blocked_by_user( $user_id, $room_type );
		}
		return $blocked_display;
	}

	/**
	 * * This function Checks The Role Based Configuration Settings of a Given Room- and Returns Host or Guest Status for the Room.
	 * Used by all rooms
	 *
	 * @param  int $owner_id - userID of owner.
	 * @return string - host or guest status.
	 */
	public function allowed_roles_host( int $owner_id ): string {
		$room_name = '';

		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $owner_id );
		if ( $room_object ) {
			$room_name = $room_object->room_name . Security::MULTI_ROOM_HOST_SUFFIX;
		}

		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

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

			$allowed_db_roles_configuration = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_db_wordpress_roles( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'allowed_roles' );

		} else {
			$allowed_db_roles_configuration = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_db_wordpress_roles( $owner_id, $room_name, 'allowed_roles' );
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
	 * @param  int    $owner_id - userID of owner.
	 * @param  string $room_name - the room name.
	 * @param  string $host_status - if used.
	 * @param  string $room_type - class of room.
	 * @return null|string depending.
	 */
	public function allowed_roles_room_video_render( int $owner_id, string $room_name, $host_status, $room_type = null ) {
		// Check Module Override State.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

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
		// Get List of Allowed/Blocked Roles from DB.
		if ( ! $does_room_record_exist ) {

			$allowed_db_roles_configuration = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_db_wordpress_roles( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'allowed_roles' );

		} else {
			$allowed_db_roles_configuration = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_db_wordpress_roles( $owner_id, $room_name, 'allowed_roles' );
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
			$blocked_display = Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
			return $blocked_display;
		} elseif ( $role_match && ! $allow_to_block_switch ) {
			return null;
		} else {
			$blocked_display = Factory::get_instance( SecurityTemplates::class )->blocked_by_role_template( $owner_id, $room_type );
			return $blocked_display;
		}
	}
	/**
	 * This function Checks The Group Membership setting ( allow only group members ) of BuddyPress Groups - and enforces result
	 * Used by Only BuddyPress Groups
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name - name of room.
	 * @param  string $room_type - type of room for inspection.
	 * @return String  Template if Blocked - null if allowed.
	 */
	public function block_bp_non_group_member_video_render( int $user_id, string $room_name, $room_type = null ) {
		// Exit for Non Groups.
		if (
			! Factory::get_instance( Dependencies::class )->is_buddypress_active() ||
			! Factory::get_instance( ModuleConfig::class )->read_enabled_status( Dependencies::MODULE_BUDDYPRESS_ID ) ||
			! bp_is_active( 'groups' ) ||
			! bp_is_groups_component()
		) {
			return null;
		}
		// Check Settings.
		global $bp;
		$creator_id = $bp->groups->current_group->creator_id;
		// Check Site Override Status.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		if ( $site_override ) {
			$creator_id = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name  = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		}
		// Site Default Settings Section.
		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
		->read_security_settings( $creator_id, $room_name, 'room_name' );
		if ( ! $does_room_record_exist ) {

			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'restrict_group_to_members_enabled' );

		} else {
			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $creator_id, $room_name, 'restrict_group_to_members_enabled' );
		}

		// Exit early if no setting for filter.
		if ( ! $room_access_setting ) {
			return null;
		}
		// Get Global Information on Group.

		$group_id          = $bp->groups->current_group->id;
		$user_id           = get_current_user_id();
		$is_user_member    = Factory::get_instance( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::class )->bp_is_user_member( $group_id, $user_id );
		$is_user_moderator = Factory::get_instance( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::class )->bp_is_user_moderator( $group_id, $user_id );
		$is_user_admin     = Factory::get_instance( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::class )->bp_is_user_admin( $group_id, $user_id );
		switch ( $room_access_setting ) {
			case 'Administrators':
				if ( $is_user_admin ) {
					return null;
				}
				// Check for all Roles starting with Admin - and Fall through.
			case 'Moderators':
				if ( $is_user_admin || $is_user_moderator ) {
					return null;
				}
				// Check for all Roles - and Fall through.
			case 'Members':
				if ( $is_user_admin || $is_user_moderator || $is_user_member ) {
					return null;
				}
				// Else Fire the Block.
		}
			return Factory::get_instance( SecurityTemplates::class )->blocked_by_group_membership( $creator_id, $room_type );
	}
	/**
	 * This function Checks The Friends Membership setting of BuddyPress Friends- and enforces result
	 * Used by Only BuddyPress Friends
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name - name of room.
	 * @param  string $host_status - type of host (if needed).
	 * @param  string $room_type - type of room for inspection.
	 * @return String  Template if Blocked - null if allowed.
	 */
	public function block_bp_friend_video_render( int $user_id, string $room_name, $host_status, $room_type = null ) {
		if (
			! Factory::get_instance( Dependencies::class )->is_buddypress_active() ||
			! Factory::get_instance( ModuleConfig::class )->read_enabled_status( Dependencies::MODULE_BUDDYPRESS_ID ) ||
			! bp_is_active( 'friends' ) ||
			! bp_is_groups_component()
		) {
			return null;
		}

		// Exiting for All room types except personal meeting.
		if ( \MyVideoRoomExtrasPlugin\Modules\MVRPersonalMeeting\MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING !== $room_name ) {
			return null;
		}

		$user_id_original = $user_id;
		// Check Site Override Status.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		if ( $site_override ) {
			$owner_id  = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		} else {
			$owner_id = $user_id;
		}
		// Site Default Settings Section.
		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
		->read_security_settings( $owner_id, $room_name, 'room_name' );
		if ( ! $does_room_record_exist ) {

			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'bp_friends_setting' );

		} else {
			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $owner_id, $room_name, 'bp_friends_setting' );
		}

			// Exit early if no setting for filter.
		if ( ! $room_access_setting ) {
			return null;
		}
			// Get Global Information on User Relationships to start.
		$visitor_id = get_current_user_id();
		if ( function_exists( 'friends_check_friendship_status' ) ) {
			$friends_status = friends_check_friendship_status( $user_id, $visitor_id );
		}

		switch ( $room_access_setting ) {
			case '':
				return null;
				// Drop Setting Off Cases.
			case 'Do-Not-Disturb':
				if ( $friends_status ) {
					return null;
				} else {
					return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id_original, $room_type );
				}

				// Else Fire the Block.
		}
			return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id_original, $room_type );
	}
}
