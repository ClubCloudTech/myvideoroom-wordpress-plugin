<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Picture-Register.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\LoginForm;
use MyVideoRoomPlugin\Library\TemplateIcons;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;

/**
 * Render the Login page
 *
 * @param ?string $details_section Optional details section.
 * @param ?string $room_name Room Name.
 *
 * @return string
 */
return function (
	object $room_object
): string {
	$display_name = $room_object->get_user_display_name();
	$user_picture = $room_object->get_user_picture_url();

	$output = '<div id="mvr-name-greeting" >';
	if ( $display_name && $user_picture ) {
		$output .= '<strong>' . esc_html__( 'Welcome ', 'myvideoroom' ) . $display_name . '</strong>';
		$all_set = true;
	} elseif ( $display_name ) {
		$output .= '<strong>' . esc_html__( 'Welcome ', 'myvideoroom' ) . $display_name . '</strong>';
	} else {
		$output .= '<strong>' . esc_html__( 'Welcome, let\'s get you setup', 'myvideoroom' ) . '</strong>';
	}
	$output .= '</div>';

	ob_start();

	?>

<div id="myvideoroom-welcome-page" class="mvr-nav-settingstabs-outer-wrap myvideoroom-welcome-page">

	<div id="mvr-top-notification" class="myvideoroom-button-notification">

		<?php
		$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'name' );

		if ( ! \is_user_logged_in() && \get_option( LoginForm::SETTING_LOGIN_DISPLAY ) ) {
			$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'login' );
		}
		if ( $all_set || $display_name ) {
			$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'photo' );
			$output .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'checksound' );
		}

		/*phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped*/echo $output;
		?>

	</div>
	<?php
	if ( $all_set ) {
		?>
	<div id="myvideoroom-all-set" class="myvideoroom-center mvr-mobile-top-margin">
		<h2><?php esc_html_e( 'You\'re good to go', 'my-video-room' ); ?></h2>
		<p id="myvideoroom-allset-description" class="myvideoroom-table-adjust">
			<?php esc_html_e( 'You\'re all set to start your meeting. You can start your meeting, or check your sound first', 'myvideoroom' ); ?>
		</p>
		<input id="vid-down" type="button" value="Join Meeting" class="myvideoroom-welcome-positive" enabled />
		<?php /*phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped*/echo Factory::get_instance( TemplateIcons::class )->format_button_icon( 'checksound' ); ?>
		<hr>
	</div>
		<?php
	} elseif ( $display_name ) {
		?>
	<div id="myvideoroom-welcome-pictureadd" class="myvideoroom-center">
		<h2><?php esc_html_e( 'Add a meeting picture?', 'my-video-room' ); ?></h2>
		<p id="myvideoroom-welcome-setup-description" class="myvideoroom-table-adjust">
			<?php esc_html_e( 'Meetings are better when you can see who you are talking to. Would you like to add a picture, or check your sound settings?', 'myvideoroom' ); ?>
		</p>
		<?php
		$output_2  = Factory::get_instance( TemplateIcons::class )->format_button_icon( 'photo' );
		$output_2 .= Factory::get_instance( TemplateIcons::class )->format_button_icon( 'checksound' );
		$output_2 .= '<input id="vid-down" type="button" value="Join Meeting" class="myvideoroom-welcome-positive" />';
		echo /*phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped*/$output_2;
		?>
		<hr>
	</div>
		<?php
	} else {
		?>
	<div id="myvideoroom-welcome-setup" class="myvideoroom-center">
		<h2><?php esc_html_e( 'Nice to Meet You', 'my-video-room' ); ?></h2>
		<p id="myvideoroom-welcome-setup-description" class="myvideoroom-table-adjust">
			<?php esc_html_e( 'To start your meeting, you need to decide your meeting name that others can see in the floorplan, and optionally set a picture for yourself.', 'myvideoroom' ); ?>
		</p>
		<hr>
	</div>
		<?php
	}
	?>
	<div id="mvr-picture" class="myvideoroom-center" style="display:none;">
		<?php
		if ( $user_picture ) {
			echo '<h2>' . esc_html__( ' Update Your Meeting Picture?', 'my-video-room' ) . '</h2>';

		} else {
			echo '<h2>' . esc_html__( ' Set a Meeting Picture?', 'my-video-room' ) . '</h2>';
		}
		?>
		<div class="mvr-clear">
			<p id="myvideoroom-picturedescription" class="myvideoroom-table-adjust">
				<?php esc_html_e( 'You can select a picture for the room participants to see you in seating plans', 'myvideoroom' ); ?>
			</p>
		</div>
		<div id="mvr-picture-left" class="mvr-left">
			<p id="mvr-text-description-current" class="mvr-hide"><?php esc_html_e( 'Current', 'myvideoroom' ); ?></p>
			<?php
			if ( $user_picture ) {
				?>
			<img class="myvideoroom-image-result" src="
				<?php echo esc_url( $user_picture ); ?>" alt="Powered by MyVideoRoom">
				<?php
			} else {
				?>
			<p id="mvr-text-description-current2"><?php esc_html_e( 'No Picture yet', 'myvideoroom' ); ?></p>
				<?php
			}
			?>
		</div>


		<div id="mvr-picture-right" class="mvr-right">
			<p id="mvr-text-description-new" class=""><?php esc_html_e( 'Use Your Own Photo', 'myvideoroom' ); ?></p>
			<input type="file" accept=".gif,.jpg,.jpeg,.png" id="mvr-file-input" />
			<p id="mvr-text-description-23" class="mvr-hide"><?php esc_html_e( 'New', 'myvideoroom' ); ?></p>
			<video id="vid-live" autoplay class="mvr-header-section "></video>
			<div id="vid-result" class="mvr-header-section"></div>
		</div>

		<div class="mvr-flex mvr-clear">
			<input id="vid-retake" type="button" value="Retake" class="mvr-hide myvideoroom-welcome-buttons" />
			<input id="vid-take" type="button" value="Snap"
				class="myvideoroom-welcome-positive mvr-hide myvideoroom-welcome-button" />
			<input id="vid-up" type="button" value="Use This" class="myvideoroom-welcome-positive mvr-hide" />
			<input id="vid-picture" type="button" value="Take Picture" class="myvideoroom-welcome-positive" />
			<input id="upload-picture" type="button" value="Upload Picture"
				class="myvideoroom-welcome-positive mvr-hide" />

		</div>
	</div>

	<div id="myvideoroom-meeting-name" style="display:none;">
		<h2><?php esc_html_e( 'Your Display Name', 'my-video-room' ); ?></h2>
		<p id="myvideoroom-namedescription" class="myvideoroom-table-adjust">
			<?php esc_html_e( 'Your Display Name is what others will see you called in the Floorplan', 'myvideoroom' ); ?>
		</p>
		<input id="vid-name" type="text" placeholder="Meeting Display Name"
			value="<?php echo esc_textarea( $display_name ); ?>"
			class="myvideoroom-input-restrict-alphanumeric mvr-input-box" />
		<input id="room-name-update" type="button" value="Save" class="myvideoroom-welcome-positive" />

	</div>
	<div id="myvideoroom-checksound" class="myvideoroom-center" style="display:none;">
		<h2><?php esc_html_e( 'Check Your Sound and Camera?', 'my-video-room' ); ?></h2>
		<p id="myvideoroom-sounddescription" class="myvideoroom-table-adjust">
			<?php esc_html_e( 'You can use this handy entry room to get your sound and camera checked out before you enter the main room', 'myvideoroom' ); ?>
		</p>
		<input id="chk-sound" type="button" value="Check Camera and Sound" class="myvideoroom-welcome-positive" />
		<input id="stop-chk-sound" type="button" value="Stop Check" class="myvideoroom-welcome-buttons mvr-hide" />
	</div>
	<?php
	if ( ! \is_user_logged_in() && \get_option( LoginForm::SETTING_LOGIN_DISPLAY ) ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( MVRSiteVideoViews::class )->render_login_page( true );
	}
	if ( ! $all_set ) {
		?>
	<input id="vid-down" type="button" value="Join Meeting" class="myvideoroom-welcome-positive mvr-hide" disabled />
		<?php
	}
	?>
</div>
	<?php
		return ob_get_clean();
};
