<?php
/**
 * Data Access Object for user video preferences
 *
 * @package MyVideoRoomPlugin\Core\Dao
 */

namespace MyVideoRoomPlugin\Module\Security\DAO;

use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\Security\Security;

/**
 * Class SecurityVideoPreference
 */
class SecurityVideoPreference {

	const TABLE_NAME = Security::TABLE_NAME_SECURITY_CONFIG;


	/**
	 * Save a User Video Preference into the database
	 *
	 * @param SecurityVideoPreferenceEntity $user_video_preference The video preference to save.
	 *
	 * @throws \Exception When failing to insert, most likely a duplicate key.
	 *
	 * @return SecurityVideoPreferenceEntity|null
	 */
	public function create( SecurityVideoPreferenceEntity $user_video_preference ): ?SecurityVideoPreferenceEntity {
		global $wpdb;

		/*
		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name());

		*/

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'user_id'                           => $user_video_preference->get_user_id(),
				'room_name'                         => $user_video_preference->get_room_name(),
				'allowed_roles'                     => $user_video_preference->get_allowed_roles(),
				'blocked_roles'                     => $user_video_preference->get_blocked_roles(),
				'room_disabled'                     => $user_video_preference->is_room_disabled(),
				'anonymous_enabled'                 => $user_video_preference->is_anonymous_enabled(),
				'allow_role_control_enabled'        => $user_video_preference->is_allow_role_control_enabled(),
				'block_role_control_enabled'        => $user_video_preference->is_block_role_control_enabled(),
				'restrict_group_to_members_enabled' => $user_video_preference->check_restrict_group_to_members_setting(),
				'site_override_enabled'             => $user_video_preference->check_site_override_setting(),
				'bp_friends_setting'                => $user_video_preference->check_bp_friends_setting(),

			)
		);

		if ( ! $result ) {
			throw new \Exception();
		}
		// Removing cache as conflict happening in rooms - to test
		// wp_cache_set( $cache_key, $user_video_preference );
		return $user_video_preference;
	}

	/**
	 * Get a User Video Preference from the database
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 *
	 * @return SecurityVideoPreferenceEntity|null
	 */
	public function read( int $user_id, string $room_name ): ?SecurityVideoPreferenceEntity {
		global $wpdb;
		/*
		$cache_key     = $this->create_cache_key( $user_id, $room_name );
		$cached_result = wp_cache_get( $cache_key );

		if ( $cached_result && $cached_result instanceof SecurityVideoPreferenceEntity ) {
			return $cached_result;
		}
		*/

		$raw_sql = '
				SELECT user_id, room_name, allowed_roles, blocked_roles, room_disabled, anonymous_enabled, allow_role_control_enabled, block_role_control_enabled, site_override_enabled, restrict_group_to_members_enabled, bp_friends_setting
				FROM ' . $wpdb->prefix . self::TABLE_NAME . '
				WHERE user_id = %d AND room_name = %s;
			';

		$prepared_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$raw_sql,
			array(
				$user_id,
				$room_name,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );

		$result = null;

		if ( $row ) {
			$result = new SecurityVideoPreferenceEntity(
				(int) $row->user_id,
				$row->room_name,
				$row->allowed_roles,
				$row->blocked_roles,
				(bool) $row->room_disabled,
				(bool) $row->anonymous_enabled,
				(bool) $row->allow_role_control_enabled,
				(bool) $row->block_role_control_enabled,
				(bool) $row->site_override_enabled,
				$row->restrict_group_to_members_enabled,
				$row->bp_friends_setting,
			);
		}

		// wp_cache_set( $cache_key, $result );
		return $result;
	}

	/**
	 * Update Database Post ID.
	 * This function updates the Post ID of the Security Entity Table so that new pages can pick up settings of deleted pages.
	 *
	 * @param  int $new_post_id - new post_id to update preference table with.
	 * @param  int $old_post_id - the old post that was deleted.
	 * @return void|null
	 */
	public function update_post_id( int $new_post_id, int $old_post_id ) {
		global $wpdb;
		/*
		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);
		*/

		$wpdb->show_errors();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->update(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'user_id' => $new_post_id,
			),
			array(
				'user_id' => $old_post_id,
			)
		);
		// wp_cache_set( $cache_key, $user_video_preference );
		return $result;
	}


	/**
	 * Update a User Video Preference into the database
	 *
	 * @param SecurityVideoPreferenceEntity $user_video_preference The updated user video preference.
	 *
	 * @throws \Exception When failing to update.
	 *
	 * @return SecurityVideoPreferenceEntity|null
	 */
	public function update( SecurityVideoPreferenceEntity $user_video_preference ): ?SecurityVideoPreferenceEntity {
		global $wpdb;
		/*
		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);
		*/

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->update(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'allowed_roles'                     => $user_video_preference->get_allowed_roles(),
				'blocked_roles'                     => $user_video_preference->get_blocked_roles(),
				'room_disabled'                     => $user_video_preference->is_room_disabled(),
				'anonymous_enabled'                 => $user_video_preference->is_anonymous_enabled(),
				'allow_role_control_enabled'        => $user_video_preference->is_allow_role_control_enabled(),
				'block_role_control_enabled'        => $user_video_preference->is_block_role_control_enabled(),
				'site_override_enabled'             => $user_video_preference->check_site_override_setting(),
				'restrict_group_to_members_enabled' => $user_video_preference->check_restrict_group_to_members_setting(),
				'bp_friends_setting'                => $user_video_preference->check_bp_friends_setting(),

			),
			array(
				'user_id'   => $user_video_preference->get_user_id(),
				'room_name' => $user_video_preference->get_room_name(),
			)
		);

		if ( false === $result ) {
			throw new \Exception();
		}

		// wp_cache_set( $cache_key, $user_video_preference );
		return $user_video_preference;
	}
	/**
	 * Reads WordPress Roles, and Merges with Security Settings stored in DB to render Multi-Select Dialog Boxes
	 *
	 * @param  int    $user_id - The User_ID.
	 * @param  string $room_name - Name of Room.
	 * @param  string $return_type = Type of Info Required.
	 * @return string
	 */
	public function read_multi_checkbox_admin_roles( int $user_id, string $room_name, string $return_type ): string {
		// Setup.
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$output    = null;

		// Get Settings in Database - return type - matches the field in the database - return it on top.
		$db_setting = $this->read_security_settings( $user_id, $room_name, $return_type );
		// Add Clear Option to Select Box if there are parameters Stored.
		if ( $db_setting ) {
			echo '<option value="">( Clear Selections - Remove Stored Roles)</option>';
		}
		$db_array  = explode( '|', $db_setting );
		$db_output = null;
		foreach ( $db_array as $setting_returned ) {
			$db_output .= '<option value="' . esc_attr( $setting_returned ) . '" selected>' . esc_html( $setting_returned ) . '</option>';
		}
		// Now need to exclude a setting if already returned above.
		foreach ( $all_roles as $key ) {
			if ( strpos( $db_setting, $key['name'] ) === false ) {
				if ( $current_user_setting &&
							$current_user_setting->get_allowed_roles() === $key['name']
						) {
					$output .= '<option value="' . esc_attr( $key['name'] ) . '" selected>' . esc_html( $key['name'] ) . '</option>';
				} else {
					$output .= '<option value="' . esc_attr( $key['name'] ) . '">' . esc_html( $key['name'] ) . '</option>';
				}
			}
		}
		return $db_output . $output;
	}

	/**
	 * Reads Database Stored Roles and returns an array of roles
	 *
	 * @param  int    $user_id - The UserID.
	 * @param  string $room_name - Name of Room to check.
	 * @param  string $return_type - Type of Return.
	 * @return string
	 */
	public function read_db_wordpress_roles( int $user_id, string $room_name, $return_type ) {

		// Get Settings in Database - return type - matches the field in the database - return it on top.
		$db_setting = $this->read_security_settings( $user_id, $room_name, $return_type );
		// Return blank if nothing set.
		if ( ! $db_setting ) {
			return null;
		}

		$db_array = explode( '|', $db_setting );

		return $db_array;
	}


	/**
	 * Delete a User Video Preference from the database
	 *
	 * @param SecurityVideoPreferenceEntity $user_video_preference The user video preference to delete.
	 *
	 * @throws \Exception When failing to delete.
	 *
	 * @return null
	 */
	public function delete( SecurityVideoPreferenceEntity $user_video_preference ) {
		global $wpdb;

		/*
		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);
		*/

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->delete(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'user_id'   => $user_video_preference->get_user_id(),
				'room_name' => $user_video_preference->get_room_name(),
			)
		);

		if ( $result ) {
			throw new \Exception();
		}
		// wp_cache_delete( $cache_key );

		return null;
	}

	/**
	 * Create a cache key
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 *
	 * @return string
	 */
	private function create_cache_key( int $user_id, string $room_name ): string {
		return self::class . "::read:user_id:${user_id}:room_name:${room_name}:1";
	}

	/**
	 * Get a Just Preference Data from the database
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 *
	 * Returns layout ID, Reception ID, or Reception Enabled Status
	 */
	public static function read_security_settings( int $user_id, string $room_name, string $return_type ) {
		global $wpdb;
		if ( ! $return_type ) {
			return null;
		}

		$raw_sql = '
		SELECT user_id, room_name, ' . $return_type . '
		FROM ' . $wpdb->prefix . self::TABLE_NAME . '
		WHERE user_id = %d AND room_name = %s;
	';

		$prepared_query = $wpdb->prepare(
											// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$raw_sql,
			array(
				$user_id,
				$room_name,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );

		if ( $row ) {
			return $row->$return_type;
		}
		return null;
	}


}
