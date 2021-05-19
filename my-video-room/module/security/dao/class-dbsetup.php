<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries - Configures Modules.
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\Module\Security\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\DAO\ModuleConfig;

/**
 * Class ModuleConfig
 */
class DBSetup {
	/**
	 * Install Module Security Config Table.
	 *
	 * @return bool
	 */
	public static function install_security_config_table(): bool {
		global $wpdb;
		$table_name = Security::TABLE_NAME_SECURITY_CONFIG;

		// Check if Exists.
		if ( Factory::get_instance( ModuleConfig::class )->check_table_exists( $table_name ) ) {
			return true;
		}

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . $table_name . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`user_id` BIGINT NOT NULL,
			`room_name` VARCHAR(255) NOT NULL,
			`room_disabled` BOOLEAN,
			`anonymous_enabled` BOOLEAN,
			`allow_role_control_enabled` BOOLEAN,
			`block_role_control_enabled` BOOLEAN,
			`site_override_enabled` BOOLEAN,
			`restrict_group_to_members_enabled` VARCHAR(255) NULL,
			`allowed_roles` VARCHAR(255) NULL,
			`blocked_roles` VARCHAR(255) NULL,
			`allowed_users` VARCHAR(255) NULL,
			`blocked_users` VARCHAR(255) NULL,
			`bp_friends_setting` VARCHAR(255) NULL,
			`allowed_template_id` BIGINT UNSIGNED NULL,
			`blocked_template_id` BIGINT UNSIGNED NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		\dbDelta( $sql );

		return true;
	}
}
