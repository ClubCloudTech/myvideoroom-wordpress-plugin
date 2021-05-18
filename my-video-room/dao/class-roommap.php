<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Factory;

/**
 * Class RoomMap
 * Registers Rooms Permanently in Database - base for WCBookings, Meet Center, Site Video.
 */
class RoomMap {
	const TABLE_NAME             = SiteDefaults::TABLE_NAME_ROOM_MAP;
	const PAGE_STATUS_EXISTS     = 'page-exists';
	const PAGE_STATUS_NOT_EXISTS = 'page-not-exists';
	const PAGE_STATUS_ORPHANED   = 'page-not-exists-but-has-reference';

	/**
	 * Get a PostID from the Database for a Page
	 *
	 * @param string $room_name inbound room from user.
	 *
	 * @return ?int
	 */
	public function read( string $room_name ): ?int {
		global $wpdb;

		$raw_sql = '
			SELECT post_id
			FROM ' . $wpdb->prefix . self::TABLE_NAME . '
			WHERE room_name = %s
		';

		$prepared_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$raw_sql,
			array(
				$room_name,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );

		if ( $row ) {
			return (int) $row->post_id;
		}

		return null;
	}

	/**
	 * Register a given room in the Database, and ensure it does not already exist
	 *
	 * @param  string $room_name - The Room Name.
	 * @param  int    $post_id - The Post iD.
	 * @param  string $room_type - The type of room to register.
	 * @param  string $display_name - The Room Display Name for Header.
	 * @param  string $slug - The Slug.
	 *
	 * @return string|int|false
	 */
	public function register_room_in_db( string $room_name, int $post_id, string $room_type, string $display_name, string $slug ) {
		global $wpdb;
		// Empty input exit.
		if ( ! $room_name || ! $post_id ) {
			return 'Room Name or PostID Blank';
		}

		// Create Post.
		$result = $wpdb->insert(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'room_name'    => $room_name,
				'post_id'      => $post_id,
				'room_type'    => $room_type,
				'display_name' => $display_name,
				'slug'         => $slug,
			)
		);

		return $result;
	}

	/**
	 * Get Room Info from Database.
	 *
	 * @param int $post_id The Room iD to query.
	 *
	 * @return array|object|void|null
	 */
	public function get_room_info( int $post_id ) {
		global $wpdb;
		$raw_sql = '
			SELECT room_name, post_id, room_type, display_name, slug
			FROM ' . $wpdb->prefix . self::TABLE_NAME . '
			WHERE post_id = %d
		';

		$prepared_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$raw_sql,
			array(
				$post_id,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );

		if ( $row ) {
			return $row;
		}

		return null;
	}



	/**
	 * Update Room Post ID in Database
	 * This plugin will update the room name in the database with the parameter
	 *
	 * @param string $post_id   The Post iD.
	 * @param string $room_name The Room Name.
	 *
	 * @return bool|null
	 */
	public function update_room_post_id( string $post_id, string $room_name ) {
		global $wpdb;

		// Empty input exit.
		if ( ! $post_id || ! $room_name ) {
			return false;
		}

		$raw_sql = '
			UPDATE ' . $wpdb->prefix . self::TABLE_NAME . '
			SET post_id = %s
			WHERE room_name = %s
		';

		$prepared_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$raw_sql,
			array(
				$room_name,
				$post_id,
			)
		);

		$wpdb->query( $prepared_query );

		return null;
	}
	/**
	 * Delete a Room Record in Database.
	 * This function will delete the room name in the database with the parameter.
	 *
	 * @param string $room_name The Room Name.
	 *
	 * @return bool|null
	 */
	public function delete_room_mapping( string $room_name ) {
		global $wpdb;

		// empty input exit.
		if ( ! $room_name ) {
			return false;
		}

		$raw_sql = '
			DELETE FROM ' . $wpdb->prefix . self::TABLE_NAME . '
			WHERE room_name = %s
		';

		$prepared_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared.
			$raw_sql,
			array(
				$room_name,
			)
		);

		$wpdb->query( $prepared_query );

		return null;
	}

	/**
	 * Register a given room in the Database, and ensure it does not already exist
	 *
	 * @param string $room_name The Room Name.
	 *
	 * @return bool|String  Yes, No, Orphan (database exists but page deleted)
	 */
	public function check_page_exists( string $room_name ) {
		// Empty input exit.
		if ( ! $room_name ) {
			return false;
		}

		// First Check Database for Room and Post ID - return No if blank.
		$post_id_check = Factory::get_instance( self::class )->read( $room_name );
		if ( ! $post_id_check ) {
			return self::PAGE_STATUS_NOT_EXISTS;
		}

		// Second Check Post Actually Exists in WP still (user hasn't deleted page).
		$post_object = get_post( $post_id_check );
		if ( ! $post_object ) {
			return self::PAGE_STATUS_ORPHANED;
		} else {
			return self::PAGE_STATUS_EXISTS;
		}
	}

	/**
	 * Get Additional Rooms Installed
	 *
	 * @param  string $room_type - the room type to query.
	 *
	 * @return array
	 */
	public function get_room_list( string $room_type ): attay {
		global $wpdb;

		$raw_sql = '
			SELECT post_id
			FROM ' . $wpdb->prefix . self::TABLE_NAME . '
			WHERE room_type = %s
		';

		$prepared_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$raw_sql,
			array(
				$room_type,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_results( $prepared_query );

		$output = array();
		foreach ( $row as $datarow ) {
			array_push( $output, $datarow->post_id );
		}

		return $output;
	}
}
