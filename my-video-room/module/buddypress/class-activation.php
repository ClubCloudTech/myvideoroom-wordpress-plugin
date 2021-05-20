<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\BuddyPress;

/**
 * Class Module
 */
class Activation {
	/**
	 * Module constructor.
	 */
	public function activate() {
		global $wpdb;

		// Create Main Table for Room Config.
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'myvideoroom_buddypress` (
                           `record_id` int NOT NULL AUTO_INCREMENT,
							`restrict_group_to_members_enabled` BOOLEAN,
							`bp_friends_setting` VARCHAR(255) NULL,
                           PRIMARY KEY (`record_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		\dbDelta( $sql );

		return true;
	}


}
