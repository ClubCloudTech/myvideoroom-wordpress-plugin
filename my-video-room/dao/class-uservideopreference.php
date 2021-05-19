<?php
/**
 * Data Access Object for user video preferences
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Core\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Core\SiteDefaults;

/**
 * Class UserVideoPreference
 * Manages DB Layer for User Preferences for Video Room Base Settings.
 */
class UserVideoPreference {

	const TABLE_NAME = SiteDefaults::TABLE_NAME_USER_VIDEO_PREFERENCE;

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

		/*
		$cache_key = $this->create_cache_key(
			$user_video_preference->get_user_id(),
			$user_video_preference->get_room_name()
		);
		*/

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'user_id'                 => $user_video_preference->get_user_id(),
				'room_name'               => $user_video_preference->get_room_name(),
				'layout_id'               => $user_video_preference->get_layout_id(),
				'reception_id'            => $user_video_preference->get_reception_id(),
				'reception_enabled'       => $user_video_preference->is_reception_enabled(),
				'reception_video_enabled' => $user_video_preference->get_reception_video_enabled_setting(),
				'reception_video_url'     => $user_video_preference->get_reception_video_url_setting(),
				'show_floorplan'          => $user_video_preference->get_show_floorplan_setting(),
			)
		);

		if ( ! $result ) {
			throw new \Exception();
		}

		// Removing cache as conflict happening in rooms - to test.
		// wp_cache_set( $cache_key, $user_video_preference );.
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
	public function read( int $user_id, string $room_name ): ?UserVideoPreferenceEntity {
		global $wpdb;

		/*
		$cache_key     = $this->create_cache_key( $user_id, $room_name );
		$cached_result = wp_cache_get( $cache_key );

		if ( $cached_result && $cached_result instanceof UserVideoPreferenceEntity ) {
			return $cached_result;
		}
		*/

		$raw_sql = '
				SELECT user_id, room_name, layout_id, reception_id, reception_enabled, reception_video_enabled, reception_video_url, show_floorplan
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
		$row    = $wpdb->get_row( $prepared_query );
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
		}

		// wp_cache_set( $cache_key, $result );.
		return $result;
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
				'layout_id'               => $user_video_preference->get_layout_id(),
				'reception_id'            => $user_video_preference->get_reception_id(),
				'reception_enabled'       => $user_video_preference->is_reception_enabled(),
				'reception_video_enabled' => $user_video_preference->get_reception_video_enabled_setting(),
				'reception_video_url'     => $user_video_preference->get_reception_video_url_setting(),
				'show_floorplan'          => $user_video_preference->get_show_floorplan_setting(),
			),
			array(
				'user_id'   => $user_video_preference->get_user_id(),
				'room_name' => $user_video_preference->get_room_name(),
			)
		);

		// wp_cache_set( $cache_key, $user_video_preference );.
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

		// wp_cache_delete( $cache_key );.

		return null;
	}

	/**
	 * Update Database Post ID.
	 * This function updates the Post ID of the User Entity Table so that new pages can pick up settings of deleted pages.
	 *
	 * @param  int $new_post_id - new post_id to update preference table with.
	 * @param  int $old_post_id - the old post that was deleted.
	 *
	 * @return bool|int
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

		// wp_cache_set( $cache_key, $user_video_preference );.
		return $result;
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
		return self::class . "::read:user_id:${user_id}:room_name:${room_name}:1";
	}

	/**
	 * Get a Just Preference Data from the database
	 * Returns layout ID, Reception ID, or Reception Enabled Status.
	 *
	 * @param int    $user_id     The user id.
	 * @param string $room_name   The room name.
	 * @param string $return_type The return type.
	 *
	 * @return string|bool
	 */
	public function read_user_video_settings( int $user_id, string $room_name, string $return_type ) {
		global $wpdb;

		$raw_sql = '
			SELECT user_id, room_name, layout_id, reception_id, reception_enabled, reception_video_enabled, reception_video_url, show_floorplan
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

		if ( ! $row ) {
			return null;
		}

		// Return Data.
		switch ( $return_type ) {
			case 'layout_id':
				return $row->layout_id;
			case 'reception_id':
				return $row->reception_id;
			case 'reception_enabled':
				return (bool) $row->reception_enabled;

			case 'reception_video_enabled':
				return (bool) $row->reception_video_enabled;

			case 'reception_video_url':
				return (bool) $row->reception_video_url;

			case 'show_floorplan':
				return (bool) $row->show_floorplan;
		}
	}

	/**
	 * Get Preference Data from the database
	 * Returns layout ID, Reception ID, or Reception Enabled Status.
	 *
	 * @param int    $user_id       The user id.
	 * @param string $room_name     The room name.
	 * @param string $return_type   The room name.
	 *
	 * @return string|null
	 */
	public function read_user_settings( int $user_id, string $room_name, string $return_type ) {
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

		if ( $row && $row->$return_type ) {
			return $row->$return_type;
		}

		return null;
	}
}
