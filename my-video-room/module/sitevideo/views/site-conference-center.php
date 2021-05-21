<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Render the admin page
 *
 * @param array   $room_list        The list of rooms.
 * @param ?string $details_section  Optional details section.
 *
 * @return string
 */
return function (
	array $room_list,
	string $details_section = null
): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	wp_enqueue_script( 'myvideoroom-protect-input' );
	ob_start();
	?>
	<h2><?php esc_html_e( 'Site Conference Center Page', 'my-video-room' ); ?></h2>

	<div aria-label="button" class="button button-primary myvideoroom-sitevideo-add-room-button">
		<i class="dashicons dashicons-plus-alt"></i>
		<?php esc_html_e( 'Add new room', 'my-video-room' ); ?>
	</div>

	<hr />

	<div class="myvideoroom-sitevideo-add-room">
		<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/add-new-room.php')();
		?>
		<hr />
	</div>

	<?php if ( $room_list ) { ?>
		<table class="wp-list-table widefat plugins" >
			<thead>
				<tr>
					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Page Name', 'my-video-room' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Page URL', 'my-video-room' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Shortcode', 'my-video-room' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Actions', 'my-video-room' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php
			$room_item_render = require __DIR__ . '/room-item.php';
			foreach ( $room_list as $room ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $room_item_render( $room );
			}
			?>
			</tbody>
		</table>
	<?php } else { ?>
		<p>
		<?php
			printf(
			/* translators: %s is the text "Add new room" */
				esc_html__(
					'You don\'t current have any site conference rooms. Please click on "%s" above to get started',
					'myvideoroom'
				),
				esc_html__( 'Add new room', 'my-video-room' ),
			)
		?>
		</p>
	<?php } ?>

	<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host"
		data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>"
	>
		<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $details_section;
		?>
	</div>

	<?php
	return ob_get_clean();
};
