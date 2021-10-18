<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\SiteDefaults;

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
	\wp_enqueue_script( 'myvideoroom-monitor' );
	ob_start();

	if ( ! is_user_logged_in() ) {
		?><div class="mvr-admin-page-wrap">
		<h2><?php esc_html_e( 'Please Sign in to Access Reception', 'myvideoroom' ); ?></h2>
		<?php
		global $wp;
		$args = array(
			'redirect' => home_url( $wp->request ),
		);
		wp_login_form( $args );

		?>
	</div>
		<?php
		return ob_get_clean();
	}

	$settings_url = \add_query_arg( \esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ) );
	?>
	<div class="mvr-woocommerce-overlay">
	<div class="mvr-admin-page-wrap">
		<h2><?php esc_html_e( 'Room Reception Center', 'myvideoroom' ); ?></h2>

		<button class="mvr-ul-style-menu  myvideoroom-sitevideo-add-room-button">
		<i class="myvideoroom-dashicons dashicons-plus-alt"></i>
		<?php esc_html_e( 'Add new room', 'myvideoroom' ); ?>
		</button>

	<button class=" mvr-ul-style-menu ">
	<a href="<?php echo esc_url_raw( $settings_url ) . '&room_id=' . esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>" data-room-id="<?php echo esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>" data-input-type="<?php echo esc_attr( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ); ?>" class="myvideoroom-sitevideo-settings myvideoroom-button-link mvr-button-fix">
	<i class="myvideoroom-dashicons dashicons-admin-settings"></i>
	<?php esc_html_e( 'Default Room Appearance', 'myvideoroom' ); ?></a>
	</button>

	<hr />

	<div class="myvideoroom-sitevideo-add-room">
		<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/../add-new-room.php' )();
		?>
		<hr />
	</div>

	<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO, true );
	?>

	<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host"
			data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>">
			<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $details_section;
			?>
		</div>
</div>

	<?php
	return ob_get_clean();
};
