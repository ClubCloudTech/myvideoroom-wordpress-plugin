<?php
/**
 * Outputs the Welcome Tab
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Header\WelcomeTab.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\SectionTemplates;

/**
 * Render the Outputs the Welcome Tab
 *
 * @param array   $tabs          Any Tabs to Display.
 * @param ?string $html_library  Randomizing Data for Tabs.
 * @param bool    $host_status   Whether user is host.
 * @param ?string $header        Data Object.
 *
 * @return string
 */
return function (
	string $room_name = null
	): string {
	ob_start();
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
			echo Factory::get_instance( SectionTemplates::class )->welcome_template( $room_name ); 

	return ob_get_clean();
};
