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
	return $module->get_admin_page();
};
