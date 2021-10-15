<?php
/**
 * Outputs a Basket Operation Confirmation
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\BasketConfirmation.php
 */

/**
 * Renders a basket operation confirmation
 *
 * @param $operation_type - operation being performed.
 * @param string $room_name -  Name of Room.
 * @param string $auth_nonce - Nonce of operation.
 * @param string $message - Message to Display.
 * @param string $confirmation_button_approved - Button to Display for Approved.
 * @param string $data_for_nonce - Extra parameter like record id, product id etc for strengthening nonce.
 * @param string $cancel_type - The type of Operation to confirm in cancel button (used to redirect cancel through handlers to non basket window).
 */
return function (
	string $operation_type,
	string $room_name,
	string $auth_nonce,
	string $message = null,
	string $confirmation_button_approved,
	string $data_for_nonce = null,
	string $confirmation_button_cancel
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

<div id="mvr-basket-section-confirmation" class=" mvr-woocommerce-basket mvr-nav-settingstabs-outer-wrap mvr-table-row myvideoroom-welcome-page">
	<?php
	echo sprintf(
	/* translators: %s is the message variant translated above */
		\esc_html__(
			'Are you sure you want to %s',
			'myvideoroom'
		),
		esc_html( $message )
	);

	?>

	<table id="mvr-confirmation-table" class="wp-list-table widefat plugins mvr-shopping-basket-frame">
		<thead>
			<tr class="mvr-shopping-basket-frame">
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
