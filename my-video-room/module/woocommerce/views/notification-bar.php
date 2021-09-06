<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Cart
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;
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
	string $room_name
): string {
	ob_start();

	?>

	<div id="notification" class="mvr-table-row">
		<div id="notificationleft" class="mvr-left" >
		<?php
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
			echo Factory::get_instance( HostManagement::class )->master_button( $room_name );
		?>
		</div>
		<div id="notificationright" class="mvr-right">
		<?php
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
			echo Factory::get_instance( HostManagement::class )->sync_notification_button( $room_name );
		?>
		</div>		
	</div>
	<?php

	return ob_get_clean();
};
