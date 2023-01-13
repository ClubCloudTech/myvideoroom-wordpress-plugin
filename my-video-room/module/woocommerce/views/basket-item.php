<?php
/**
 * Shows a single row with the current Basket Contents.
 *
 * @package MyVideoRoomPlugin\Modules\WooCommerce\Views\Basket-Item.php
 */

use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Render the admin page
 *
 * @param \stdClass $room The room
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	array $item,
	string $room_name
): string {
	ob_start();
	?>
	<tr class="active mvr-table-mobile" basket-id="<?php echo esc_attr( $item['product_id'] ); ?>">
		<td class=" column-primary mvr-icons myvideoroom-mobile-img-adjust myvideoroom-mobile-table-row-adjust">
			<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
			echo $item['image'];
			?>
		</td>

		<td class="myvideoroom-mobile-table-row-adjust">		
			<a href="<?php echo esc_url( $item['link'] ); ?>"
					title="<?php esc_html_e( 'View Product', 'myvideoroom' ); ?>"
					target="_blank"

				><?php echo esc_attr( $item['name'] ); ?></a>

	</td>


		<td class="column-description myvideoroom-mobile-table-row-adjust">
			<?php
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
				echo $item['price'] . ' x ' . $item['quantity']; 
			?>
		</td>
		<td class="plugin-title column-primary myvideoroom-mobile-table-row-adjust">
		<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
			echo $item['subtotal'];
		?>
		</td>
		<td class="myvideoroom-mobile-table-row-adjust">
			<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
			echo apply_filters( 'myvideoroom_basket_buttons', null, $item, $room_name );
			if ( ! $item['am_i_downloading'] ) {
				?>
				<a 	class="mvr-icons myvideoroom-woocommerce-basket-ajax myvideoroom-button-override"
					alt="remove"
					data-target="mvr-shopping-basket" 
					data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
					data-input-type="<?php echo esc_attr( WooCommerce::SETTING_DELETE_PRODUCT ); ?>"
					data-room-name="<?php echo esc_attr( $room_name ); ?>"
					title="<?php esc_html_e( 'Delete this item from Basket', 'myvideoroom' ); ?>"
					target="_blank"

				><span class="myvideoroom-dashicons dashicons-dismiss myvideoroom-dashicons-override"></span></a>
				<a 	class="mvr-icons myvideoroom-woocommerce-basket-ajax myvideoroom-button-override"
					data-target="mvr-shopping-basket" 
					data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
					data-quantity="<?php echo esc_attr( $item['quantity'] ); ?>"
					data-variation-id="<?php echo esc_attr( $item['variation_id'] ); ?>"
					data-input-type="<?php echo esc_attr( WooCommerce::SETTING_BROADCAST_PRODUCT ); ?>"
					data-room-name="<?php echo esc_attr( $room_name ); ?>"
					data-auth-nonce="<?php echo esc_attr( wp_create_nonce( WooCommerce::SETTING_BROADCAST_PRODUCT ) ); ?>"
					title="<?php esc_html_e( 'Share This Product With the Room', 'myvideoroom' ); ?>"
					target="_blank"

				><span class="myvideoroom-dashicons dashicons-upload myvideoroom-dashicons-override"></span></a>
				<?php
			} else {
				echo esc_html_e( 'Basket is Auto Syncing' );
			}
			?>
		</td>
	</tr>


	<?php

	return ob_get_clean();
};
