<?php
/**
 * Shows the user the room deleted message
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

return function (): string {
	ob_start();
	?>

	<span class="page-deleted">
		<?php esc_html_e( 'The page was successfully deleted', 'myvideoroom' ); ?>
	</span>

	<?php
	return ob_get_clean();
};
