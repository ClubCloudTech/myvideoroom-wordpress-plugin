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
 * @param string $url The invite url
 *
 * @return string
 */
return function (
	string $url
): string {
	ob_start();
	?>

	<span>
		<?php echo esc_html( $url ); ?>
	</span>

	<?php

	return ob_get_clean();
};
