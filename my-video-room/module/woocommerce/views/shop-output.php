<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Room Sync
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Shop-Output.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
use MyVideoRoomPlugin\Module\WooCommerce\Library\WooCategory;

/**
 * Render the WooCommerce Basket Render Page
 *
 * @param array   $output   - The Product Archive.
 * @param string $room_name -  Name of Room.
 * @param string $last_queue -  The display table with the last basket queue shared in the room.
 *
 * @return string
 */
return function (
	$output,
	string $room_name = null,
	string $last_queue = null,
	int $shop_count = null
): string {
	ob_start();
?>
	<div id="basket-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
	<div class ="mvr-storefront-master">
	<div id="storeid" 
	data-last-storecount="<?php echo esc_attr( $shop_count ); ?>"
	></div>
	<?php
	if ( strlen( $output ) > 100 ) {
		?>

	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
			<h2>
			<?php
			esc_html_e( 'Room Store', 'myvideoroom' );
			?>
			</h2>
			<p>
			<?php
			esc_html_e( 'The Room store contains products the store owner wants to connect to this room. You can add products from here or add them via other baskets, search, or basket sync', 'myvideoroom' );
			?>
			</p>
			</tr>
		</thead>
		<tbody>
			<div class="mvr-woocommerce-overlay">
			<?php
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
				echo $output;
			?>
			</div>
		</tbody>
	</table>
		<?php
	} else {
		?>
	<p>
		<?php

				esc_html_e(
					'This room has no products available in its store category or tags.',
					'myvideoroom'
				);

		?>
	</p>
</div>

		<?php
	}
	if ( $last_queue ) {
		?>
				<h2>
			<?php
			esc_html_e( 'Previously Shared Basket', 'myvideoroom' );
			?>
			</h2>
			<p>
			<?php
			esc_html_e( 'This room previously shared a basket. You can grab a copy of its last products here.', 'myvideoroom' );
			?>
			</p>
				<div  class="mvr-woocommerce-overlay">
				<?php
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
				echo $last_queue;
				?>
				</div>	
	</div>
			<?php
	}
	return ob_get_clean();
};
