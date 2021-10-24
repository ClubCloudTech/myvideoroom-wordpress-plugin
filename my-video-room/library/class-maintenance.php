<?php
/**
 * Maintenance and Scheduled Operations.
 *
 * @package my-video-room/library/class-maintenance.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class RoomAdmin
 */
class Maintenance {

	const OPTION_DB_CLEAN_SYNC = 'myvideoroom-db-sync-cleanup';
	const OPTION_WC_CLEAN_SYNC = 'myvideoroom-wc-sync-cleanup';


	/**
	 * Activate.
	 * @return string
	 */
	public function activate() {
		\update_option( self::OPTION_DB_CLEAN_SYNC, 14 );
		\update_option( self::OPTION_WC_CLEAN_SYNC, 14 );

	}

	/**
	 * Prune Cart Sync Table.
	 *
	 * @param string $room_name The name of the room.
	 * @param bool   $slug_only return just slug.
	 *
	 * @return string
	 */
	public function prune_cart_sync_table() {
	//$timestamp_tolerance = 

	}

}
