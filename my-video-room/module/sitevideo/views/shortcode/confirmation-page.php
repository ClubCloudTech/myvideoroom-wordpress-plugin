<?php
/**
 * Outputs a Membership Confirmation
 *
 * @package ElementalPlugin\Membership\Views\confirmation-page.php
 */

/**
 * Renders a Membership Screen operation confirmation
 *
 * @param string $message - Message to Display.
 * @param string $confirmation_button_approved - Button to Display for Approved.
 * @param string $confirmation_button_cancel - Button to Display for rejected.
 */
return function (
	string $message = null,
	string $confirmation_button_approved,
	string $confirmation_button_cancel
): string {
	// Check Nonce for Operation.

	if ( ! $message ) {
		$message = esc_html__( 'do this ?', 'myvideoroom' );
	}
	ob_start();
	?>

<div id="mvr-basket-section-confirmation" class="mvr-nav-settingstabs-outer-wrap mvr-table-row myvideoroom-welcome-page">
	<div class="mvr-confirmation-table-left">
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
	</div>
	<div class="mvr-confirmation-table-right">
	<div class="mvr-confirmation-butons">
	<?php
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function is Icon only, and already escaped within it.
					echo $confirmation_button_approved . $confirmation_button_cancel;
	?>

	</div>

</div>

	<?php
	return ob_get_clean();
};
