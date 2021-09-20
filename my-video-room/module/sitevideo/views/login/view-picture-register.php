<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Picture-Register.php
 */

/**
 * Render the admin page
 *
 * @param ?string $details_section Optional details section.
 *
 * @return string
 */
return function (
	string $details_section = null
): string {

	ob_start();

	?>

		<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap myvideoroom-header-table-right">
			<h2><?php esc_html_e( 'Your Meeting Avatar', 'my-video-room' ); ?></h2>
			<video id="vid-live" autoplay></video>
			<input id="vid-retake" type="button" value="Retake" class="mvr-hide"/>
			<input id="vid-take" type="button" value="Snapshot"/>
			<input id="vid-down" type="button" value="Download"/>
			<input id="vid-up" type="button" value="Upload"/>
			<div id="vid-result"></div>
			<?php


			?>
		</div>

		<?php

		return ob_get_clean();
};
