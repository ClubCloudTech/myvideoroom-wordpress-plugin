<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Cart
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Render the WooCommerce Basket Render Page
 *
 * @param array   $basket_list        The list of rooms.
 * @param string $room_name -  Name of Room.
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	array $basket_list,
	string $room_name
): string {
	ob_start();


	?>
<div id="basket-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap mvr-woocommerce-basket">
	<?php

	if ( $basket_list ) {
		?>
	<div class="mvr-header-table-left">
				<?php
				$delete_basket_nonce = wp_create_nonce( WooCommerce::SETTING_DELETE_BASKET );
				$nav_button_filter   = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REFRESH_BASKET, esc_html__( 'Update Basket', 'my-video-room' ), $room_name );
				$nav_button_filter  .= Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_BASKET, esc_html__( 'Clear Basket', 'my-video-room' ), $room_name, $delete_basket_nonce );
				//$nav_button_filter = apply_filters( 'myvideoroom_template_icon_section', $nav_button_filter, $user_id, $room_name, $visitor_status );
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
				echo $nav_button_filter;
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
		$result = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_room_participants( $room_name );

		// TODO REMOVE
		//echo var_dump( $result );
		
		?>
	</p>
</div>

		<?php
	}

	return ob_get_clean();
};
