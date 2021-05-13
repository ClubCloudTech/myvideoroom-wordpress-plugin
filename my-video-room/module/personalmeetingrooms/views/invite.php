<?php
/**
 * Render an invite link
 *
 * @package MyVideoRoomPlugin\Module\PersonalMeetingRooms
 *
 * @return string
 */

/**
 * Output the invite link for a personal meeting room
 *
 * @param string  $url      The invite url.
 * @param ?string $message  The success/failure message.
 * @param bool    $success  The status.
 * @param integer $id_index A unique id to generate unique id names.
 *
 * @return string
 */
return function (
	string $url,
	?string $message,
	?bool $success,
	int $id_index = 0
): string {
	ob_start();
	?>

	<div class="myvideoroom-personalmeetingrooms-invite">
		<p>
			<?php
			esc_html_e(
				'You can invite people to your personal meeting room using the following link:',
				'myvideoroom'
			);
			?>
		</p>
		<?php echo esc_html( $url ); ?>

		<p>
			<?php
			esc_html_e(
				'Or email the link to them.',
				'myvideoroom'
			);
			?>
		</p>
		<form action="" method="post">
			<label for="myvideoroom_personalmeetingrooms_invite_address_<?php echo esc_attr( $id_index ); ?>">Email address</label>
			<input
				type="email"
				placeholder="<?php esc_html_e( 'Email address' ); ?>"
				id="myvideoroom_personalmeetingrooms_invite_address_<?php echo esc_attr( $id_index ); ?>"
				name="myvideoroom_personalmeetingrooms_invite_address"
			/>

			<input type="hidden" value="<?php echo esc_html( $url ); ?>" name="myvideoroom_personalmeetingrooms_invite_link" />

			<input type="hidden" value="myvideoroom_personalmeetingrooms_invite" name="myvideoroom_action" />
			<?php wp_nonce_field( 'myvideoroom_personalmeetingrooms_invite', 'myvideoroom_nonce' ); ?>
			<input type="submit" value="<?php esc_html_e( 'Send link' ); ?>">
		</form>

		<?php
		if ( null !== $success ) {
			$status_type = $success ? 'failure' : 'success';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
			echo '<span class="status ' . $status_type . '">' . $message . '</span>';
		}
		?>
	</div>

	<?php

	return ob_get_clean();
};
