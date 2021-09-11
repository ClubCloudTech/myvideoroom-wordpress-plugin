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

	if ( ! is_user_logged_in() ) {
		?><div class="mvr-admin-page-wrap">
		<h2><?php esc_html_e( 'Please Sign in to Improve Your Experience', 'my-video-room' ); ?></h2>
		<?php
		global $wp;
		$args = array(
			'redirect' => home_url( $wp->request ),
		);
		wp_login_form( $args );

		?>
	</div>
		<?php
	}

	return ob_get_clean();
};
