<?php
/**
 * Outputs the View for Guests to enter meeting code or username.
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

/**
 * Render the Meet Center Meeting Host or Invite Code Prompt View
 *
 * @return string
 */
return function (): string {
	ob_start();
	?>
<div id="video-host-wrap" class="mvr-nav-shortcode-outer-wrap">

	<div class="mvr-header-table-left">
		<h2 class="mvr-header-title">
			<?php echo esc_html__( 'Welcome to ', 'my-video-room' ) . esc_html( get_bloginfo( 'name' ) ); ?></h2>
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
	<div class="myvideoroom-table-right-split">
		<h2 class="mvr-header-title"><?php esc_html_e( 'Please Select Your Meeting Host', 'my-video-room' ); ?></h2>
		<form action="">
			<label for="host"
				class="mvr-header-label"><?php esc_html_e( 'Host\'s Username:', 'my-video-room' ); ?></label>
			<input type="text" id="host" name="host" class="mvr-select-box">
			<p class="mvr-title-label">
				<?php esc_html_e( 'This is the Site Username for the user you would like to join', 'my-video-room' ); ?>
			</p>
			<h2 class="mvr-header-title"><?php esc_html_e( 'OR', 'my-video-room' ); ?></h2>
			<label for="host" class="mvr-header-label"><?php esc_html_e( 'Host\'s Invite Code:', 'my-video-room' ); ?>
			</label>
			<input type="text" id="invite" name="invite" class="mvr-select-box">
			<p class="mvr-title-label">
				<?php esc_html_e( 'This is the Invite Code XXX-YYY-ZZZ for the meeting', 'my-video-room' ); ?></p>
			<input type="submit" value="Submit" class="mvr-form-button">
		</form>
	</div>
</div>

	<?php
	return ob_get_clean();
};
