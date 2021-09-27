<?php
/**
 * Outputs a Basket Operation Confirmation
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\BasketConfirmation.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Renders a basket operation confirmation
 *
 * @param $operation_type - operation being performed.
 * @param string $room_name -  Name of Room.
 * @param string $auth_nonce - Nonce of operation.
 * @param string $message - Message to Display.
 * @param string $confirmation_button_approved - Button to Display for Approved.
 * @param string $data_for_nonce - Extra parameter like record id, product id etc for strengthening nonce.
 */
return function (
	string $operation_type,
	string $room_name,
	string $auth_nonce,
	string $message = null,
	string $confirmation_button_approved,
	string $data_for_nonce = null
): string {
	// Check Nonce for Operation.
	$verify = wp_verify_nonce( $auth_nonce, $operation_type . $data_for_nonce );
	if ( ! $verify ) {
		esc_html_e( 'Error ! Security Mismatch - please refresh', 'myvideoroom' );
		return '';
	}

	if ( ! $message ) {
		$message = esc_html__( 'do this ?', 'myvideoroom' );
	}
	ob_start();
	?>
<div id="mvr-basket-section" class=" mvr-woocommerce-basket mvr-nav-settingstabs-outer-wrap mvr-table-row myvideoroom-welcome-page">
	<?php
	echo sprintf(
	/* translators: %s is the message variant translated above */
		\esc_html__(
			'Are you sure you want to %s',
			'myvideoroom'
		),
		esc_html( $message )
	);
	// Confirmation Cancel Button.
	$confirmation_button_cancel = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REFRESH_BASKET, esc_html__( 'Cancel', 'my-video-room' ), $room_name, null, null, 'mvr-main-button-cancel' );

	?>

	<table id="mvr-confirmation-table" class="wp-list-table widefat plugins mvr-shopping-basket-frame">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name column-primary">
					<?php
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
					echo $confirmation_button_approved;
					?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
					echo $confirmation_button_cancel;
					?>
				</th>
			</tr>
		</thead>
</div>

	<?php
	return ob_get_clean();
};
