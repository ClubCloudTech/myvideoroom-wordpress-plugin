<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Cart
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Table-Output.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Render the WooCommerce Basket Render Page
 *
 * @param array   $basket_list   - Products in Basket.
 * @param string $room_name -  Name of Room.
 * @param array   $last_queuenum - The last number of items in queue.
 * @param ?string $room_type  Category of Room to Filter.
 * @param bool $download_active -Autosync status.
 *
 * @return string
 */
return function (
	array $basket_list,
	string $room_name,
	string $last_queuenum = null,
	string $last_carthash = null,
	bool $download_active = null,
	bool $master_status = null,
	bool $broadcast_status = null
): string {
	ob_start();

	?>
<div id="basket-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap mvr-woocommerce-basket">

	<div id="roomid" 
	data-room-name="<?php echo esc_attr( $room_name ); ?>" 
	data-last-queuenum="<?php echo esc_attr( $last_queuenum ); ?>"
	data-last-carthash="<?php echo esc_attr( $last_carthash ); ?>"
	>
	</div>
	<?php
	// Render Product Broadcast Table if Autosync isnt on.

	if ( ! $download_active ) {
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
		echo Factory::get_instance( ShoppingBasket::class )->render_sync_queue_table( $room_name );
	}

	?>
	<h1>
		<?php
		$prefix = esc_html__( 'Your ', 'my-video-room' );

		if ( $master_status ) {
			$main = esc_html__( ' Shareable ', 'myvideoroom' );
		}
		if ( $broadcast_status ) {
			$main = esc_html__( ' Shared Room ', 'myvideoroom' );
		}
		if ( $download_active ) {
			$main = esc_html__( ' Auto Updated ', 'myvideoroom' );
		}
		$suffix = esc_html__( 'Basket', 'my-video-room' );

		echo esc_html( $prefix . $main . $suffix );

		?>

	</h1>
	<?php
	if ( $basket_list ) {
		?>
	<div class="mvr-header-table-left">
				<?php
				if ( ! $download_active ) {
					$delete_basket_nonce = wp_create_nonce( WooCommerce::SETTING_DELETE_BASKET );
					$nav_button_filter   = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REFRESH_BASKET, esc_html__( 'Update Basket', 'my-video-room' ), $room_name );
					$nav_button_filter  .= Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_BASKET, esc_html__( 'Clear Basket', 'my-video-room' ), $room_name, $delete_basket_nonce, null, 'mvr-main-button-cancel' );
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
					echo $nav_button_filter;
				}

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
					<?php esc_html_e( 'Subtotal', 'my-video-room' ); ?>
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
				echo $basket_item_render( $basket, $room_name );
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
				);
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REFRESH_BASKET, esc_html__( 'Update Basket', 'my-video-room' ), $room_name );

		?>
	</p>
</div>

		<?php
	}

	return ob_get_clean();
};
