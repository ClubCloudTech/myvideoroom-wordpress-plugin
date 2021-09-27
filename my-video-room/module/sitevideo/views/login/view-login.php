<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Login.php
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

<div id="mvr-login-form" class="myvideoroom-center  myvideoroom-welcome-page" style="display:none;">
	<h2><?php esc_html_e( 'Have an Account?', 'my-video-room' ); ?></h2>
	<p id="myvideoroom-picturedescription" class="myvideoroom-table-adjust">
		<?php esc_html_e( 'You can login to access your previously stored room settings, baskets, lists, and pictures', 'myvideoroom' ); ?>
	</p>
	<?php
			global $wp;
			$args = array(
				'redirect' => home_url( $wp->request ),
				'remember' => true,
			);

			wp_login_form( $args );

			?>
</div>

	<?php

		return ob_get_clean();
};
