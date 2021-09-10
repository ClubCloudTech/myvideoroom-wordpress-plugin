<?php
/**
 * Handles activation and deactivation
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\BuddyPress\Dao;

use MyVideoRoomPlugin\Module\BuddyPress\Entity\Settings;
use MyVideoRoomPlugin\Plugin;

/**
 * Class Activation
 */
class BuddyPressDao {

	/**
	 * Create the table
	 */
	public function create_table() {
		$table_name = $this->get_table_name();
		$sql        = <<<SQL
		CREATE TABLE IF NOT EXISTS `${table_name}` (
			`record_id` int NOT NULL,
			`restrict_group_to_members_enabled` CHAR(25) NULL,
			`bp_friends_setting` CHAR(25) NULL,
			PRIMARY KEY (`record_id`)
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

		return $wpdb->prefix . Plugin::PLUGIN_NAMESPACE . '_buddypress';
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
	 * Get settings by id
	 *
	 * @param int $id The id.
	 *
	 * @return ?\MyVideoRoomPlugin\Module\BuddyPress\Settings
	 */
	public function get_by_id( int $id ): ?Settings {
		global $wpdb;

		$result = \wp_cache_get( $id, $this->get_cache_group() );

		if ( false === $result ) {
			$table_name = $this->get_table_name();

			//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$result = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT restrict_group_to_members_enabled, bp_friends_setting FROM `${table_name}` WHERE record_id = %s",
					$id
				),
				'array'
			);
			\wp_cache_set( $id, $result, $this->get_cache_group() );
		}

		if ( $result ) {
			return new Settings(
				$id,
				$result['restrict_group_to_members_enabled'],
				$result['bp_friends_setting']
			);
		}

		return null;
	}

	/**
	 * Get the cache key
	 */
	private function get_cache_group(): string {
		return Plugin::PLUGIN_NAMESPACE . '_buddypress';
	}

	/**
	 * Save the settings
	 *
	 * @param Settings $settings The settings to insert or update.
	 *
	 * @return Settings
	 */
	public function persist( Settings $settings ): Settings {
		global $wpdb;

		//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->replace(
			$this->get_table_name(),
			array(
				'record_id'                         => $settings->get_id(),
				'restrict_group_to_members_enabled' => $settings->get_member_restriction(),
				'bp_friends_setting'                => $settings->get_friend_restriction(),
			),
			array(
				'record_id'                         => '%d',
				'restrict_group_to_members_enabled' => '%s',
				'bp_friends_setting'                => '%s',
			)
		);

		\wp_cache_set(
			$settings->get_id(),
			array(
				'restrict_group_to_members_enabled' => $settings->get_member_restriction(),
				'bp_friends_setting'                => $settings->get_friend_restriction(),
			),
			$this->get_cache_group()
		);

		return $settings;
	}

}
