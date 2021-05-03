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
	<div class="myvideoroom-outer-box-wrap">
		<table style="width:100%">
			<tr>
				<th class="myvideoroom-header-table">
					<h1 class="myvideoroom-heading-head-top"><?php esc_html_e( 'Visual Room Builder', 'myvideoroom' ); ?></h1>
				</th>
			</tr>
		</table>
	</div>

	<div id="visualiser-tab" class="myvideoroom-tab-align">
		<?php
        // phpcs:ignore -- Visualiser worker generates content and is output safely at its level.
        echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->visualiser_worker( 'Your Room' );
		?>
	</div>
	<?php

	return ob_get_clean();
};
