<?php
/**
 * Get details about the modules installed into the plugin
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Modules\BuddyPress\Plugin as BuddyPress;
use MyVideoRoomPlugin\Modules\Plugable;

/**
 * Class Modules
 */
class Modules {


	/**
	 * Get all available My Video Room modules
	 *
	 * @return Plugable[]
	 */
	public function get_modules(): array {
		$modules = array(
			'buddypress' => new BuddyPress(),
		);

		return array_filter( $modules, fn( Plugable $module) => $module->is_available() );
	}
}
