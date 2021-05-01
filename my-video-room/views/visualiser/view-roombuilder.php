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
	wp_enqueue_script( 'mvr-frametab' );
	wp_enqueue_style( 'visualiser' );

	ob_start();
	?>
<div class="mvr-outer-box-wrap">
	<table style="width:100%">
		<tr>
			<th class="mvr-header-table">
				<h1 class="mvr-heading-head-top"><?php esc_html_e( 'Visual Room Builder', 'myvideoroom' ); ?></h1>
			</th>
		</tr>
	</table>
</div>
<div id="visualiser-tab" class="mvr-tab-align">
						<?php
						// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
						echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->visualiser_worker( 'Your Room' );
						?>
</div>
	<?php

	return ob_get_clean();
};
