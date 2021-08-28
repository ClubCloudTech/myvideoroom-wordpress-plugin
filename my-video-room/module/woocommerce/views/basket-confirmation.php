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
 */
return function (
	string $operation_type,
	string $room_name,
	string $auth_nonce
): string {
	// Check Nonce for Operation.
	$verify = wp_verify_nonce( $auth_nonce, $operation_type );
	if ( ! $verify ) {
		esc_html_e( 'Error ! Security Mismatch - please refresh', 'myvideoroom' );
		return '';
	}
	ob_start();

	if ( WooCommerce::SETTING_DELETE_BASKET === $operation_type ) {
		$message             = WooCommerce::TEXT_DELETE_BASKET;
		$delete_basket_nonce = wp_create_nonce( WooCommerce::SETTING_DELETE_BASKET_CONFIRMED );
	} else {
		$message = esc_html__( 'do this ?', 'myvideoroom' );
	}


	echo sprintf(
	/* translators: %s is the message variant translated above */
		\esc_html__(
			'Are you sure you want to %s',
			'myvideoroom'
		),
		esc_html( $message )
	);
	// Confirmation Buttons.
	$confirmation_button_cancel   = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REFRESH_BASKET, esc_html__( 'Cancel', 'my-video-room' ), $room_name );
	$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_BASKET_CONFIRMED, esc_html__( 'Clear Basket', 'my-video-room' ), $room_name, $delete_basket_nonce );
	?>

	<table class="wp-list-table widefat plugins">
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

	<?php
	return ob_get_clean();
};
