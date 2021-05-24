<?php
/**
 * Data Access Object for user video preferences
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class UserVideoPreference
 * Manages DB Layer for User Preferences for Video Room Base Settings.
 */
class UserVideoPreference {

	const TABLE_NAME = SiteDefaults::TABLE_NAME_USER_VIDEO_PREFERENCE;

	/**
	 * Get the table name for this DAO.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Save a User Video Preference into the database
	 *
	 * @param UserVideoPreferenceEntity $user_video_preference The video preference to save.
	 *
	 * @throws \Exception When failing to insert, most likely a duplicate key.
	 *
	 * @return UserVideoPreferenceEntity|null
	 */
	public function create( UserVideoPreferenceEntity $user_video_preference ): ?UserVideoPreferenceEntity {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$this->get_table_name(),
			array(
				'user_id'                 => $user_video_preference->get_user_id(),
				'room_name'               => $user_video_preference->get_room_name(),
				'layout_id'               => $user_video_preference->get_layout_id(),
				'reception_id'            => $user_video_preference->get_reception_id(),
				'reception_enabled'       => $user_video_preference->is_reception_enabled(),
				'reception_video_enabled' => $user_video_preference->is_reception_video_enabled(),
				'reception_video_url'     => $user_video_preference->get_reception_video_url_setting(),
				'show_floorplan'          => $user_video_preference->is_floorplan_enabled(),
			)
		);

		\wp_cache_set( $cache_key, $user_video_preference->to_json(), implode( '::', array( __CLASS__, 'get_by_id' ) ) );
		\wp_cache_delete( $user_video_preference->get_user_id(), implode( '::', array( __CLASS__, 'get_by_user_id' ) ) );

		if ( ! $result ) {
			return null;
		}

		return $user_video_preference;
	}

	/**
	 * Get a User Video Preference from the database
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 *
	 * @return UserVideoPreferenceEntity|null
	 */
	public function get_by_id( int $user_id, string $room_name ): ?UserVideoPreferenceEntity {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$user_id,
			$room_name
		);

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return UserVideoPreferenceEntity::from_json( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT user_id, room_name, layout_id, reception_id, reception_enabled, reception_video_enabled, reception_video_url, show_floorplan
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
				WHERE user_id = %d AND room_name = %s;
			',
				$user_id,
				$room_name,
			)
		);

		$result = null;

		if ( $row ) {
			$result = new UserVideoPreferenceEntity(
				(int) $row->user_id,
				$row->room_name,
				$row->layout_id,
				$row->reception_id,
				(bool) $row->reception_enabled,
				(bool) $row->reception_video_enabled,
				$row->reception_video_url,
				(bool) $row->show_floorplan,
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
	 * @return UserVideoPreferenceEntity[]
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
	 * Update a User Video Preference into the database
	 *
	 * @param UserVideoPreferenceEntity $user_video_preference The updated user video preference.
	 *
	 * @throws \Exception When failing to update.
	 *
	 * @return UserVideoPreferenceEntity|null
	 */
	public function update( UserVideoPreferenceEntity $user_video_preference ): ?UserVideoPreferenceEntity {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$this->get_table_name(),
			array(
				'user_id'                 => $user_video_preference->get_user_id(),
				'layout_id'               => $user_video_preference->get_layout_id(),
				'reception_id'            => $user_video_preference->get_reception_id(),
				'reception_enabled'       => $user_video_preference->is_reception_enabled(),
				'reception_video_enabled' => $user_video_preference->is_reception_video_enabled(),
				'reception_video_url'     => $user_video_preference->get_reception_video_url_setting(),
				'show_floorplan'          => $user_video_preference->is_floorplan_enabled(),
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
	 * @param UserVideoPreferenceEntity $user_video_preference The user video preference to delete.
	 *
	 * @throws \Exception When failing to delete.
	 *
	 * @return null
	 */
	public function delete( UserVideoPreferenceEntity $user_video_preference ) {
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
	 * Update Database Post ID.
	 * This function updates the Post ID of the User Entity Table so that new pages can pick up settings of deleted pages.
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

	// ---

	/**
	 * Create a cache key
	 *
	 * @param int    $user_id The user id.
	 * @param string $room_name The room name.
	 *
	 * @return string
	 */
	private function create_cache_key( int $user_id, string $room_name ): string {
		return "user_id:${user_id}:room_name:${room_name}:1";
	}

	/**
	 * Get a Just Preference Data from the database
	 * Returns layout ID, Reception ID, or Reception Enabled Status.
	 *
	 * @deprecated - use self::get_by_id instead.
	 *
	 * @param int    $user_id     The user id.
	 * @param string $room_name   The room name.
	 * @param string $return_type The return type.
	 *
	 * @return string|bool|null
	 */
	public function read_user_video_settings( int $user_id, string $room_name, string $return_type ) {
		$preference = $this->get_by_id( $user_id, $room_name );

		if ( ! $preference ) {
			return null;
		}

		// Return Data.
		switch ( $return_type ) {
			case 'layout_id':
				return $preference->get_layout_id();
			case 'reception_id':
				return $preference->get_reception_id();
			case 'reception_enabled':
				return $preference->is_reception_enabled();
			case 'reception_video_enabled':
				return $preference->is_reception_video_enabled();
			case 'reception_video_url':
				return $preference->get_reception_video_url_setting();
			case 'show_floorplan':
				return $preference->is_floorplan_enabled();
			default:
				return null;
		}
	}
}
