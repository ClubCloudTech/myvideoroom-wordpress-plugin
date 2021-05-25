<?php
/**
 * Data Access Object for user video preferences
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\Module\Security\DAO;

use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class SecurityVideoPreference
 */
class SecurityVideoPreference {
	/**
	 * Get the table name for this DAO.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . Security::TABLE_NAME_SECURITY_CONFIG;
	}

	/**
	 * Install Module Security Config Table.
	 *
	 * @return bool
	 */
	public function install_security_config_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $this->get_table_name() . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`user_id` BIGINT NOT NULL,
			`room_name` VARCHAR(255) NOT NULL,
			`room_disabled` BOOLEAN,
			`anonymous_enabled` BOOLEAN,
			`allow_role_control_enabled` BOOLEAN,
			`block_role_control_enabled` BOOLEAN,
			`site_override_enabled` BOOLEAN,
			`restrict_group_to_members_enabled` VARCHAR(255) NULL,
			`allowed_roles` VARCHAR(255) NULL,
			`blocked_roles` VARCHAR(255) NULL,
			`allowed_users` VARCHAR(255) NULL,
			`blocked_users` VARCHAR(255) NULL,
			`bp_friends_setting` VARCHAR(255) NULL,
			`allowed_template_id` BIGINT UNSIGNED NULL,
			`blocked_template_id` BIGINT UNSIGNED NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		return \maybe_create_table( $this->get_table_name(), $sql_create );
	}

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

		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$this->get_table_name(),
			array(
				'user_id'                           => $user_video_preference->get_user_id(),
				'room_name'                         => $user_video_preference->get_room_name(),
				'allowed_roles'                     => $user_video_preference->get_allowed_roles(),
				'blocked_roles'                     => $user_video_preference->get_blocked_roles(),
				'room_disabled'                     => $user_video_preference->is_room_disabled(),
				'anonymous_enabled'                 => $user_video_preference->is_anonymous_enabled(),
				'allow_role_control_enabled'        => $user_video_preference->is_allow_role_control_enabled(),
				'block_role_control_enabled'        => $user_video_preference->is_block_role_control_enabled(),
				'restrict_group_to_members_enabled' => $user_video_preference->is_restricted_to_group_to_members(),
				'site_override_enabled'             => $user_video_preference->is_site_override_enabled(),
				'bp_friends_setting'                => $user_video_preference->is_bp_friends_setting_enabled(),

			)
		);

		$user_video_preference->set_id( $wpdb->insert_id );

		\wp_cache_set( $cache_key, $user_video_preference->to_json(), implode( '::', array( __CLASS__, 'get_by_id' ) ) );
		\wp_cache_delete( $user_video_preference->get_user_id(), implode( '::', array( __CLASS__, 'get_by_user_id' ) ) );

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
	public function get_by_id( int $user_id, string $room_name ): ?SecurityVideoPreferenceEntity {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$user_id,
			$room_name
		);

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return SecurityVideoPreferenceEntity::from_json( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT 
			       record_id, 
			       user_id, 
			       room_name,
			       allowed_roles,
			       blocked_roles, 
			       room_disabled, 
			       anonymous_enabled,
			       allow_role_control_enabled,
			       block_role_control_enabled, 
			       site_override_enabled, 
			       restrict_group_to_members_enabled,
			       bp_friends_setting
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
				WHERE user_id = %d AND room_name = %s;
			',
				array(
					$user_id,
					$room_name,
				)
			)
		);

		$result = null;

		if ( $row ) {
			$result = new SecurityVideoPreferenceEntity(
				(int) $row->record_id,
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
			wp_cache_set( $cache_key, __METHOD__, $result->to_json() );
		} else {
			wp_cache_set( $cache_key, __METHOD__, null );
		}

		return $result;
	}



	/**
	 * Get a User Video Preference from the database
	 *
	 * @param int $user_id The user id.
	 *
	 * @return SecurityVideoPreferenceEntity[]
	 */
	public function get_by_user_id( int $user_id ): array {
		global $wpdb;

		$results = array();

		$room_names = \wp_cache_get( $user_id, __METHOD__ );

		if ( false === $room_names ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$room_names = $wpdb->get_col(
				$wpdb->prepare(
					'
						SELECT room_name
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
						WHERE user_id = %d;
					',
					$user_id,
				)
			);

			\wp_cache_set( $user_id, __METHOD__, $room_names );
		}

		foreach ( $room_names as $room_name ) {
			$results[] = $this->get_by_id( $user_id, $room_name );
		}

		return $results;
	}


	/**
	 * Update Database Post ID.
	 * This function updates the Post ID of the Security Entity Table so that new pages can pick up settings of deleted pages.
	 *
	 * @param  int $new_user_id New post_id to update preference table with.
	 * @param  int $old_user_id The old post that was deleted.
	 *
	 * @return bool
	 */
	public function update_user_id( int $new_user_id, int $old_user_id ): bool {
		$preferences = $this->get_by_user_id( $old_user_id );

		foreach ( $preferences as $preference ) {
			$preference->set_user_id( $new_user_id );
			$this->update( $preference );
		}

		return true;
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

		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$this->get_table_name(),
			array(
				'user_id'                           => $user_video_preference->get_user_id(),
				'allowed_roles'                     => $user_video_preference->get_allowed_roles(),
				'blocked_roles'                     => $user_video_preference->get_blocked_roles(),
				'room_disabled'                     => $user_video_preference->is_room_disabled(),
				'anonymous_enabled'                 => $user_video_preference->is_anonymous_enabled(),
				'allow_role_control_enabled'        => $user_video_preference->is_allow_role_control_enabled(),
				'block_role_control_enabled'        => $user_video_preference->is_block_role_control_enabled(),
				'site_override_enabled'             => $user_video_preference->is_site_override_enabled(),
				'restrict_group_to_members_enabled' => $user_video_preference->is_restricted_to_group_to_members(),
				'bp_friends_setting'                => $user_video_preference->is_bp_friends_setting_enabled(),

			),
			array(
				'user_id'   => $user_video_preference->get_user_id(),
				'room_name' => $user_video_preference->get_room_name(),
			)
		);

		\wp_cache_set( $cache_key, $user_video_preference->to_json(), implode( '::', array( __CLASS__, 'get_by_id' ) ) );
		\wp_cache_delete( $user_video_preference->get_user_id(), implode( '::', array( __CLASS__, 'get_by_user_id' ) ) );

		return $user_video_preference;
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

		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$this->get_table_name(),
			array(
				'user_id'   => $user_video_preference->get_user_id(),
				'room_name' => $user_video_preference->get_room_name(),
			)
		);

		\wp_cache_delete( $cache_key, implode( '::', array( __CLASS__, 'get_by_id' ) ) );
		\wp_cache_delete( $user_video_preference->get_user_id(), implode( '::', array( __CLASS__, 'get_by_user_id' ) ) );

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
		return "user_id:${user_id}:room_name:${room_name}";
	}

	/**
	 * Get a Just Preference Data from the database
	 *
	 * @deprecated Call self::get_by_id instead
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 * @param string $return_type - The field to return.
	 *
	 * @return null
	 *
	 * Returns layout ID, Reception ID, or Reception Enabled Status
	 */
	public function read_security_settings( int $user_id, string $room_name, string $return_type ) {

		if ( ! $return_type ) {
			return null;
		}

		$preference = $this->get_by_id( $user_id, $room_name );

		if ( ! $preference ) {
			return null;
		}

		switch ( $return_type ) {
			case 'site_override_enabled':
				return $preference->is_site_override_enabled();

			case 'room_name':
				return $preference->get_room_name();

			case 'allow_role_control_enabled':
				return $preference->is_allow_role_control_enabled();

			case 'anonymous_enabled':
				return $preference->is_anonymous_enabled();

			case 'block_role_control_enabled':
				return $preference->is_block_role_control_enabled();

			case 'room_disabled':
				return $preference->is_room_disabled();

			case 'bp_friends_setting':
				return $preference->is_bp_friends_setting_enabled();

			case 'restrict_group_to_members_enabled':
				return $preference->is_restricted_to_group_to_members();

			case 'allowed_roles':
				return $preference->get_allowed_roles();

			default:
				return null;
		}
	}

	/**
	 * Get User and Site Default Permissions from Database.
	 *
	 * @deprecated Call self::get_by_id instead
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 *
	 * @return object Permissions Object.
	 *
	 * Returns layout ID, Reception ID, or Reception Enabled Status
	 */
	public function check_security_settings( int $user_id, string $room_name ): ?SecurityVideoPreferenceEntity {
		return $this->get_by_id( $user_id, $room_name );
	}
}
