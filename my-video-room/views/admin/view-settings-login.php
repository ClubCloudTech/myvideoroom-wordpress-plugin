<?php
/**
 * Output of Login Page
 *
 * @package MyVideoRoomPlugin\Admin\Views\view-settings-login.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;

return function (
	int $id_index = 0,
	bool $login_override = null,
	string $login_shortcode = null
): string {
	ob_start();

	?>
<div class="mvr-woocommerce-overlay mvr-nav-shortcode-outer-wrap">
	<div id="loginsettings" class="mvr-nav-settingstabs-outer-wrap ">
		<form method="post" action="" class="settingspost">
			<label for="myvideoroom_login_override_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Override Default WordPress Login Form', 'myvideoroom' ); ?></strong>
			</label>
			<input type="checkbox" class="myvideoroom_login_override myvideoroom-separation" name="myvideoroom_login_override"
				id="myvideoroom_login_override_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $login_override ? 'checked' : ''; ?> />
			<?php echo esc_html_e( '(Leave Blank for WordPress default login)', 'myvideoroom' ); ?>
			<br><br>
			<label for="myvideoroom_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph myvideoroom-separation">
				<?php esc_html_e( 'Shortcode of Login Application to use ', 'myvideoroom' ); ?>
			</label>
			<h2 class="myvideoroom-inline">[</h2>
			<input type="text" id="myvideoroom_login_shortcode" name="myvideoroom_login_shortcode"
				class="mvr-input-box myvideoroom-input-restrict-alphanumeric"
				value="<?php echo esc_textarea( $login_shortcode ); ?>">
			<h2 class="myvideoroom-inline">]</h2>

			<br>
			<label for="myvideoroom_login_iframe_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Use Iframe for Login Page', 'myvideoroom' ); ?></strong>
			</label>
			<input type="checkbox" class="myvideoroom_login_iframe" name="myvideoroom_login_iframe"
				class="myvideoroom-separation" id="myvideoroom_login_override_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $login_override ? 'checked' : ''; ?> />
				<p><?php echo esc_html_e( 'Instead of putting the shortcode on the page, the shortcode will be run from a window inside an Iframe. Some applications may post and redirect post login which will force your guest to leave the room if they try to login.', 'myvideoroom' ); ?></p>
				<p><?php echo esc_html_e( 'You can use an Iframe instead to allow for more complicated scenarios of login without breaking the room flow, and we will refresh the page if we detect a successful signin.', 'myvideoroom' ); ?></p>
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
