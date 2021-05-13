<?php
/**
 * Render an error for the room builder page
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 *
 * @return string
 */

/**
 * Show an error message
 *
 * @return string
 */
return function (): string {
	ob_start();
	?>

	<p>
		<?php
		esc_html_e(
			'Something went wrong generating the preview, please reload the page and try again',
			'myvideoroom'
		);
		?>
	</p>

	<?php

	return ob_get_clean();
};
