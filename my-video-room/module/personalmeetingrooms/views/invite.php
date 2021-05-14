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

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\Post;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Module;

return function (
	string $url,
	?string $message,
	?bool $success
): string {
	$html_lib = Factory::get_instance( HTML::class, array( 'personalmeetingrooms_invite' ) );

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
		<form action="" method="post" data-sending-text="Sending...">
			<label for="<?php echo esc_attr( $html_lib->get_id( 'address' ) ); ?>">Email address</label>
			<input
				type="email"
				placeholder="<?php esc_html_e( 'Email address' ); ?>"
				id="<?php echo esc_attr( $html_lib->get_id( 'address' ) ); ?>"
				name="<?php esc_attr( $html_lib->get_field_name( 'address' ) ); ?>"
			/>

			<input type="hidden" value="<?php echo esc_html( $url ); ?>" name="<?php esc_attr( $html_lib->get_field_name( 'link' ) ); ?>" />

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( Post::class )->create_form_submit( Module::INVITE_EMAIL_ACTION, esc_html__( 'Send link', 'myvideoroom' ) );
			?>
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
