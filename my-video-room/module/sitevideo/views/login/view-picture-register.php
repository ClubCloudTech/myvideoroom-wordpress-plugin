<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Picture-Register.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\TemplateIcons;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;

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

<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap myvideoroom-welcome-page">

	<div id="mvr-top-notification" class="myvideoroom-button-notification">

		<?php
		$output  = Factory::get_instance( TemplateIcons::class )->format_button_icon( 'name' );
		$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'photo' );
		if ( ! \is_user_logged_in() ) {
			$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'login' );
		}
		$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'checksound' );
		echo $output;
		?>

	</div>
	<div id="mvr-picture" class="myvideoroom-center">
		<h2><?php esc_html_e( 'Meeting Picture?', 'my-video-room' ); ?></h2>
		<p id="myvideoroom-picturedescription" class="myvideoroom-table-adjust">
			<?php esc_html_e( 'You can select a picture for the room participants to see you in seating plans', 'myvideoroom' ); ?>
		</p>
		<div class="mvr-flex">
			<input id="vid-retake" type="button" value="Retake"
				class="mvr-hide mvr-main-button-enabled myvideoroom-welcome-buttons" />
			<input id="vid-take" type="button" value="Snap" class="myvideoroom-welcome-positive mvr-hide"
				class="mvr-main-button-enabled myvideoroom-welcome-buttons" />
			<input id="vid-up" type="button" value="Use This" class="myvideoroom-welcome-positive mvr-hide" />
			<input id="vid-picture" type="button" value="Take Picture" class="myvideoroom-welcome-positive" />
			<input id="vid-skip" type="button" value="Skip" class="myvideoroom-welcome-buttons" />
		</div>


		<div id="myvideoroom-picturewrap" class="mvr-hide">
			<div id="vid-result" class="mvr-header-section"></div>
			<video id="vid-live" autoplay class="mvr-header-section myvideoroom-image-result"></video>
			<div id="vid-result" class="mvr-header-section"></div>
		</div>

	</div>
	<div id="myvideoroom-meeting-name" class="mvr-hide">
			<hr>
			<h2><?php esc_html_e( 'Your Meeting Name', 'my-video-room' ); ?></h2>
			<p id="myvideoroom-namedescription" class="myvideoroom-table-adjust">
				<?php esc_html_e( 'Your Display Name is what others will see you called in the Floorplan', 'myvideoroom' ); ?>
			</p>
			<input id="vid-name" type="text" placeholder="Meeting Display Name"
				class="myvideoroom-input-restrict-alphanumeric" />
			<hr>
			<input id="vid-down" type="button" value="Checkin to Meeting" class="myvideoroom-welcome-positive"
				disabled />
		</div>
		<div id="myvideoroom-checksound" class="mvr-hide myvideoroom-center">
		<h2><?php esc_html_e( 'Check Your Sound and Camera?', 'my-video-room' ); ?></h2>
			<p id="myvideoroom-sounddescription" class="myvideoroom-table-adjust">
				<?php esc_html_e( 'You can use this handy entry room to get your sound and camera checked out before you enter the main room', 'myvideoroom' ); ?>
			</p>
			<input id="chk-sound" type="button" value="Check Camera and Sound" class="myvideoroom-welcome-positive" />
			<input id="stop-chk-sound" type="button" value="Stop Check" class="myvideoroom-welcome-buttons mvr-hide" />
		</div>

			<?php
			if ( ! \is_user_logged_in() ) {
						//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
						echo Factory::get_instance( MVRSiteVideoViews::class )->render_login_page();
			}
			?>
</div>
	<?php

		return ob_get_clean();
};
