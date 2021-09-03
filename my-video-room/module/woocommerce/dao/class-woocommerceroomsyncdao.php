<?php
/**
 * Data Access Object for Room Attendee Sync
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideo.php
 */

namespace MyVideoRoomPlugin\Module\WooCommerce\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceRoomSync;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;
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
			`last_notification` BIGINT UNSIGNED NULL,
			`room_host` BOOLEAN,
			`basket_change` VARCHAR(255) NULL,
			`sync_state` VARCHAR(255) NULL,
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
				'cart_id'           => $woocommerceroomsyncobj->get_cart_id(),
				'room_name'         => $woocommerceroomsyncobj->get_room_name(),
				'timestamp'         => $woocommerceroomsyncobj->get_timestamp(),
				'last_notification' => $woocommerceroomsyncobj->get_last_notification(),
				'room_host'         => $woocommerceroomsyncobj->is_room_host(),
				'basket_change'     => $woocommerceroomsyncobj->get_basket_change(),
				'sync_state'        => $woocommerceroomsyncobj->get_sync_state(),
				'current_master'    => $woocommerceroomsyncobj->is_current_master(),
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
						SELECT cart_id, room_name, timestamp, room_host, current_master, basket_change, record_id 
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
	 * Get all Room Hosts
	 *
	 * @param string $room_name - The Room Name to Return Recipients for.
	 *
	 * @return WooCommerceRoomSync[]
	 */
	public function get_room_hosts_from_db( string $room_name ) {
		global $wpdb;

		$timestamp    = \current_time( 'timestamp' );
		$allowed_time = $timestamp - SiteDefaults::LAST_VISITED_TOLERANCE;

		// Can't cache as query involves time.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$participants = $wpdb->get_results(
				$wpdb->prepare(
					'
						SELECT cart_id, room_name, timestamp, room_host, current_master, basket_change, record_id, sync_state, last_notification
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
						WHERE room_name = %s AND timestamp > %d AND room_host IS TRUE;
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
						SELECT cart_id, room_name, timestamp, room_host, current_master, record_id, sync_state
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
		// Flush all Other Masters.

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
		// Set New Master.

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
		// Clear Basket Transfer State.
		$this->update_basket_transfer_state( $room_name );

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );
		\wp_cache_delete( $cart_id, __CLASS__ . '::get_room_info' );

		return true;
	}

	/**
	 * Notify User.
	 *
	 * @param string $room_name The Room Name.
	 * @param string $new_master_id - (optional). If entered without state to set - will automatically turn on sync for this ID.
	 *
	 * @return bool|null
	 */
	public function notify_user( string $room_name, string $user_hash_id = null ): ?bool {
		global $wpdb;

		$timestamp   = current_time( 'timestamp' );
		$am_i_host   = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
		$sync_state  = Factory::get_instance( HostManagement::class )->am_i_syncing( $room_name );
		$am_i_master = Factory::get_instance( HostManagement::class )->am_i_master( $room_name );

		// Try to Update First.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET last_notification = %d, timestamp = %d 
					WHERE cart_id = %s AND room_name = %s
				',
				$timestamp,
				$timestamp,
				$user_hash_id,
				$room_name,
			)
		);
		// If Record Doesn't Exist Create Record.
		if ( ! $result ) {
			$result = new WooCommerceRoomSync(
				$user_hash_id,
				$room_name,
				current_time( 'timestamp' ),
				current_time( 'timestamp' ),
				$am_i_host,
				WooCommerce::SETTING_BASKET_REQUEST_OFF,
				$sync_state,
				$am_i_master,
				null
			);
			$this->create( $result );
		}

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );

		return null;
	}



	/**
	 * Turn Change Basket Sync.
	 *
	 * @param string $room_name The Room Name.
	 * @param string $new_master_id - (optional). If entered without state to set - will automatically turn on sync for this ID.
	 * @param string $state_to_change  - The state to set. (either one must be set).
	 * @param bool   $clear_flag  - Setting to Delete Setting for Sync State.
	 *
	 * @return bool|null
	 */
	public function change_basket_sync_state( string $room_name, string $new_master_id = null, string $state_to_change = null, bool $clear_flag = null ): ?bool {
		global $wpdb;

		// Handle What to Change.
		if ( $clear_flag && $room_name && $new_master_id ) {
			$sync_state   = null;
			$core_room_id = $new_master_id;

		} elseif ( $state_to_change ) {
			// State Change Option requires $new_master_id - if its missing - exit.
			if ( ! $new_master_id ) {
				return false;
			}
			$sync_state   = $state_to_change;
			$core_room_id = $new_master_id;
		} else {
			$sync_state   = $new_master_id;
			$core_room_id = WooCommerce::SETTING_BASKET_REQUEST_USER;
		}
		$timestamp = current_time( 'timestamp' );

		// Try to Update First.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET sync_state = %s, last_notification = %d 
					WHERE cart_id = %s AND room_name = %s
				',
				$sync_state,
				$timestamp,
				$core_room_id,
				$room_name,
			)
		);
		// If Record Doesn't Exist Create Record.
		if ( ! $result ) {
			$result = new WooCommerceRoomSync(
				WooCommerce::SETTING_BASKET_REQUEST_USER,
				$room_name,
				current_time( 'timestamp' ),
				current_time( 'timestamp' ),
				false,
				WooCommerce::SETTING_BASKET_REQUEST_OFF,
				$sync_state,
				false,
				null
			);
			$this->create( $result );
		}
		// Return Failure if Create and Update Failed.
		if ( ! $result ) {
			return false;
		}

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );

		return true;
	}

	/**
	 * Update Basket Transfer State.
	 *
	 * @param string $room_name The Room Name.
	 * @param string $user_hash  - user has to match (optional).
	 * @param string $state_to_change - the value to set (optional).
	 *
	 * @return bool|null
	 */
	public function update_basket_transfer_state( string $room_name, string $user_hash = null, string $state_to_change = null ): ?bool {
		global $wpdb;
		if ( $user_hash && $state_to_change ) {
			$core_room_id  = $user_hash;
			$basket_change = $state_to_change;
		} else {
			$core_room_id  = WooCommerce::SETTING_BASKET_REQUEST_USER;
			$basket_change = WooCommerce::SETTING_BASKET_REQUEST_OFF;
		}

		// Try to Update First.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET basket_change = %s
					WHERE cart_id = %s AND room_name = %s
				',
				$basket_change,
				$core_room_id,
				$room_name,
			)
		);
		// If Record Doesn't Exist Create Record.
		if ( ! $result ) {
			$result = new WooCommerceRoomSync(
				WooCommerce::SETTING_BASKET_REQUEST_USER,
				$room_name,
				current_time( 'timestamp' ),
				current_time( 'timestamp' ),
				false,
				WooCommerce::SETTING_BASKET_REQUEST_OFF,
				null,
				false,
				null
			);
			$this->create( $result );
		}

		if ( ! $result ) {
			return false;
		}

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );

		return true;
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
				   last_notification,
				   room_host,
				   basket_change,
				   sync_state,
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
				$row->last_notification,
				$row->room_host,
				$row->basket_change,
				$row->sync_state,
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
				'cart_id'           => $woocommerceroomsyncobj->get_cart_id(),
				'room_name'         => $woocommerceroomsyncobj->get_room_name(),
				'timestamp'         => $woocommerceroomsyncobj->get_timestamp(),
				'last_notification' => $woocommerceroomsyncobj->get_last_notification(),
				'room_host'         => $woocommerceroomsyncobj->is_room_host(),
				'basket_change'     => $woocommerceroomsyncobj->get_basket_change(),
				'sync_state'        => $woocommerceroomsyncobj->get_sync_state(),
				'current_master'    => $woocommerceroomsyncobj->is_current_master(),
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
