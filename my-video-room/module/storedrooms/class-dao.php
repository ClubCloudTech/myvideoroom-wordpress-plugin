<?php
/**
 * Handles activation and deactivation
 *
 * @package MyVideoRoomPlugin\Module\StoredRooms
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\StoredRooms;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Plugin;

/**
 * Class Dao
 */
class Dao {

	/**
	 * Create the table
	 */
	public function create_table() {
		$table_name = $this->get_table_name();
		$sql        = <<<SQL
		CREATE TABLE IF NOT EXISTS `${table_name}` (
			`id` int NOT NULL AUTO_INCREMENT,
			`room_name` VARCHAR(255) NULL,
			`layout_id` VARCHAR(50) NULL,
			`guest_floorplan_enabled` BOOLEAN DEFAULT 0,
			`guest_reception_enabled` BOOLEAN DEFAULT 0,
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

	/**
	 * Get a stored app shortcode by id.
	 *
	 * @param string $id The id of the shortcode to fetch.
	 *
	 * @return ?StoredAppShortcode
	 */
	public function get_by_id( string $id ): ?StoredAppShortcode {
		global $wpdb;

		$table_name = $this->get_table_name();

		$id_to_hash = Factory::get_instance( IdToHash::class );

		$result = $wpdb->get_row(
			$wpdb->prepare(
				"
					SELECT 
						id,
						room_name, 
						layout_id,
						guest_floorplan_enabled,
						guest_reception_enabled,
						reception_id,
						reception_url
					FROM `${table_name}` 
					WHERE id = %d
					",
				$id_to_hash->get_id_from_hash( $id )
			),
			'ARRAY_A'
		);

		if ( $result ) {
			$id_to_hash           = Factory::get_instance( IdToHash::class );
			$stored_app_shortcode = new StoredAppShortcode( $id_to_hash->get_hash_from_id( (int) $result['id'] ) );

			if ( $result['room_name'] ) {
				$stored_app_shortcode->set_name( $result['room_name'] );
			}

			if ( $result['layout_id'] ) {
				$stored_app_shortcode->set_layout( $result['layout_id'] );
			}

			if ( $result['guest_floorplan_enabled'] ) {
				$stored_app_shortcode->enable_floorplan();
			} else {
				$stored_app_shortcode->disable_floorplan();
			}

			if ( $result['guest_reception_enabled'] ) {
				$stored_app_shortcode->enable_reception();
			} else {
				$stored_app_shortcode->disable_reception();
			}

			if ( $result['reception_id'] ) {
				$stored_app_shortcode->set_reception_id( $result['reception_id'] );
			}

			if ( $result['reception_url'] ) {
				$stored_app_shortcode->set_reception_video_url( $result['reception_url'] );
			}

			return $stored_app_shortcode;
		}

		return null;
	}

	/**
	 * Persist the room
	 *
	 * @param StoredAppShortcode $stored_app_shortcode The app shortcode to persist.
	 *
	 * @return StoredAppShortcode
	 */
	public function persist( StoredAppShortcode $stored_app_shortcode ): StoredAppShortcode {
		global $wpdb;

		$id_to_hash = Factory::get_instance( IdToHash::class );

		$id = null;
		if ( $stored_app_shortcode->get_id() ) {
			$id = $id_to_hash->get_id_from_hash( $stored_app_shortcode->get_id() );
		}

		$wpdb->replace(
			$this->get_table_name(),
			array(
				'id'                      => $id,
				'room_name'               => $stored_app_shortcode->get_name(),
				'layout_id'               => $stored_app_shortcode->get_layout(),
				'guest_floorplan_enabled' => (int) $stored_app_shortcode->is_floorplan_enabled(),
				'guest_reception_enabled' => (int) $stored_app_shortcode->is_reception_enabled(),
				'reception_id'            => $stored_app_shortcode->get_reception_id(),
				'reception_url'           => $stored_app_shortcode->get_reception_video(),
			),
			array(
				'id'                      => '%d',
				'room_name'               => '%s',
				'layout_id'               => '%s',
				'guest_floorplan_enabled' => '%d',
				'guest_reception_enabled' => '%d',
				'reception_id'            => '%s',
				'reception_url'           => '%s',
			)
		);


		if ( ! $stored_app_shortcode->get_id() ) {
			$stored_app_shortcode->set_id(
				$id_to_hash->get_hash_from_id( $wpdb->insert_id )
			);
		}

		return $stored_app_shortcode;
	}
}
