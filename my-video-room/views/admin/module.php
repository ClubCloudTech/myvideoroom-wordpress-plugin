<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Module\Module;

/**
 * Render the admin page
 *
 * @param Module $module The module to render
 */
return function (
	Module $module
): string {

	\ob_start();
	?>
	<h2><?php echo \esc_html( $module->get_name() ); ?></h2>

	<?php

	//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --We want to render the HTML output from the module.
	echo $module->get_admin_page();

	return \ob_get_clean();
};
