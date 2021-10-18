<?php
/**
 * Renders The Room Template Browser
 *
 * @package MyVideoRoomPlugin\Views
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\HTML;

/**
 * Show the available layouts and receptions
 *
 * @param array $available_layouts    The list of available layouts.
 * @param array $available_receptions The list of available receptions.
 */

return function (
	array $available_layouts = array(),
	array $available_receptions = array()
): string {
	$html_lib = Factory::get_instance( HTML::class, array( 'template-browser' ) );

	\ob_start();
	
};
