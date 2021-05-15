<?php
/**
 * Render an invite email
 *
 * @package MyVideoRoomPlugin\Module\PersonalMeetingRooms
 */

/**
 * Output the invite link for a personal meeting room
 *
 * @param string $invite_link  The invite link
 * @param string $site_name    The email link
 * @param string $inviter      The name of the person who sent the invite
 *
 * @return string
 */
return function (
	string $invite_link,
	string $site_name,
	string $inviter
): string {
	ob_start();

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" lang="en-GB">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title><?php esc_html_e( 'Video Meeting Invite', 'my-video-room' ); ?></title>
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		</head>
		<body>

			<p><?php esc_html_e( 'Hello there! ', 'my-video-room' ); ?></p>

			<p>
				<?php
				printf(
					/* translators: First %s is the name of the WordPress site, second is the name of the person who sent the link */
					esc_html__(
						'You have been invited to a video call on %1$s by %2$s.',
						'myvideoroom'
					),
					esc_html( $site_name ),
					esc_html( $inviter )
				);
				?>
			</p>

			<p>
				<?php
				printf(
					/* translators: %s is the link to join the call */
					esc_html__(
						'Please visit %s to join the call - your host is waiting for you.',
						'myvideoroom'
					),
					'<a href="' . esc_url_raw( $invite_link ) . '">' . esc_url_raw( $invite_link ) . '</a>'
				);
				?>
			</p>

			<p>
				<?php
				esc_html_e(
					'You will need to either click/tap on the flashing circle to join, or you will arrive in a waiting room, the host will be alerted and they will join you to the meeting.',
					'my-video-room'
				);
				?>
			</p>

			<p><?php esc_html_e( 'Thanks,', 'my-video-room' ); ?></p>

			<p>
			<?php
			printf(
				/* translators: %s is the name of the WordPress site */
				esc_html__(
					'The %s Team.',
					'myvideoroom'
				),
				esc_html( $site_name )
			);
			?>
			</p>

			<p>
				<?php
				printf(
					/* translators: %s is the a link to the ClubCloud service */
					esc_html__(
						'MyVideoRoom: A video service provided by %s.',
						'myvideoroom'
					),
					'<a href="https://www.clubcloud.tech">ClubCloud</a>'
				);
				?>
			</p>
		</body>
	</html>
	<?php

	return ob_get_clean();
};
