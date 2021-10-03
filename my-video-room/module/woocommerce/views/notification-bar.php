<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Cart
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Library\SectionTemplates;
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
	string $room_name,
	string $client_change_state = null,
	string $popup = null
): string {
	ob_start();
	?>

	<div id="notification" class="mvr-table-row">

		<?php
			$suffix  = '<div id="mvr-postbutton-notification" class="mvr-notification-align"></div>';
			$output  = Factory::get_instance( HostManagement::class )->master_button( $room_name );
			$output .= Factory::get_instance( HostManagement::class )->sync_notification_button( $room_name );
			$output .= Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REFRESH_BASKET, Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::BUTTON_REFRESH ), $room_name, null, null, null, 'mvr-shopping-basket', 'myvideoroom-button-link' );
			$output .= $client_change_state;
			$output .= $popup;

			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
			echo $output . $suffix;
		?>

	</div>
	<?php

	return ob_get_clean();
};
