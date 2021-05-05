<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Module\Module;

/**
 * Render the admin page
 *
 * @param Module $module The module to render
 */
return function (
	Module $module
): string {

	ob_start();
	?>
	<h2><?php echo esc_html( $module->get_name() ); ?></h2>

	<?php

    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $module->get_instance()->create_admin_settings();

	return ob_get_clean();
};
