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

	/*$edit_actions = array(
		array(
			__( 'Edit in WordPress' ),
			get_site_url() . '/wp-admin/post.php?post=' . esc_textarea( $room->id ) . '&action=edit',
			'dashicons dashicons-wordpress',
			array( 'target' => '_blank' ),
		),
	);*/

	?>
	<tr class="active" basket-id="<?php echo esc_attr( $item['product_id'] ); ?>">
		<td class="plugin-title column-primary mvr-icons"><?php echo $item['image']; ?></td>

		<td><?php echo esc_attr( $item['name'] ); ?></td>


		<td class="column-description">
			<?php echo $item['price'] . ' x ' . $item['quantity']; ?>
		</td>
		<td class="plugin-title column-primary">
		<?php echo $item['subtotal'] ?>
		</td>
		<td>
				<a href=""
					class="mvr-icons dashicons dashicons-dismiss myvideoroom-sitevideo-delete myvideoroom-woocommerce-basket-ajax"
					data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
					data-input-type="<?php echo esc_attr( WooCommerce::SETTING_DELETE_PRODUCT ); ?>"
					data-room-name="<?php echo esc_attr( $room_name ); ?>"
					title="<?php esc_html_e( 'Delete this item from Basket', 'myvideoroom' ); ?>"
					target="_blank"

				></a>
				<a href=""
					class="mvr-icons dashicons dashicons-upload myvideoroom-sitevideo-delete myvideoroom-woocommerce-basket-ajax"
					data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
					data-quantity="<?php echo esc_attr( $item['quantity'] ); ?>"
					data-variation-id="<?php echo esc_attr( $item['variation_id'] ); ?>"
					data-input-type="<?php echo esc_attr( WooCommerce::SETTING_BROADCAST_PRODUCT ); ?>"
					data-room-name="<?php echo esc_attr( $room_name ); ?>"
					data-auth-nonce="<?php echo esc_attr( wp_create_nonce( WooCommerce::SETTING_BROADCAST_PRODUCT ) ); ?>"
					title="<?php esc_html_e( 'Share This Product With the Room', 'myvideoroom' ); ?>"
					target="_blank"

				></a>
		</td>
	</tr>


	<?php

	return ob_get_clean();
};
