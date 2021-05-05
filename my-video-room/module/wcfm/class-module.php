<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WCFM;

use MyVideoRoomPlugin\Module\Plugable;

/**
 * Class Module
 */
class Module implements Plugable {
	/**
	 * Is the plugin compatible
	 *
	 * @return bool
	 */
	public function is_compatible(): bool {
		return true;
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
		<p>This is a settings page</p>
		<?php

		return ob_get_clean();
	}
}
