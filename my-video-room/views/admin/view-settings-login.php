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
		<h2><?php esc_html_e( 'Login Settings ', 'my-video-room' ); ?></h2>

		<form method="post" action="" class="settingspost">
			<label for="myvideoroom_login_override_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Override Default WordPress Login Form', 'my-video-room' ); ?></strong>
			</label>
			<input type="checkbox" class="myvideoroom_login_override" name="myvideoroom_login_override"
				class="myvideoroom-separation" id="myvideoroom_login_override_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $login_override ? 'checked' : ''; ?> />
			<?php echo esc_html_e( '(Leave Blank for WordPress default login)', 'myvideoroom' ); ?>
			<br><br>
			<label for="myvideoroom_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph myvideoroom-separation">
				<?php esc_html_e( 'Shortcode of Login Application to use ', 'my-video-room' ); ?>
			</label>
			<h2 class="myvideoroom-inline">[</h2>
			<input type="text" id="myvideoroom_login_shortcode" name="myvideoroom_login_shortcode"
				class="mvr-input-box myvideoroom-input-restrict-alphanumeric"
				value="<?php echo esc_textarea( $login_shortcode ); ?>">
			<h2 class="myvideoroom-inline">]</h2>

			<br><br>

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( HttpPost::class )->create_form_submit(
				'login_setting',
				\esc_html__( 'Save changes', 'myvideoroom' )
			);
			?>
		</form>
	</div>
	<div class="mvr-flex">
		<div class="myvideoroom-table-split-left myvideoroom-split-padding ">
			<img class="" src="
				<?php echo esc_url( plugins_url( '/../../img/login.png', __FILE__ ) ); ?>" alt="Powered by MyVideoRoom">
		</div>
		<div class="myvideoroom-table-split-right myvideoroom-split-padding">
			<h2><?php esc_html_e( 'What is this setting ?', 'myvideoroom' ); ?></h2>
			<p> <?php esc_html_e( 'The Welcome Center contains a login page for users, as do other pages like the personal video room reception. Use this setting to change what application handles the login event', 'myvideoroom' ); ?>
			</p>
			<p> <?php esc_html_e( 'Your site can use the default WordPress login experience or if you have your own plugin for authentication, you can put the shortcode for its login form here, and it will be applied in our login pages', 'myvideoroom' ); ?>
			</p>
		</div>
		<div class="myvideoroom-clear"></div>
	</div>
</div>
	<?php
	return ob_get_clean();
};
