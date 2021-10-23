<?php
/**
 * Data Access Object for Room Attendee Sync
 *
 * @package my-video-room/dao/class-roomsyncdao.php
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\DAO\Setup;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Entity\RoomSync;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class RoomSyncDAO
 */
class RoomSyncDAO {

	/**
	 * Get the table name for Room Presence Table DAO.
	 *
	 * @return string
	 */
	private function get_room_presence_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . SiteDefaults::TABLE_NAME_ROOM_PRESENCE;
	}

	/**
	 * Save a Room Sync Event into the database
	 *
	 * @param RoomSync $roomsyncobj The video preference to save.
	 *
	 * @return RoomSync|null
	 * @throws \Exception When failing to insert, most likely a duplicate key.
	 */
	public function create( RoomSync $roomsyncobj ): ?RoomSync {
		global $wpdb;

		$cart_id   = $roomsyncobj->get_cart_id();
		$room_name = $roomsyncobj->get_room_name();

		// Check Record Doesn't already exist (update not create if it does).

		$check = $this->get_by_id_sync_table( $cart_id, $room_name );

		if ( $check ) {
			return $this->update( $roomsyncobj );
		}

		$cache_key = $this->create_cache_key(
			$cart_id,
			$room_name
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$this->get_room_presence_table_name(),
			array(
				'cart_id'           => $roomsyncobj->get_cart_id(),
				'room_name'         => $roomsyncobj->get_room_name(),
				'timestamp'         => $roomsyncobj->get_timestamp(),
				'last_notification' => $roomsyncobj->get_last_notification(),
				'room_host'         => $roomsyncobj->is_room_host(),
				'basket_change'     => $roomsyncobj->get_basket_change(),
				'sync_state'        => $roomsyncobj->get_sync_state(),
				'current_master'    => $roomsyncobj->is_current_master(),
				'owner_id'          => $roomsyncobj->get_owner_id(),
				'user_picture_url'  => $roomsyncobj->get_user_picture_url(),
				'user_display_name' => $roomsyncobj->get_user_display_name(),
				'user_picture_path' => $roomsyncobj->get_user_picture_path(),
			)
		);

		$roomsyncobj->set_id( $wpdb->insert_id );

		\wp_cache_set(
			$cache_key,
			$roomsyncobj->to_json(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_id_sync_table',
				)
			)
		);
		\wp_cache_delete(
			$roomsyncobj->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return $roomsyncobj;
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
	 * @return RoomSync[]
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
	 * @return RoomSync[]
	 */
	public function get_room_participants( string $room_name ) {
		global $wpdb;
		/*phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested*/
		$timestamp    = \current_time( 'timestamp' );
		$allowed_time = $timestamp - SiteDefaults::LAST_VISITED_TOLERANCE;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching 
			$participants = $wpdb->get_results(
				$wpdb->prepare(
					'
						SELECT cart_id, room_name, timestamp, room_host, current_master, basket_change, record_id, owner_id, user_picture_url, user_display_name, user_picture_path
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
	 * @return RoomSync[]
	 */
	public function get_room_hosts_from_db( string $room_name ) {
		global $wpdb;
		/*phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested*/
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

		if ( $wpdb->last_error ) {
			Factory::get_instance( Setup::class )->install_room_presence_table();
		}

		return $participants;
	}

	/**
	 * Get Room Master
	 *
	 * @param string $room_name - The Room Name to Return Recipients for.
	 *
	 * @return RoomSync[]
	 */
	public function get_room_masters( string $room_name ) {
		global $wpdb;

		// Can't cache as query involves time.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$participants = $wpdb->get_results(
				$wpdb->prepare(
					'
						SELECT cart_id, room_name, timestamp, room_host, current_master, record_id, sync_state, owner_id
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

		// Flush other sync requests.
		$this->flush_sync_state( $room_name );

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );
		\wp_cache_delete( $cart_id, __CLASS__ . '::get_room_info' );

		return true;
	}

	/**
	 * Update Master Status in Database.
	 *
	 * @param string $room_name The Room Name.
	 *
	 * @return bool|null
	 */
	public function flush_sync_state( string $room_name ): ?bool {
		global $wpdb;

		// Flush all Update Requests.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET sync_state = 0
					WHERE room_name = %s
				',
				$room_name,
			)
		);
		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );

		return true;
	}

	/**
	 * Notify User.
	 *
	 * @param string $room_name The Room Name.
	 * @param string $user_hash_id - User hash to check for.
	 *
	 * @return bool|null
	 */
	public function notify_user( string $room_name, string $user_hash_id = null ): ?bool {
		global $wpdb;
		//phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$timestamp = current_time( 'timestamp' );

		// Try to Update First.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET last_notification = %d
					WHERE cart_id = %s AND room_name = %s
				',
				$timestamp,
				$user_hash_id,
				$room_name,
			)
		);

		\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );
		if ( $result ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Update Timestamp
	 *
	 * @param string $room_name The Room Name.
	 * @param string $user_hash_id - User hash to check for.
	 *
	 * @return bool
	 */
	public function reset_timestamp( string $room_name, string $user_hash_id = null ): bool {
		global $wpdb;
		//phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$timestamp = current_time( 'timestamp' );
		if ( ! $user_hash_id ) {
			$user_hash_id = Factory::get_instance( RoomAdmin::class )->get_user_session();
		}

		// Try to Update First.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->query(
			$wpdb->prepare(
				'
					UPDATE ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
					SET timestamp = %d
					WHERE cart_id = %s AND room_name = %s
				',
				$timestamp,
				$user_hash_id,
				$room_name,
			)
		);
		if ( $result ) {
			\wp_cache_delete( $room_name, __CLASS__ . '::get_by_id_sync_table' );
			return true;
		} else {
			return false;
		}

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
		//phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
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
			$result = new RoomSync(
				WooCommerce::SETTING_BASKET_REQUEST_USER,
				$room_name,
				/*phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested*/
				current_time( 'timestamp' ),
				/*phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested*/
				current_time( 'timestamp' ),
				false,
				WooCommerce::SETTING_BASKET_REQUEST_OFF,
				$sync_state,
				false,
				null,
				null,
				null,
				null,
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
	 * @param bool   $host_status - Optional Host Status.
	 *
	 * @return RoomSync|null
	 */
	public function get_by_id_sync_table( string $cart_id, string $room_name, bool $host_status = null ): ?RoomSync {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$cart_id,
			$room_name
		);

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return RoomSync::from_json( $result );
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
				   record_id,
				   owner_id,
				   user_picture_url,
				   user_display_name,
				   user_picture_path
				   
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_room_presence_table_name() . '
				WHERE cart_id = %s AND room_name = %s;
			',
				array(
					$cart_id,
					$room_name,
				)
			)
		);

		if ( $wpdb->last_error ) {
			$this->repair_update_database( $wpdb->last_error );
		}

		$result = null;

		if ( $row ) {
			$result = new RoomSync(
				$row->cart_id,
				$row->room_name,
				$row->timestamp,
				$row->last_notification,
				$row->room_host,
				$row->basket_change,
				$row->sync_state,
				$row->current_master,
				$row->id,
				$row->owner_id,
				$row->user_picture_url,
				$row->user_display_name,
				$row->user_picture_path
			);
			wp_cache_set( $cache_key, __METHOD__, $result->to_json() );
		} else {
			wp_cache_set( $cache_key, __METHOD__, null );
		}

		return $result;
	}

	/**
	 * Create a User Picture and Name Storage record in DB.
	 *
	 * @param string $cart_id   The Cart id.
	 *
	 * @return RoomSync|null
	 */
	public function create_new_user_storage_record( string $cart_id = null ): ?RoomSync {
		if ( ! $cart_id ) {
			$cart_id = Factory::get_instance( RoomAdmin::class )->get_user_session();
		}
		$room_name      = MVRSiteVideo::USER_STATE_INFO;
		$current_object = new RoomSync(
			$cart_id,
			$room_name,
			/*phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested*/
			current_time( 'timestamp' ),
			/*phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested*/
			current_time( 'timestamp' ),
			false,
			WooCommerce::SETTING_BASKET_REQUEST_OFF,
			null,
			false,
			null,
			null,
			null,
			null,
			null
		);
		$success = $this->create( $current_object );
		if ( $success ) {
			return $current_object;
		} else {
			return null;
		}
	}

	/**
	 * Update a Cart Object into the database
	 *
	 * @param RoomSync $roomsyncobj The updated Cart Object.
	 *
	 * @return RoomSync|null
	 * @throws \Exception When failing to update.
	 */
	public function update( RoomSync $roomsyncobj ): ?RoomSync {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$roomsyncobj->get_cart_id(),
			$roomsyncobj->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$this->get_room_presence_table_name(),
			array(
				'cart_id'           => $roomsyncobj->get_cart_id(),
				'room_name'         => $roomsyncobj->get_room_name(),
				'timestamp'         => $roomsyncobj->get_timestamp(),
				'last_notification' => $roomsyncobj->get_last_notification(),
				'room_host'         => $roomsyncobj->is_room_host(),
				'basket_change'     => $roomsyncobj->get_basket_change(),
				'sync_state'        => $roomsyncobj->get_sync_state(),
				'current_master'    => $roomsyncobj->is_current_master(),
				'owner_id'          => $roomsyncobj->get_owner_id(),
				'user_picture_url'  => $roomsyncobj->get_user_picture_url(),
				'user_display_name' => $roomsyncobj->get_user_display_name(),
				'user_picture_path' => $roomsyncobj->get_user_picture_path(),
			),
			array(
				'cart_id'   => $roomsyncobj->get_cart_id(),
				'room_name' => $roomsyncobj->get_room_name(),
			)
		);

		\wp_cache_set(
			$cache_key,
			$roomsyncobj->to_json(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_id_sync_table',
				)
			)
		);
		\wp_cache_delete(
			$roomsyncobj->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return $roomsyncobj;
	}

	/**
	 * Delete a Cart Object from the database
	 *
	 * @param RoomSync $roomsyncobj The Cart Object to delete.
	 *
	 * @return null
	 * @throws \Exception When failing to delete.
	 */
	public function delete( RoomSync $roomsyncobj ) {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$roomsyncobj->get_cart_id(),
			$roomsyncobj->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$this->get_room_presence_table_name(),
			array(
				'cart_id'   => $roomsyncobj->get_cart_id(),
				'room_name' => $roomsyncobj->get_room_name(),
			)
		);

		\wp_cache_delete( $cache_key, implode( '::', array( __CLASS__, 'get_by_id_sync_table' ) ) );
		\wp_cache_delete(
			$roomsyncobj->get_cart_id(),
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

	/**
	 * Database Restore and Update
	 *
	 * @param string $db_error_message   The Error Message.
	 *
	 * @return bool
	 */
	private function repair_update_database( string $db_error_message = null ): bool {
		global $wpdb;

		// Case Table Mising Column.
		if ( strpos( $db_error_message, 'Unknown column' ) !== false ) {
			// Update Database to new Schema.

			$table_name = $this->get_room_presence_table_name();
			// V2.
			$update_db = "ALTER TABLE `{$table_name}` ADD `user_picture_url` VARCHAR(255) NULL AFTER `owner_id`, ADD `user_display_name` VARCHAR(255) NULL AFTER `user_picture_url`";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
			$wpdb->query( $wpdb->prepare( $update_db ) );

			// V3.
			$update_db = "ALTER TABLE `{$table_name}` ADD `user_picture_path` VARCHAR(255) NULL AFTER `user_display_name`; ";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
			$wpdb->query( $wpdb->prepare( $update_db ) );
			return true;
		}

		// Case Table Delete.
		$table_message = $this->get_room_presence_table_name() . '\' doesn\'t exist';
		if ( strpos( $db_error_message, $table_message ) !== false ) {
			// Recreate Table.
			Factory::get_instance( Setup::class )->install_room_presence_table();

			return true;
		}
	}
}
