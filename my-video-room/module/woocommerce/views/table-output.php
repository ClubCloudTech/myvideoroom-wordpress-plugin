<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Cart
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

/**
 * Render the WooCommerce Basket Render Page
 *
 * @param array   $basket_list        The list of rooms.
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	array $basket_list
): string {
	ob_start();
	?>
<div id="security-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
	<?php
	if ( $basket_list ) {
		?>
	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Product', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Name', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Price', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Quantity', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Actions', 'my-video-room' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$basket_item_render = require __DIR__ . '/basket-item.php';
			foreach ( $basket_list as $basket ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $basket_item_render( $basket );
			}
			?>
		</tbody>
	</table>
		<?php
	} else {
		?>
	<p>
		<?php
				esc_html_e(
					'You don\'t have anything in your basket.',
					'myvideoroom'
				)
		?>
	</p>
</div>
		<?php
	}

	return ob_get_clean();
};
