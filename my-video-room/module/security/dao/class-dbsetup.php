<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries - Configures Modules.
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\Module\Security\DAO;

use MyVideoRoomPlugin\Module\Security\Security;

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
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$table_name = $wpdb->prefix . Security::TABLE_NAME_SECURITY_CONFIG;

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (
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

		return maybe_create_table( $table_name, $sql_create );
	}
}
