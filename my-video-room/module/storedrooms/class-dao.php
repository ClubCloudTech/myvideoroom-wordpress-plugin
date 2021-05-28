<?php
/**
 * Handles activation and deactivation
 *
 * @package MyVideoRoomPlugin\Module\StoredRooms
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\StoredRooms;

use MyVideoRoomPlugin\Plugin;

/**
 * Class Activatio
 */
class Dao {

	/**
	 * Create the table
	 */
	public function create_table() {
		$table_name = $this->get_table_name();
		$sql        = <<<SQL
		CREATE TABLE IF NOT EXISTS `${table_name}` (
			`id` CHAR(6) NOT NULL,
			`room_name` VARCHAR(255) NULL,
			`layout_id` VARCHAR(50) NULL,
			`guest_floorplan_enabled` BOOLEAN DEFAULT 0,
			`guest_reception_enabled` BOOLEAN DEFAULT 0,
			`reception_enabled` BOOLEAN DEFAULT 0,
			`reception_id` VARCHAR(50) NULL,	
			`reception_url` VARCHAR(255) NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		\dbDelta( $sql );
	}

	/**
	 * Get the table name
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . Plugin::PLUGIN_NAMESPACE . '_stored_rooms';
	}

	/**
	 * Drop the table
	 */
	public function drop_table() {
		global $wpdb;

		$table_name = $this->get_table_name();

		//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `${table_name}`" );
		\wp_cache_flush();
	}
}
