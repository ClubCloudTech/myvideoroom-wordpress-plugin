<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Room Sync
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Shop-Output.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Render the WooCommerce Basket Render Page
 *
 * @param array   $output   - The Product Archive.
 * @param string $room_name -  Name of Room.
 *
 * @return string
 */
return function (
	$output,
	string $room_name = null
): string {
	ob_start();

	?>
<div id="basket-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">

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
					'This room has no products available in its store category and tags.',
					'myvideoroom'
				);

		?>
	</p>
</div>

		<?php
	}

	return ob_get_clean();
};
