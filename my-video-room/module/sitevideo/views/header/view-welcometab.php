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
return function (): string {
	return Factory::get_instance( SectionTemplates::class )->welcome_template();
};
