<?php
/**
 * Handles activation and deactivation
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\BuddyPress;

use MyVideoRoomPlugin\Factory;

/**
 * Class Activation
 */
class Activation {

	/**
	 * Activate the module
	 * - creates the table
	 */
	public function activate() {
		Factory::get_instance( Dao::class )->create_table();
	}

	/**
	 * Deactivate the module
	 * - drops the table
	 */
	public function deactivate() {
		Factory::get_instance( Dao::class )->drop_table();
	}
}
