<?php
/**
 * Setup Functions - Module Definition File- Config Modules will get initialised here.
 *
 * @package MyVideoRoomPlugin\Setup
 */

namespace MyVideoRoomPlugin\Setup;

use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;

/**
 * Class Module Definition
 * Adds Modules to DB if Needed.
 */
class ModuleDefinition extends SiteDefaults {
	/**
	 * Register Additional Modules in DB If Needed
	 *
	 * @param int    $module_id - registered ID of module.
	 * @param string $module_name - listed Module name.
	 * @param string $initialise_callback_function - function to execute on post initialisation (if any).
	 * @return null
	 */
	public function add_additional_modules_in_db( int $module_id, string $module_name, $initialise_callback_function = null ) {
			// Exit on no input.
		if ( ! $module_id && ! $module_name ) {
			return null;
		}
				Factory::get_instance( ModuleConfig::class )->register_module_in_db( $module_name, $module_id );

			// Initialise.
		if ( $initialise_callback_function ) {
				return $initialise_callback_function;
		}
			return null;
	}
}

