<?php
/**
 * Data Access Object for controlling Room Session State Database Entries
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Entity\RoomSync as RoomSyncEntity;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;

/**
 * Class SessionState
 * Registers Rooms Permanently in Database - base for WCBookings, Meet Center, Site Video.
 */
class SessionState {

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
			Factory::get_instance( HostManagement::class )->notify_user( $room_name );
		}
		Factory::get_instance( RoomSyncDAO::class )->create( $current_record );

	}

}
