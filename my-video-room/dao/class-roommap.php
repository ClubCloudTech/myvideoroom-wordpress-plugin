<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\SiteDefaults;

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
	 * Get the table name for this DAO.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Get a PostID from the Database for a Page
	 *
	 * @param string $room_name inbound room from user.
	 *
	 * @return ?int
	 */
	public function get_post_id_by_room_name( string $room_name ): ?int {
		global $wpdb;

		$result = \wp_cache_get( $room_name, __METHOD__ );

		if ( false === $result ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$row = $wpdb->get_row(
				$wpdb->prepare(
					'
						SELECT post_id 
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
						WHERE room_name = %s
					',
					$room_name,
				)
			);

			if ( $row ) {
				$result = (int) $row->post_id;
			}

			\wp_cache_set( $room_name, $result, __METHOD__ );
		}

		return $result;
	}

	/**
	 * Register a given room in the Database, and ensure it does not already exist
	 *
	 * @param string  $room_name    The Room Name.
	 * @param int     $post_id      The Post iD.
	 * @param string  $room_type    The type of room to register.
	 * @param string  $display_name The Room Display Name for Header.
	 * @param string  $slug         The Slug.
	 * @param ?string $shortcode    The shortcode.
	 *
	 * @return string|int|false
	 */
	public function register_room_in_db( string $room_name, int $post_id, string $room_type, string $display_name, string $slug, string $shortcode = null ) {
		global $wpdb;
		// Empty input exit.
		if ( ! $room_name || ! $post_id ) {
			return 'Room Name or PostID Blank';
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$this->get_table_name(),
			array(
				'room_name'    => $room_name,
				'post_id'      => $post_id,
				'room_type'    => $room_type,
				'display_name' => $display_name,
				'slug'         => $slug,
				'shortcode'    => $shortcode,
			)
		);

		\wp_cache_delete( $room_name, __CLASS__ . '::get_post_id_by_room_name' );
		\wp_cache_delete( $room_type, __CLASS__ . '::get_all_post_ids_of_rooms' );
		\wp_cache_delete( $post_id, __CLASS__ . '::get_room_info' );
		\wp_cache_delete( '__ALL__', __CLASS__ . '::get_all_post_ids_of_rooms' );

		return $result;
	}

	/**
	 * Get Room Info from Database.
	 *
	 * @param int $post_id The Room iD to query.
	 *
	 * @return ?\stdClass
	 */
	public function get_room_info( int $post_id ): ?\stdClass {
		global $wpdb;

		$result = \wp_cache_get( $post_id, __METHOD__ );

		if ( false === $result ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$result = $wpdb->get_row(
				$wpdb->prepare(
					'
						SELECT room_name, post_id, room_type, display_name, slug
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
						WHERE post_id = %d
					',
					$post_id,
				)
			);

			if ( $result ) {
				$result->id = (int) $result->post_id;
			}

			\wp_cache_set( $post_id, $result, __METHOD__ );
		}
		if ( '' === $result ) {
			$result = null;
		}
		return $result;
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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
					SET post_id = %s
					WHERE room_name = %s
				',
				$post_id,
				$room_name,
			)
		);

		\wp_cache_delete( $room_name, __CLASS__ . '::get_post_id_by_room_name' );
		\wp_cache_delete( $post_id, __CLASS__ . '::get_room_info' );

		return null;
	}


	/**
	 * Delete a Room Record in Database.
	 * This function will delete the room name in the database with the parameter.
	 *
	 * @param string $room_name The Room Name.
	 *
	 * @return bool
	 */
	public function delete_room_mapping( string $room_name ): bool {
		global $wpdb;

		// empty input exit.
		if ( ! $room_name ) {
			return false;
		}

		$post_id = $this->get_post_id_by_room_name( $room_name );

		if ( ! $post_id ) {
			return false;
		}

		$room_info = $this->get_room_info( $post_id );

		if ( ! $room_info ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					DELETE FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
				    WHERE room_name = %s
			    ',
				$room_name,
			)
		);

		\wp_cache_delete( $room_name, __CLASS__ . '::get_post_id_by_room_name' );
		\wp_cache_delete( $room_info->room_type, __CLASS__ . '::get_all_post_ids_of_rooms' );
		\wp_cache_delete( $post_id, __CLASS__ . '::get_room_info' );
		\wp_cache_delete( '__ALL__', __CLASS__ . '::get_all_post_ids_of_rooms' );

		return true;
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
		$post_id_check = $this->get_post_id_by_room_name( $room_name );
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
	 * @param ?string $room_type The room type to query.
	 *
	 * @return array
	 */
	public function get_all_post_ids_of_rooms( string $room_type = null ): array {
		global $wpdb;

		$cache_key = $room_type;
		if ( null === $room_type ) {
			$cache_key = '__ALL__';
		}

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( false === $result ) {
			if ( $room_type ) {

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						'
							SELECT post_id
							FROM ' . /*phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared*/$this->get_table_name() . '
							WHERE room_type = %s
							ORDER BY room_type ASC
						',
						$room_type
					)
				);
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						'
							SELECT post_id
							FROM ' . /*phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared*/$this->get_table_name() . '
							ORDER BY room_type ASC
						'
					)
				);
			}

			$result = array_map(
				function ( $row ) {
					return (int) $row->post_id;
				},
				$rows
			);

			\wp_cache_set( $cache_key, $result, __METHOD__ );
		}

		return $result;
	}
}
