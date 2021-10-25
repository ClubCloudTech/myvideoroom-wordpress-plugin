<?php
/**
 * Outputs the Login Form for Frontend
 *
 * @package my-video-room/module/sitevideo/views/login/view-login.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Dependencies;
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
	bool $login_iframe = null,
	string $login_shortcode = null,
	string $redirect_url,
	string $style = null
): string {

	ob_start();

	?>

<div id="mvr-login-form" data-logged-in="<?php echo esc_attr( is_user_logged_in() ); ?>" class="myvideoroom-center myvideoroom-welcome-page" style="<?php echo esc_attr( $style ); ?>">
	<h2><?php esc_html_e( 'Have an Account?', 'my-video-room' ); ?></h2>
	<p id="myvideoroom-picturedescription-login" class="myvideoroom-table-adjust">
		<?php esc_html_e( 'You can login to access your previously stored room settings, baskets, lists, and pictures', 'myvideoroom' ); ?>
	</p>
	<?php
	if ( $login_override && ! $login_iframe ) {
		if ( Factory::get_instance( Dependencies::class )->does_full_shortcode_exist( $login_shortcode ) ) {
			echo do_shortcode( '[' . $login_shortcode . ']' );
			echo '</div>';
			return ob_get_clean();
		}
	} elseif ( $login_override && $login_iframe && strlen( $login_shortcode ) > 5 ) {
		$nonce = wp_create_nonce( $login_shortcode . MVRSiteVideo::ROOM_SLUG_REDIRECT );
		?>
		<iframe id="iframe-login" src="/<?php echo esc_textarea( $redirect_url ) . '?nonce=' . esc_textarea( $nonce ) . '&shortcode=' . esc_textarea( $login_shortcode ) . '&action=' . esc_textarea( MVRSiteVideo::ROOM_SLUG_REDIRECT ); ?>" 
		sandbox="allow-forms allow-scripts allow-same-origin" height="600" width="400" frameBorder="0" class=""></iframe>
		</div>
		<?php
		return ob_get_clean();

	}
		// Fallthrough in case no setting above, or shortcode entered was invalid.
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
