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
			<h2><?php esc_html_e( 'Meeting Picture?', 'my-video-room' ); ?></h2>
			<p id="myvideoroom-picturedescription" class="myvideoroom-table-adjust"><?php esc_html_e( 'You can select a picture for the room participants to see you in seating plans', 'myvideoroom' ); ?></p>
			<input id="vid-picture" type="button" value="Take Picture" />
			<input id="vid-skip" type="button" value="Skip" />

			<div id="myvideoroom-picturewrap" class="mvr-hide">
				<div id="vid-result" class="mvr-header-section"></div>
				<video id="vid-live" autoplay class="mvr-header-section"></video>
				<div class="mvr-flex">
					<input id="vid-retake" type="button" value="Retake" class="mvr-hide mvr-main-button-enabled"/>
					<input id="vid-take" type="button" value="Snap" class="mvr-main-button-enabled" class="mvr-main-button-enabled"/>
					<input id="vid-up" type="button" value="Use This" class="mvr-main-button-enabled mvr-hide"/>
				</div>
				<div id="vid-result" class="mvr-header-section"></div>
			</div>
			<div id="myvideoroom-meeting-name" class="mvr-hide">
			<hr>	
			<h2><?php esc_html_e( 'Your Meeting Name', 'my-video-room' ); ?></h2>	
			<p id="myvideoroom-namedescription" class="myvideoroom-table-adjust"><?php esc_html_e( 'Your Display Name is what others will see you called in the Floorplan', 'myvideoroom' ); ?></p>
				<input id="vid-name" type="text" placeholder="Meeting Display Name" class="myvideoroom-input-restrict-alphanumeric"/>
				<hr>
				<input id="vid-down" type="button" value="Checkin to Meeting" class="mvr-main-button-enabled" disabled/>
			</div>
		</div>

		<?php

		return ob_get_clean();
};
