<?php
/**
 * Outputs Formatted Table for Site Video and Other Applications
 *
 * @package my-video-room/module/sitevideo/views/shared/table-output.php
 */

/**
 * Render the admin page
 *
 * @param array   $room_list        The list of rooms.
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	array $room_list,
	string $room_type = null,
	bool $shortcode = null,
	int $offset = null
): string {
	ob_start();
	?>

	<?php
	if ( $room_list && ! $shortcode ) {
		if ( ! $room_type ) {
			$room_type = 'main';
		}
		?>
	<table id="mvr-table-basket-frame_<?php echo esc_attr( $room_type ); ?>" class="wp-list-table widefat plugins myvideoroom-table-adjust">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Page Name', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Page URL', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Shortcode', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Reception', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Actions', 'myvideoroom' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$room_item_render = require __DIR__ . '/../admin/room-item.php';
			foreach ( $room_list as $room ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $room_item_render( $room, $room_type, $offset );
			}
			?>
		</tbody>
	</table>
		<?php
	} elseif ( $room_list && true === $shortcode ) {
		?>
		<table  id="mvr-table-basket-frame_site-conference-room" data-type="frontend" class="wp-list-table widefat plugins myvideoroom-table-adjust">
			<thead>
				<tr class="myvideoroom-hide-mobile">
					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Room Name', 'myvideoroom' ); ?>
					</th>
					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Customer URL', 'myvideoroom' ); ?>
					</th>
					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Status', 'myvideoroom' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Actions', 'myvideoroom' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$room_item_render = require __DIR__ . '/../shortcode/room-item.php';
			foreach ( $room_list as $room ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $room_item_render( $room, $room_type );
			}
			?>
		</tbody>
	</table>
				<?php

	} else {
		?>
	<p>
		<?php
			printf(
			/* translators: %s is the text "Add new room" */
				esc_html__(
					'You don\'t current have any rooms. Please click on "%s" above to get started',
					'myvideoroom'
				),
				esc_html__( 'Add new room', 'myvideoroom' ),
			)
		?>
	</p>
		<?php
	}
	return ob_get_clean();
};
