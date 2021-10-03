<?php
/**
 * Outputs Formatted Room Category Edit Table for WooCommerce Category Management.
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Room-Management-Output.php
 */

/**
 * Render the WooCommerce Sync Table for inclusion in main table
 *
 * @param array   $basket_list   - Products in Basket.
 * @param string $room_name -  Name of Room.
 * @param array   $inbound_queue - Products to Decide Upon.
 * @param ?string $room_type  Category of Room to Filter.
 * @param bool $room_basket_archive  Flag whether the table is a user table, or an archive table of the last shared basket.
 *
 * @return string
 */
return function (
	array $basket_list,
	string $room_name,
	bool $room_basket_archive = null
): string {
	ob_start();

	if ( ! $room_basket_archive && $basket_list ) {
		$message = esc_html__( 'Manage Room Store', 'myvideoroom' );

	} else {
		$message = esc_html__( 'Store is Empty', 'myvideoroom' );
	}
	if ( $basket_list ) {
		?>
<div id="roommanage-video-notification"></div>
<div id="roommanage-video-host-wrap-table">
	<h2>
		<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped Above.
		echo $message;
		?>
	</h2>
	<p>
		<?php
		esc_html_e( 'You can remove items from your store, or broadcast them here. To add an item insert it to your basked and click save to store.', 'myvideoroom' );
		?>
	</p>
	<div class="mvr-woocommerce-overlay">
		<table class="wp-list-table widefat plugins myvideoroom-table-adjust">
			<thead>
				<tr class="myvideoroom-hide-mobile">
					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Product', 'myvideoroom' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Name', 'myvideoroom' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Price', 'myvideoroom' ); ?>
					</th>

					<th scope="col" class="manage-column column-name column-primary">
						<?php esc_html_e( 'Actions', 'myvideoroom' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$basket_item_render = require __DIR__ . '/store-manage-item.php';
				foreach ( $basket_list as $basket ) {
					//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $basket_item_render( $basket, $room_name, $room_basket_archive );
				}
				?>
			</tbody>
		</table>
	</div>
</div>

		<?php
	}

	return ob_get_clean();
};
