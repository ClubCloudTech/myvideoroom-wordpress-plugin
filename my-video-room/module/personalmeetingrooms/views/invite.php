<?php
/**
 * Render an invite link
 *
 * @return string
 * @package MyVideoRoomPlugin\Module\PersonalMeetingRooms
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\Module;


/**
 * Output the invite link for a personal meeting room
 *
 * @param string  $url                The invite url.
 * @param ?bool   $show_icons         If we should show icons instead of labels.
 * @param ?bool   $invert_icon_colors If we should show invert the icon colors.
 * @param ?string $message            The success/failure message.
 * @param bool    $success            The status.
 *
 * @return string
 */
return function (
	string $url,
	?bool $show_icons,
	?bool $invert_icon_colors,
	?string $message,
	?bool $success
): string {
	$html_lib = Factory::get_instance( HTML::class, array( 'personalmeetingrooms_invite' ) );

	$main_class = 'myvideoroom-personalmeetingrooms-invite';

	if ( $show_icons ) {
		$main_class .= ' icon';
	}

	if ( $invert_icon_colors ) {
		$main_class .= ' invert';
	}
	ob_start();
	?>

	<div class="<?php echo esc_attr( $main_class ); ?>">
		<p>
			<?php
			esc_html_e(
				'Invite someone to your personal meeting:',
				'myvideoroom'
			);
			?>
		</p>

		<span
			class="link"
			data-copy-text="<?php esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>"
			data-copied-text="<?php esc_attr_e( 'Copied!', 'myvideoroom' ); ?>"
		>
		<?php echo esc_html( $url ); ?>
		</span>

		<form action="" method="post" data-sending-text="<?php esc_attr_e( 'Sending...', 'myvideoroom' ); ?>">
			<label for="<?php echo esc_attr( $html_lib->get_id( 'address' ) ); ?>">Email address</label>
			<input
				type="email"
				placeholder="<?php esc_html_e( 'Email address' ); ?>"
				id="<?php echo esc_attr( $html_lib->get_id( 'address' ) ); ?>"
				name="<?php echo esc_attr( $html_lib->get_field_name( 'address' ) ); ?>"
				required
			/>

			<input
				type="hidden"
				value="<?php echo esc_html( $url ); ?>"
				name="<?php echo esc_attr( $html_lib->get_field_name( 'link' ) ); ?>"
			/>

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( HttpPost::class )->create_form_submit( Module::INVITE_EMAIL_ACTION, esc_html__( 'Send link', 'myvideoroom' ) );
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
