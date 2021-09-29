<?php
/**
 * Data Access Object for controlling Room Session State Database Entries
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Entity\RoomSync as RoomSyncEntity;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class SessionState
 * Registers Rooms Permanently in Database - base for WCBookings, Meet Center, Site Video.
 */
class SessionState {

	/**
	 * Register Room Presence
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status - If User is Host.
	 * @param int    $room_id      - Room ID.
	 * @return void
	 */
	public function register_room_presence( string $room_name, bool $host_status, int $room_id ) {
		// Setup.
		$cart_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$timestamp    = \current_time( 'timestamp' );

		$current_record = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $cart_session, $room_name, $host_status );

		if ( $current_record ) {
			$current_record->set_timestamp( $timestamp );
			$current_record->set_room_host( $host_status );
			$current_record->set_owner_id( $room_id );

		} else {
			$current_record = new RoomSyncEntity(
				$cart_session,
				$room_name,
				$timestamp,
				$timestamp,
				$host_status,
				null,
				null,
				$host_status,
				null,
				$room_id
			);
			// Set Last Notification Timestamp for new room.
			$this->notify_user( $room_name );
		}
		Factory::get_instance( RoomSyncDAO::class )->create( $current_record );

	}

	/**
	 * Flag for Notification
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $hash_id - User Hash to match. (optional) - will update master record if nothing received.
	 * @return bool
	 */
	public function notify_user( string $room_name, string $hash_id = null ): bool {

		if ( ! $hash_id ) {
			$hash_id = WooCommerce::SETTING_BASKET_REQUEST_USER;
		}

		// Change State.
		Factory::get_instance( RoomSyncDAO::class )->notify_user( $room_name, $hash_id );

		return false;

	}


}
