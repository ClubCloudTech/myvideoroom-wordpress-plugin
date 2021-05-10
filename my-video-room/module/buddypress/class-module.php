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
class Module {

	/**
	 * Module constructor.
	 */
	public function __construct() {}

	/**
	 * Create the admin settings page
	 *
	 * @return string
	 */
	public static function create_admin_settings(): string {
		ob_start();

		?>

		<p>
			<?php esc_html_e( 'The module is not yet ready. Check back soon!', 'myvideoroom' ); ?>
		</p>

		<?php

		return ob_get_clean();
	}
}
