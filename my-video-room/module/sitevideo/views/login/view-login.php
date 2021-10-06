<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Login.php
 */

use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Render the admin page
 *
 * @param ?string $details_section Optional details section.
 *
 * @return string
 */
return function (
	bool $login_override = null,
	string $login_shortcode = null,
	string $room_name = null,
	string $redirect_url
): string {

	ob_start();

	?>

<div id="mvr-login-form" class="myvideoroom-center  myvideoroom-welcome-page" style="display:none;">
	<h2><?php esc_html_e( 'Have an Account?', 'my-video-room' ); ?></h2>
	<p id="myvideoroom-picturedescription-login" class="myvideoroom-table-adjust">
		<?php esc_html_e( 'You can login to access your previously stored room settings, baskets, lists, and pictures', 'myvideoroom' ); ?>
	</p>
	<?php

	if ( $login_override && strlen( $login_shortcode ) > 5 ) {
		$nonce = wp_create_nonce( $login_shortcode . MVRSiteVideo::ROOM_SLUG_REDIRECT );
		?>
<iframe id="iframe-login" src="/<?php echo esc_textarea( $redirect_url ) . '?nonce=' . esc_textarea( $nonce ) . '&shortcode=' . esc_textarea( $login_shortcode ) . '&action=' . esc_textarea( MVRSiteVideo::ROOM_SLUG_REDIRECT ); ?>" 
sandbox="allow-forms allow-scripts allow-same-origin" height="600" width="400" frameBorder="0" class=""></iframe>
		<?php 


	} else {

		global $wp;
		$args = array(
			'redirect' => home_url( $wp->request ),
			'remember' => true,
		);
		wp_login_form( $args );
	}

			?>
</div>

	<?php

		return ob_get_clean();
};
