<?php
/**
 * Data Access Object for Room Attendee Sync
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideo.php
 */

namespace MyVideoRoomPlugin\Module\WooCommerce\DAO;

use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceRoomSync;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class WooCommerceRoomSyncDAO
 */
class WooCommerceRoomSyncDAO {

	/**
	 * Install WooCommerce Sync Config Table.
	 *
	 * @return bool
	 */
	public function install_woocommerce_room_presence_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $this->get_room_presence_table_name() . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`cart_id` VARCHAR(255) NOT NULL,
			`room_name` VARCHAR(255) NOT NULL,
			`timestamp` BIGINT UNSIGNED NULL,
			`room_host` BOOLEAN,
			`current_master` BOOLEAN,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		return \maybe_create_table( $this->get_room_presence_table_name(), $sql_create );
	}


	/**
	 * Get the table name for Room Presence Table DAO.
	 *
	 * @return string
	 */
	private function get_room_presence_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . WooCommerce::TABLE_NAME_WOOCOMMERCE_ROOM;
	}

	/**
	 * Save a Room Sync Event into the database
	 *
	 * @param WooCommerceRoomSync $woocommerceroomsyncobj The video preference to save.
	 *
	 * @return WooCommerceRoomSync|null
	 * @throws \Exception When failing to insert, most likely a duplicate key.
	 */
	public function create( WooCommerceRoomSync $woocommerceroomsyncobj ): ?WooCommerceRoomSync {
		global $wpdb;

		$cart_id   = $woocommerceroomsyncobj->get_cart_id();
		$room_name = $woocommerceroomsyncobj->get_room_name();

		// Check Record Doesn't already exist (update not create if it does).

		$check = $this->get_by_id_sync_table( $cart_id, $room_name );

		if ( $check ) {
			return $this->update( $woocommerceroomsyncobj );
		}

		$cache_key = $this->create_cache_key(
			$cart_id,
			$room_name
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$this->get_room_presence_table_name(),
			array(
				'cart_id'        => $woocommerceroomsyncobj->get_cart_id(),
				'room_name'      => $woocommerceroomsyncobj->get_room_name(),
				'timestamp'      => $woocommerceroomsyncobj->get_timestamp(),
				'room_host'      => $woocommerceroomsyncobj->is_room_host(),
				'current_master' => $woocommerceroomsyncobj->is_current_master(),
			)
		);

		$woocommerceroomsyncobj->set_id( $wpdb->insert_id );

		\wp_cache_set(
			$cache_key,
			$woocommerceroomsyncobj->to_json(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_id_sync_table',
				)
			)
		);
		\wp_cache_delete(
			$woocommerceroomsyncobj->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return $woocommerceroomsyncobj;
	}

	/**
	 * Create a cache key
	 *
	 * @param string $cart_id   The user id.
	 * @param string $room_name The room name.
	 *
	 * @return string
	 */
	private function create_cache_key( string $cart_id, string $room_name ): string {
		return "cart_id:${cart_id}:room_name:${room_name}";
	}

	/**
	 * Get a Cart Object from the database
	 *
	 * @param string $cart_id The Cart id.
	 *
	 * @return WooCommerceRoomSync[]
	 */
	public function get_by_cart_id( string $cart_id ): array {
		global $wpdb;

		$results = array();

		$room_names = \wp_cache_get( $cart_id, __METHOD__ );

		if ( false === $room_names ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$room_names = $wpdb->get_col(
				$wpdb->prepare(
					'
						SELECT room_name
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
						WHERE cart_id = %s;
					',
					$cart_id,
				)
			);

			\wp_cache_set( $cart_id, __METHOD__, $room_names );
		}

		foreach ( $room_names as $room_name ) {
			$results[] = $this->get_by_id_sync_table( $cart_id, $room_name );
		}

		return $results;
	}

	/**
	 * Get all Room Participants
	 *
	 * @param string $room_name - The Room Name to Return Recipients for.
	 *
	 * @return WooCommerceRoomSync[]
	 */
	public function get_room_participants( string $room_name ) {
		global $wpdb;

		$timestamp    = \current_time( 'timestamp' );
		$allowed_time = $timestamp - SiteDefaults::LAST_VISITED_TOLERANCE;

		// Can't cache as query involves time.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$participants = $wpdb->get_results(
				$wpdb->prepare(
					'
						SELECT cart_id, room_name, timestamp, room_host, current_master, record_id 
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
						WHERE room_name = %s AND timestamp > %d;
					',
					$room_name,
					$allowed_time
				)
			);

		return $participants;
	}

	/**
	 * Get Room Master
	 *
	 * @param string $room_name - The Room Name to Return Recipients for.
	 *
	 * @return WooCommerceRoomSync[]
	 */
	public function get_room_masters( string $room_name ) {
		global $wpdb;

		// Can't cache as query involves time.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$participants = $wpdb->get_results(
				$wpdb->prepare(
					'
						SELECT cart_id, room_name, timestamp, room_host, current_master, record_id 
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
						WHERE room_name = %s AND current_master IS TRUE 
						ORDER BY timestamp DESC
					',
					$room_name,
				)
			);

		return $participants;
	}

	/**
	 * Update Master Status in Database.
	 *
	 * @param string $cart_id   The New ID for the Master.
	 * @param string $room_name The Room Name.
	 *
	 * @return bool|null
	 */
	public function update_master( string $cart_id, string $room_name ): ?bool {
		global $wpdb;

		// Empty input exit.
		if ( ! $cart_id || ! $room_name ) {
			return false;
		}
//Flush all Other Masters.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET current_master = 0
					WHERE cart_id <> %s AND room_name = %s
				',
				$cart_id,
				$room_name,
			)
		);
//Set New Master.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET current_master = 1
					WHERE cart_id = %s AND room_name = %s
				',
				$cart_id,
				$room_name,
			)
		);

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );
		\wp_cache_delete( $cart_id, __CLASS__ . '::get_room_info' );

		return null;
	}



	/**
	 * Get a Cart Object from the database
	 *
	 * @param string $cart_id   The Cart id.
	 * @param string $room_name The room name.
	 *
	 * @return WooCommerceRoomSync|null
	 */
	public function get_by_id_sync_table( string $cart_id, string $room_name ): ?WooCommerceRoomSync {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$cart_id,
			$room_name
		);

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return WooCommerceRoomSync::from_json( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT 
			       cart_id, 
			       room_name,
			       timestamp, 
				   room_host,
				   current_master,  
				   record_id
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
				WHERE cart_id = %s AND room_name = %s;
			',
				array(
					$cart_id,
					$room_name,
				)
			)
		);

		$result = null;

		if ( $row ) {
			$result = new WooCommerceRoomSync(
				$row->cart_id,
				$row->room_name,
				$row->timestamp,
				$row->room_host,
				$row->current_master,
				$row->id,
			);
			wp_cache_set( $cache_key, __METHOD__, $result->to_json() );
		} else {
			wp_cache_set( $cache_key, __METHOD__, null );
		}

		return $result;
	}

	/**
	 * Update a Cart Object into the database
	 *
	 * @param WooCommerceRoomSync $woocommerceroomsyncobj The updated Cart Object.
	 *
	 * @return WooCommerceRoomSync|null
	 * @throws \Exception When failing to update.
	 */
	public function update( WooCommerceRoomSync $woocommerceroomsyncobj ): ?WooCommerceRoomSync {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$woocommerceroomsyncobj->get_cart_id(),
			$woocommerceroomsyncobj->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$this->get_room_presence_table_name(),
			array(
				'cart_id'        => $woocommerceroomsyncobj->get_cart_id(),
				'room_name'      => $woocommerceroomsyncobj->get_room_name(),
				'timestamp'      => $woocommerceroomsyncobj->get_timestamp(),
				'room_host'      => $woocommerceroomsyncobj->is_room_host(),
				'current_master' => $woocommerceroomsyncobj->is_current_master(),
			),
			array(
				'cart_id'   => $woocommerceroomsyncobj->get_cart_id(),
				'room_name' => $woocommerceroomsyncobj->get_room_name(),
			)
		);

		\wp_cache_set(
			$cache_key,
			$woocommerceroomsyncobj->to_json(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_id_sync_table',
				)
			)
		);
		\wp_cache_delete(
			$woocommerceroomsyncobj->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return $woocommerceroomsyncobj;
	}

	/**
	 * Delete a Cart Object from the database
	 *
	 * @param WooCommerceRoomSync $woocommerceroomsyncobj The Cart Object to delete.
	 *
	 * @return null
	 * @throws \Exception When failing to delete.
	 */
	public function delete( WooCommerceRoomSync $woocommerceroomsyncobj ) {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$woocommerceroomsyncobj->get_cart_id(),
			$woocommerceroomsyncobj->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$this->get_room_presence_table_name(),
			array(
				'cart_id'   => $woocommerceroomsyncobj->get_cart_id(),
				'room_name' => $woocommerceroomsyncobj->get_room_name(),
			)
		);

		\wp_cache_delete( $cache_key, implode( '::', array( __CLASS__, 'get_by_id_sync_table' ) ) );
		\wp_cache_delete(
			$woocommerceroomsyncobj->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return null;
	}
}
