<?php
/**
 * Setup Functions - Database Objects for Setup
 *
 * @package my-video-room/dao/class-setup.php
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class Setup
 * Installs Main Plugin Tables, and Base Settings.
 */
class Setup {

	/**
	 * Install_user_video_preference_table - this is the main table for all User Room Config
	 *
	 * @return ?string
	 */
	public static function install_user_video_preference_table(): ?string {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$table_name = $wpdb->prefix . SiteDefaults::TABLE_NAME_USER_VIDEO_PREFERENCE;

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`user_id` BIGINT NOT NULL,
			`room_name` VARCHAR(255) NOT NULL,
			`layout_id` VARCHAR(255) NULL,
			`reception_id` VARCHAR(255) NULL,
			`reception_enabled` BOOLEAN,
			`reception_video_enabled` BOOLEAN,
			`reception_video_url` VARCHAR(255) NULL,
			`show_floorplan` BOOLEAN,
			`timestamp` BIGINT UNSIGNED NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		$record = \maybe_create_table( $table_name, $sql_create );
		if ( $record ) {
			return $wpdb->last_error;
		} else {
			return null;
		}
	}

	/**
	 * Install Room Mapping Config Table - Create Table for Mapping Meet/Go and Other Plugin Rooms to WP post IDs.
	 *
	 * @return bool
	 */
	public static function install_room_post_mapping_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$table_name = $wpdb->prefix . SiteDefaults::TABLE_NAME_ROOM_MAP;

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`room_name` VARCHAR(255) NOT NULL,
			`post_id` BIGINT UNSIGNED NOT NULL,
			`room_type` VARCHAR(255) NOT NULL,
			`shortcode` VARCHAR(255) NULL,
			`display_name` VARCHAR(255) NOT NULL,
			`slug` VARCHAR(255) NOT NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		return \maybe_create_table( $table_name, $sql_create );
	}

	/**
	 * Install Module Config Table.
	 *
	 * @return bool
	 */
	public static function install_module_config_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$table_name = $wpdb->prefix . SiteDefaults::TABLE_NAME_MODULE_CONFIG;

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (
			`module_id` BIGINT UNSIGNED NOT NULL,
			`module_name` VARCHAR(255) NOT NULL,
			`module_enabled` BOOLEAN,
			`module_status` VARCHAR(255) NULL,
			`module_param` VARCHAR(255) NULL,
			`module_has_admin_page` BOOLEAN,
			PRIMARY KEY (`module_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		return \maybe_create_table( $table_name, $sql_create );
	}

	/**
	 * Install SessionState Sync Config Table.
	 *
	 * @return bool
	 */
	public function install_room_presence_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name = $wpdb->prefix . SiteDefaults::TABLE_NAME_ROOM_PRESENCE;

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`cart_id` VARCHAR(255) NOT NULL,
			`room_name` VARCHAR(255) NOT NULL,
			`timestamp` BIGINT UNSIGNED NULL,
			`last_notification` BIGINT UNSIGNED NULL,
			`room_host` BOOLEAN,
			`basket_change` VARCHAR(255) NULL,
			`sync_state` VARCHAR(255) NULL,
			`current_master` BOOLEAN,
			`owner_id` BIGINT UNSIGNED NULL,
			`user_picture_url` VARCHAR(255) NULL,
			`user_display_name` VARCHAR(255) NULL,
			`user_picture_path` VARCHAR(255) NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		return \maybe_create_table( $table_name, $sql_create );
	}

	/**
	 * Initialise_default_video_settings - adds default settings to the main room table on Plugin setup.
	 *
	 * @return bool
	 */
	public function initialise_default_video_settings(): bool {

		// Check default doesn't already exist.
		$record = Factory::get_instance( UserVideoPreferenceDao::class )->get_by_id(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( ! $record ) {
			$current_user_setting = new UserVideoPreferenceEntity(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				SiteDefaults::ROOM_NAME_SITE_DEFAULT,
				'boardroom',
				'default',
				false,
			);
			Factory::get_instance( UserVideoPreferenceDao::class )->create( $current_user_setting );
		}

		return true;
	}
}

