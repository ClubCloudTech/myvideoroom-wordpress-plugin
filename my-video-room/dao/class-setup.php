<?php
/**
 * Setup Functions
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomInit;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\ModuleConfig;

/**
 * Class Setup
 * Installs Main Plugin Tables, and Base Settings.
 */
class Setup {

	/**
	 * Initialise_default_video_settings - adds default settings to the main room table on Plugin setup.
	 *
	 * @return string - message and changes to db.
	 */
	public function initialise_default_video_settings(): string {
		// Site Default - Entire Site.
		Factory::get_instance( RoomInit::class )->room_default_settings_install(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT,
			'boardroom',
			'default',
			false
		);

		return '';
	}

	/**
	 * Install_user_video_preference_table - this is the main table for all User Room Config
	 *
	 * @return bool
	 */
	public static function install_user_video_preference_table(): bool {
		global $wpdb;

		$table_name = SiteDefaults::TABLE_NAME_USER_VIDEO_PREFERENCE;

		// Check if Exists.
		if ( Factory::get_instance( ModuleConfig::class )->check_table_exists( $table_name ) ) {
			return true;
		}

		// Create Main Table for Room Config.
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . $table_name . '` (
                           `record_id` int NOT NULL AUTO_INCREMENT,
						   `user_id` BIGINT NOT NULL,
                           `room_name` VARCHAR(255) NOT NULL,
                           `layout_id` VARCHAR(255) NULL,
                           `reception_id` VARCHAR(255) NULL,
                           `reception_enabled` BOOLEAN,
						   `reception_video_enabled` BOOLEAN,
						   `reception_video_url` VARCHAR(255) NULL,
						   `show_floorplan` BOOLEAN,
                           PRIMARY KEY (`record_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		\dbDelta( $sql );

		return true;
	}

	/**
	 * Install Room Mapping Config Table - Create Table for Mapping Meet/Go and Other Plugin Rooms to WP post IDs.
	 *
	 * @return bool
	 */
	public static function install_room_post_mapping_table(): bool {
		global $wpdb;

		$table_name = SiteDefaults::TABLE_NAME_ROOM_MAP;
		// Check if Exists.
		if ( Factory::get_instance( ModuleConfig::class )->check_table_exists( $table_name ) ) {
			return true;
		}

		$sql2 = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . $table_name . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`room_name` VARCHAR(255) NOT NULL,
			`post_id` BIGINT UNSIGNED NOT NULL,
			`room_type` VARCHAR(255) NOT NULL,
			`shortcode` VARCHAR(255) NULL,
			`display_name` VARCHAR(255) NOT NULL,
			`slug` VARCHAR(255) NOT NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql2 );

		return true;
	}

	/**
	 * Install Module Config Table.
	 *
	 * @return bool
	 */
	public static function install_module_config_table(): bool {
		global $wpdb;
		$table_name = SiteDefaults::TABLE_NAME_MODULE_CONFIG;

		// Check if Exists.
		if ( Factory::get_instance( ModuleConfig::class )->check_table_exists( $table_name ) ) {
			return true;
		}

		// Create Main Table for Module Config.
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . $table_name . '` (
			`module_id` BIGINT UNSIGNED NOT NULL,
			`module_name` VARCHAR(255) NOT NULL,
			`module_enabled` BOOLEAN,
			`module_status` VARCHAR(255) NULL,
			`module_param` VARCHAR(255) NULL,
			`module_has_admin_page` BOOLEAN,
			`module_admin_path` VARCHAR(255) NULL,
			PRIMARY KEY (`module_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		\dbDelta( $sql );
		return true;
	}
}

