<?php
/**
 * Addon functionality for BuddyPress -Video Room Handlers for BuddyPress
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressHelpers
 */

namespace MyVideoRoomPlugin\Module\BuddyPress\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference;

/**
 * Class BuddyPress Helpers provides Assistance Tools for BuddyPress Integration
 */
class BuddyPressHelpers {

	/**
	 * Modify UserID for Groups Hook
	 *
	 * @param int $user_id - required.
	 * @return string
	 */
	public function modify_user_id_for_groups( int $user_id ) {
		global $bp;
		if ( function_exists( 'bp_is_groups_component' ) && \bp_is_groups_component() && $bp->groups->current_group->id ) {
			$user_id = $bp->groups->current_group->id;
		}
		return $user_id;
	}

	/**
	 * Permissions Helpers
	 * These functions provide support to tabs based on user status
	 */

	/**
	 * Bp_is_user_moderator - returns whether a user id is a moderator of a BuddyPress Group
	 *
	 * @param  mixed $group_id - required.
	 * @param  mixed $user_id - not required.
	 * @return bool
	 */
	public function bp_is_user_moderator( $group_id, $user_id = null ): bool {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
			return null;
		}
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$is_mod = false;
		if ( function_exists( 'bp_get_user_groups' ) ) {
			$user_groups_mod = \bp_get_user_groups(
				$user_id,
				array(
					'is_mod' => true,
				)
			);
		}

		if ( isset( $user_groups_mod[ $group_id ] ) ) {
			$is_mod = true;
		}
		return $is_mod;
	}

	/**
	 * Bp_is_user_admin - returns admin status of a user in a group.
	 *
	 * @param  mixed $group_id - required.
	 * @param  mixed $user_id - optional.
	 * @return bool
	 */
	public function bp_is_user_admin( $group_id, $user_id = null ): bool {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
			return null;
		}
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$is_admin = false;
		if ( function_exists( 'bp_get_user_groups' ) ) {
			$user_groups_admin = \bp_get_user_groups(
				$user_id,
				array(
					'is_admin' => true,
				)
			);
		}

		if ( isset( $user_groups_admin[ $group_id ] ) ) {
			$is_admin = true;
		}
		return $is_admin;
	}

	/**
	 * Bp_is_user_member - checks whether user is member of a group
	 *
	 * @param  mixed $group_id - required.
	 * @param  mixed $user_id - optional.
	 * @return bool
	 */
	public function bp_is_user_member( $group_id, $user_id = null ): bool {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
			return null;
		}
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$is_member = false;
		if ( function_exists( 'bp_get_user_groups' ) ) {
			$user_groups_member = \bp_get_user_groups( $user_id );
		}

		if ( isset( $user_groups_member[ $group_id ] ) ) {
			$is_member = true;
		}
		return $is_member;
	}
	/**
	 * BP - are Users Friends.
	 *
	 * @param  int $user_id - The first person to check.
	 * @param  int $visitor_id - required. The second person to check.
	 * @return string
	 */
	public function bp_are_users_friends( int $user_id, int $visitor_id ): string {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
			return null;
		}
		if ( ! \bp_is_active( 'friends' ) || ! function_exists( 'friends_check_friendship' ) ) {
			return null;
		}
		$friends_status = \friends_check_friendship( $user_id, $visitor_id );
		return $friends_status;
	}
	/**
	 * Bp_can_host_group - returns whether user is a host of a group or not
	 *
	 * @param  mixed $group_id required.
	 * @param  mixed $user_id optional.
	 * @return bool
	 */
	public function bp_can_host_group( $group_id, $user_id = null ): bool {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
			return null;
		}
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$is_user_admin     = $this->bp_is_user_admin( $group_id, $user_id );
		$is_user_moderator = $this->bp_is_user_moderator( $group_id, $user_id );

		if ( $is_user_admin || $is_user_moderator || is_super_admin() || is_network_admin() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Bp_is_room_active - returns room state from DB
	 *
	 * @param  mixed $room_name - required.
	 * @param  mixed $user_id - optional.
	 * @return bool
	 */
	public function bp_is_room_active( $room_name, $user_id = null ): bool {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
			return null;
		}
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$room_disabled = Factory::get_instance( SecurityVideoPreference::class )->read_security_settings( $user_id, $room_name, 'room_disabled' );

		if ( $room_disabled ) {
			return false;
		}
		return true;
	}

}
