<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser as VisualiserShortcodeRoomVisualiser;


return function (): string {

	ob_start();
	?>
	<h2><?php esc_html_e( 'Visual Room Builder', 'myvideoroom' ); ?></h2>

	<?php
    // phpcs:ignore -- Visualiser worker generates content and is output safely at its level.
    echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->visualiser_worker( 'Your Room' );
	?>
	<?php

	return ob_get_clean();
};
