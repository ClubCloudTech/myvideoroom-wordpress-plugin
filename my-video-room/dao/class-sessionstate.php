<?php
/**
 * Data Access Object for controlling Room Session State Database Entries
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class SessionState
 * Registers Rooms Permanently in Database - base for WCBookings, Meet Center, Site Video.
 */
class SessionState {

	const PAGE_STATUS_EXISTS     = 'page-exists';
	const PAGE_STATUS_NOT_EXISTS = 'page-not-exists';
	const PAGE_STATUS_ORPHANED   = 'page-not-exists-but-has-reference';



	/**
	 * Get the table name for Room Presence Table DAO.
	 *
	 * @return string
	 */
	private function get_room_presence_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . SiteDefaults::TABLE_NAME_ROOM_PRESENCE;
	}


}
