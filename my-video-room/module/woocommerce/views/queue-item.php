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
	<tr class="active" basket-id="<?php echo esc_attr( $item['product_id'] ); ?>">
		<td class="plugin-title column-primary mvr-icons"><?php echo $item['image']; ?></td>

		<td><a href="<?php echo $item['link']; ?>"
					title="<?php esc_html_e( 'View Product', 'myvideoroom' ); ?>"
					target="_blank"

				><?php echo esc_attr( $item['name'] ); ?></a>
			</td>


		<td class="column-description">
			<?php echo $item['price'] . ' x ' . $item['quantity']; ?>
		</td>
		<td class="plugin-title column-primary">
		<?php echo $item['subtotal']; ?>
		</td>
		<td>
				<?php

				if ( ! $item['am_i_downloading'] ) {
					if ( ! $room_basket_archive ) {
						?>
				<a href=""
					class="mvr-icons myvideoroom-sitevideo-delete myvideoroom-woocommerce-basket-ajax"
					data-record-id="<?php echo esc_attr( $item['record_id'] ); ?>"
					data-input-type="<?php echo esc_attr( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE ); ?>"
					data-room-name="<?php echo esc_attr( $room_name ); ?>"
					title="<?php esc_html_e( 'Reject Item - Do not add to your basket', 'myvideoroom' ); ?>"
					target="_blank"

				><span class="dashicons dashicons-dismiss"></span></a>
						<?php
					}
					?>

				<a href=""
					class="mvr-icons myvideoroom-woocommerce-basket-ajax"
					data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>"
					data-record-id="<?php echo esc_attr( $item['record_id'] ); ?>"
					data-quantity="<?php echo esc_attr( $item['quantity'] ); ?>"
					data-variation-id="<?php echo esc_attr( $item['variation_id'] ); ?>"
					data-input-type="<?php echo esc_attr( WooCommerce::SETTING_ADD_PRODUCT ); ?>"
					data-room-name="<?php echo esc_attr( $room_name ); ?>"
					data-auth-nonce="<?php echo esc_attr( wp_create_nonce( WooCommerce::SETTING_ADD_PRODUCT . $item['product_id'] ) ); ?>"
					title="<?php esc_html_e( 'Accept Item- add it to your basket', 'myvideoroom' ); ?>"
					target="_blank"
				><span class="dashicons dashicons-yes-alt"></span></a>

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
