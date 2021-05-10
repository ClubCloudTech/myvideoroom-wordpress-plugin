<?php
/**
 * Get versioning information
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Version
 */
class Version {
	/**
	 * Get the current version of the installed plugin
	 * Used for cache-busting.
	 *
	 * @return string
	 */
	public function get_plugin_version(): string {
		$plugin_data = get_plugin_data( __DIR__ . '/../index.php' );

		return $plugin_data['Version'];
	}
}
