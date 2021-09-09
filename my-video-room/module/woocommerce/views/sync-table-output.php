<?php
/**
 * Outputs Formatted Sync Table for WooCommerce Shopping Cart - this is used inside the Main table
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Sync-Table-Output.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

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
		$nav_button_filter  = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ACCEPT_ALL_QUEUE, esc_html__( 'Accept All items', 'myvideoroom' ), $room_name, $accept_all_nonce, WooCommerce::SETTING_ACCEPT_ALL_QUEUE );
		$accept_all_nonce   = wp_create_nonce( WooCommerce::SETTING_ACCEPT_ALL_QUEUE );
		$style              = 'mvr-woocommerce-basket';
		$message            = esc_html__( 'Items Shared with You', 'myvideoroom' );
		$reject_all_nonce   = wp_create_nonce( WooCommerce::SETTING_REJECT_ALL_QUEUE );
		$nav_button_filter .= Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REJECT_ALL_QUEUE, esc_html__( 'Delete All items', 'myvideoroom' ), $room_name, $reject_all_nonce, WooCommerce::SETTING_REJECT_ALL_QUEUE );

	} else {
		$message = null;
	}

	if ( $basket_list ) {
		?>
<div id="basket-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap <?php echo esc_attr( $style ); ?>">
	<h1>
		<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped Above.
		echo $message;
		?>
	</h1>
	<div>
				<?php
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
				echo $nav_button_filter;
				?>
	</div>

	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
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
					<?php esc_html_e( 'Subtotal', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Actions', 'myvideoroom' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$basket_item_render = require __DIR__ . '/queue-item.php';
			foreach ( $basket_list as $basket ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $basket_item_render( $basket, $room_name, $room_basket_archive );
			}
			?>
		</tbody>
	</table>
		</div>

		<?php
	}

	return ob_get_clean();
};
