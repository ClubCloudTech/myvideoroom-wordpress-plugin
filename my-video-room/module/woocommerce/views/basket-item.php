<?php
/**
 * Shows a single row with the current Basket Contents.
 *
 * @package MyVideoRoomPlugin\Modules\WooCommerce\Views\Basket-Item.php
 */

use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Render the admin page
 *
 * @param \stdClass $room The room
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	array $item
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

//var_dump( $item );
//return ob_get_clean();

	$regenerate_nonce = wp_create_nonce( 'regenerate_room_' . $item['product_id'] );
	$regenerate_url   = \add_query_arg(
		array(
			'room_id'  => $item['product_id'],
			'action'   => 'regenerate',
			'_wpnonce' => $regenerate_nonce,
		),
		\esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
	);

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
					class="mvr-icons dashicons dashicons-dismiss myvideoroom-sitevideo-delete"
					data-room-id="<?php echo esc_attr( $room->id ); ?>"
					title="<?php echo esc_attr( $action[0] ); ?>"

				></a>
		</td>
	</tr>


	<?php

	return ob_get_clean();
};
