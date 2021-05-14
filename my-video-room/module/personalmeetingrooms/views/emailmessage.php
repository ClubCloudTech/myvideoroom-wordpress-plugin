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
	string $invite_link,
	string $site_name
): string {
	ob_start();

?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" lang="en-GB">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php esc_html_e('Video Meeting Invite', 'my-video-room'); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>
	<?php esc_html_e('Hello There! ', 'my-video-room'); ?><br>
	<br><?php esc_html_e('A guest of ours has sent you a link for a Video Meeting from', 'my-video-room'); ?>
	<?php
	printf(
		/* translators: %s is the name of the WordPress site */
		esc_html__(
			'The %s Team.',
			'myvideoroom'
		),
		esc_html($site_name)
	);
	?>
	:<br>
	<br>
	<a href='<?php echo esc_url_raw($invite_link); ?>'><?php echo esc_url_raw($invite_link); ?></a><br>
	<br>
	<?php
	printf(
		/* translators: %s is the name of the WordPress site */
		esc_html__(
			'All you need to do is click on the link and you will be taken to the video meeting hosted by %s',
			'myvideoroom'
		),
		'<a href="https://www.clubcloud.tech">ClubCloud</a>.'
	);
	?>
	<br><?php esc_html_e('You will either need to click/tap on a flashing circle to join or will you arrive in a waiting room at which point the host will be alerted and will join you to the meeting.', 'my-video-room'); ?>
	<br>
	<br><?php esc_html_e('Thank You,', 'my-video-room'); ?><br>
	<br>
	<?php
	printf(
		/* translators: %s is the name of the WordPress site */
		esc_html__(
			'The %s Team.',
			'myvideoroom'
		),
		esc_html($site_name)
	);
	?>
	</body>
	</html>
<?php

	return ob_get_clean();
};