<?php
/**
 * Outputs Formatted Sync Table for WooCommerce Shopping Cart - this is used inside the Main table
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Sync-Table-Output.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;

/**
 * Render the WooCommerce Sync Table for inclusion in main table
 *
 * @param array   $basket_list   - Products in Basket.
 * @param string $room_name -  Name of Room.
 * @param array   $inbound_queue - Products to Decide Upon.
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	array $basket_list,
	string $room_name
): string {
	ob_start();

	if ( $basket_list ) {
		?>
<div id="basket-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap mvr-woocommerce-basket">
	<h1><?php esc_html_e( 'Items Shared with You', 'my-video-room' ); ?></h1>
	<div class="mvr-header-table-left">
				<?php
				// Sync Table Buttons to Go Here
				?>
	</div>

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
			$basket_item_render = require __DIR__ . '/queue-item.php';
			foreach ( $basket_list as $basket ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $basket_item_render( $basket, $room_name );
			}
			?>
		</tbody>
	</table>
		</div>

		<?php
	}

	return ob_get_clean();
};
