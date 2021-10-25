<?php
/**
 * Output of Login Page Configuration Page in Admin Backend.
 *
 * @package my-video-room/views/admin/view-settings-login.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\LoginForm;

return function (
	int $id_index = 0,
	bool $login_display = null,
	bool $login_override = null,
	bool $login_iframe = null,
	string $login_shortcode = null
): string {
	ob_start();

	?>
<div class="mvr-woocommerce-overlay mvr-nav-shortcode-outer-wrap">
	<div id="loginsettings" class="mvr-nav-settingstabs-outer-wrap ">
		<form method="post" action="" class="settingspost">
		<p><label for="myvideoroom_login_display_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Show a Login Page for Signed Out Users', 'myvideoroom' ); ?></strong>
			</label>
			<input type="checkbox" class="myvideoroom_login_display myvideoroom-separation" name="<?php echo 'myvideoroom_' . esc_attr( LoginForm::SETTING_LOGIN_DISPLAY ); ?>"
				id="<?php echo esc_attr( LoginForm::SETTING_LOGIN_DISPLAY ); ?>_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $login_display ? 'checked' : ''; ?> />
</p><hr>
<p>
			<label for="myvideoroom_login_override_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Override Default WordPress Login Form', 'myvideoroom' ); ?></strong>
			</label>
			<input type="checkbox" class="myvideoroom_login_override myvideoroom-separation" name="<?php echo 'myvideoroom_' . esc_attr( LoginForm::SETTING_LOGIN_OVERRIDE ); ?>"
				id="<?php echo esc_attr( LoginForm::SETTING_LOGIN_OVERRIDE ); ?>_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $login_override ? 'checked' : ''; ?> />
			<?php echo esc_html_e( '(Leave Blank for WordPress default login)', 'myvideoroom' ); ?>
</p>
<p>
			<label for="myvideoroom_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph myvideoroom-separation">
				<?php esc_html_e( 'Shortcode of Login Application to use (setting only applied if Login and Override Default are checked above. ', 'myvideoroom' ); ?>
			</label>
			<h2 class="myvideoroom-inline">[</h2>
			<input type="text" id="<?php echo esc_attr( LoginForm::SETTING_LOGIN_SHORTCODE ); ?>" name="<?php echo 'myvideoroom_' . esc_attr( LoginForm::SETTING_LOGIN_SHORTCODE ); ?>"
				class="mvr-input-box myvideoroom-input-restrict-alphanumeric"
				value="<?php echo esc_textarea( $login_shortcode ); ?>">
			<h2 class="myvideoroom-inline">]</h2>
</p>
			<br>
			<label for="<?php echo esc_attr( LoginForm::SETTING_LOGIN_IFRAME ); ?>_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Use Iframe for Login Page', 'myvideoroom' ); ?></strong>
			</label>
			<input type="checkbox" class="<?php echo esc_attr( LoginForm::SETTING_LOGIN_IFRAME ); ?>" name="<?php echo 'myvideoroom_' . esc_attr( LoginForm::SETTING_LOGIN_IFRAME ); ?>"
				class="myvideoroom-separation" id="<?php echo esc_attr( LoginForm::SETTING_LOGIN_IFRAME ); ?>_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $login_iframe ? 'checked' : ''; ?> />
				<p><?php echo esc_html_e( 'Instead of putting the shortcode on the page, the shortcode will be run from a window inside an Iframe. Some applications may post and redirect post login which will force your guest to leave the room if they try to login. If this is happening and your calls are breaking you can run the login form inside an Iframe which will keep users inside the call.', 'myvideoroom' ); ?></p>
				<p><?php echo esc_html_e( 'The system will automatically refresh the page if we detect a successful signin inside the Iframe.', 'myvideoroom' ); ?></p>
				<hr>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( HttpPost::class )->create_form_submit(
				'login_setting',
				\esc_html__( 'Save changes', 'myvideoroom' )
			);
			?>
		</form>
	</div>
</div>
	<?php
	return ob_get_clean();
};
