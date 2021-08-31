<?php
/**
 * Outputs Formatted Sync Table for WooCommerce Shopping Cart - this is used inside the Main table
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Sync-Table-Output.php
 */

/**
 * Render the WooCommerce Sync Table for inclusion in main table
 *
 * @param string $room_name -  Name of Room.
 * @return string
 */
return function (
	string $room_name
): string {
	ob_start();

	?>
<div id="refresh" class="mvr-nav-settingstabs-outer-wrap mvr-woocommerce-basket" 
data-reload-page="refresh" 
data-room-name="<?php echo esc_attr( $room_name ); ?>" >
</div>

	<?php

	return ob_get_clean();
};
