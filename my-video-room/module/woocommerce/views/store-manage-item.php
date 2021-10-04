<?php
/**
 * Shows a single row with the current Basket Contents.
 *
 * @package MyVideoRoomPlugin\Modules\WooCommerce\Views\Queue-Item.php
 */

use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Render the admin page
 *
 * @param \stdClass $room The room
 * @param ?string $room_type  Category of Room to Filter.
 * @param bool $room_basket_archive  Flag whether the table is a user table, or an archive table of the last shared basket.
 *
 * @return string
 */
return function (
	array $item,
	string $room_name,
	bool $room_basket_archive = null
): string {
	ob_start();

	?>
<tr class="active mvr-table-mobile" basket-id="<?php echo esc_attr( $item['product_id'] ); ?>">
	<td class="plugin-title column-primary mvr-icons myvideoroom-mobile-img-adjust myvideoroom-mobile-table-row-adjust">
		<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - WooCommerce image already escaped.
		echo $item['image']; 
		?>
		</td>
	<td class="myvideoroom-mobile-table-row-adjust">
		<a href="<?php echo esc_url( $item['link'] ); ?>" title="<?php esc_html_e( 'View Product', 'myvideoroom' ); ?>"
			target="_blank"><?php echo esc_attr( $item['name'] ); ?></a>
	</td>
	<td class="column-description myvideoroom-mobile-table-row-adjust">
		<?php
		if ( $item['price'] ) {
			echo esc_textarea( $item['price'] );
		} else {
			esc_html_e( 'Out of Stock', 'myvideoroom' );
		}

		?>
	</td>
	<td class="myvideoroom-mobile-table-row-adjust">
		<a  class="mvr-icons myvideoroom-woocommerce-basket-ajax"
			data-target="<?php echo esc_textarea( WooCommerce::SETTING_STORE_FRAME ); ?>" 
			data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
			data-input-type="<?php echo esc_attr( WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY ); ?>"
			data-room-name="<?php echo esc_attr( $room_name ); ?>"
			data-auth-nonce="<?php echo esc_attr( wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY . $item['product_id'] ) ); ?>"
			title="<?php esc_html_e( 'Remove this item from the room', 'myvideoroom' ); ?>" target="_blank"><span
				class="myvideoroom-dashicons dashicons-dismiss"></span></a>
		<a  class="mvr-icons myvideoroom-woocommerce-basket-ajax"
			data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
			data-quantity="1"
			data-target="<?php echo esc_textarea( WooCommerce::SETTING_STORE_FRAME ); ?>"
			data-variation-id="<?php echo esc_attr( $item['variation_id'] ); ?>"
			data-input-type="<?php echo esc_attr( WooCommerce::SETTING_BROADCAST_PRODUCT ); ?>"
			data-room-name="<?php echo esc_attr( $room_name ); ?>"
			data-auth-nonce="<?php echo esc_attr( wp_create_nonce( WooCommerce::SETTING_BROADCAST_PRODUCT ) ); ?>"
			title="<?php esc_html_e( 'Share a single instance of this product with the room', 'myvideoroom' ); ?>" target="_blank"><span
				class="myvideoroom-dashicons dashicons-upload"></span></a>

	</td>
</tr>


	<?php

	return ob_get_clean();
};
