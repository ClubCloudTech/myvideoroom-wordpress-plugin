<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Modules\BuddyPress;

use MyVideoRoomPlugin\Modules\Plugable;

/**
 * Class Plugin
 */
class Plugin implements Plugable {

	/**
	 * Get the plugin name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'BuddyPress';
	}

	/**
	 * Is the plugin available
	 *
	 * @return bool
	 */
	public function is_available(): bool {
		return true;
	}

	/**
	 * Is the plugin installed
	 *
	 * @return bool
	 */
	public function is_installed(): bool {
		return true;
	}

	/**
	 * Is the plugin active
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return false;
	}

	/**
	 * Initialise the plugin
	 */
	public function init(): void {

	}

	/**
	 * Activate the plugin
	 *
	 * @return bool
	 */
	public function activate(): bool {
		return true;
	}

	/**
	 * Deactivate the plugin
	 *
	 * @return bool
	 */
	public function deactivate(): bool {
		return true;
	}

	/**
	 * Create the admin settings page
	 *
	 * @return string
	 */
	public function create_admin_settings(): string {
		ob_start();

		?>
		<h2>BuddyPress</h2>
		<p>This is a settings page for BuddyPress</p>
		<?php

		return ob_get_clean();
	}
}
