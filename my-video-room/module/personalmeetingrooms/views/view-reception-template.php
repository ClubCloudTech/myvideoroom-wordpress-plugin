<?php
/**
 * Outputs the Reception Tab for Guests to enter meeting code or Host username.
 *
 * @package /my-video-room/module/personalmeetingrooms/views/view-reception-template.php
 */

/**
 * Outputs the Reception Page for Guests to enter meeting code or Host username
 *
 * @return string
 */
return function (): string {
	ob_start();
	?>
<div id="video-host-wrap" class="mvr-nav-shortcode-outer-wrap">

	<div class="mvr-header-table-left mvr-personal-video-welcome">
		<h2 class="mvr-header-title">
			<?php echo esc_html__( 'Welcome to ', 'myvideoroom' ) . esc_html( get_bloginfo( 'name' ) ); ?></h2>
		<img class="myvideoroom-user-image" src="
			<?php
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( ! $image ) {
				$image = plugins_url( '/images/logoCC-clear.png', __FILE__ );
				echo esc_url( $image );
			} else {
				echo esc_url( $image[0] );
			}
			?>
			" alt="Site Logo">
	</div>
	<div class="mvr-header-table-right mvr-personal-video-welcome">
		<h2 class="mvr-header-title"><?php esc_html_e( 'Please Select Your Meeting Host', 'myvideoroom' ); ?></h2>
		<form action="">
			<label for="host"
				class="mvr-header-label"><?php esc_html_e( 'Host\'s Username:', 'myvideoroom' ); ?></label>
			<input type="text" id="host" name="host" class="mvr-select-box">
			<p class="mvr-title-label">
				<?php esc_html_e( 'This is the Site Username for the user you would like to join', 'myvideoroom' ); ?>
			</p>
			<h2 class="mvr-header-title"><?php esc_html_e( 'OR', 'myvideoroom' ); ?></h2>
			<label for="host" class="mvr-header-label"><?php esc_html_e( 'Host\'s Invite Code:', 'myvideoroom' ); ?>
			</label>
			<input type="text" id="invite" name="invite" class="mvr-select-box">
			<p class="mvr-title-label">
				<?php esc_html_e( 'This is the Invite Code XXX-YYY-ZZZ for the meeting', 'myvideoroom' ); ?></p>
			<input type="submit" value="Submit" class="mvr-form-button">
		</form>
	</div>
	<div class="mvr-clear"></div>
</div>

	<?php
	return ob_get_clean();
};
