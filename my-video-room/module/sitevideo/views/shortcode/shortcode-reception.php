<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\Sitevideo\views\shortcode\shortcode-reception.php
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
	$index_num = wp_rand( 2, 2000 );
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

	?>
<div id="mvr-shortcode-maintable" class="mvr-woocommerce-overlay">
	<div class="mvr-admin-page-wrap">
		<h2 class="mvr-override-h2"><?php esc_html_e( 'Room Reception Center', 'myvideoroom' ); ?></h2>

		<button class="mvr-ul-style-menu  myvideoroom-sitevideo-add-room-button myvideoroom-button-override">
			<i class="myvideoroom-dashicons dashicons-plus-alt"></i>
			<?php esc_html_e( 'Add new room', 'myvideoroom' ); ?>
		</button>

		<button class="mvr-ul-style-menu myvideoroom-sitevideo-settings myvideoroom-button-override"
			data-room-id="<?php echo esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>"
			data-input-type="<?php echo esc_attr( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ); ?>">
			<a href="#"
				data-room-id="<?php echo esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>"
				data-input-type="<?php echo esc_attr( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ); ?>"
				class="myvideoroom-sitevideo-settings mvr-button-fix ">
				<i class="myvideoroom-dashicons dashicons-admin-settings"></i>
				<?php esc_html_e( 'Default Room Appearance', 'myvideoroom' ); ?></a>
		</button>
		<button id="mvr-close_<?php echo esc_attr( $index_num ); ?>"
			class="mvr-ul-style-menu myvideoroom-sitevideo-hide-button myvideoroom-sitevideo-settings myvideoroom-button-override"
			data-room-id="<?php echo esc_attr( $index_num ); ?>" data-input-type="close" style="display:none;">
			<i class="dashicons dashicons-dismiss"></i>
			<?php esc_html_e( 'Close', 'myvideoroom' ); ?>
		</button>
		<hr />

		<div class="myvideoroom-sitevideo-add-room">
			<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/../shortcode/add-new-room.php' )();
			?>
			<hr />
		</div>
		<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host"
			data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>">
			<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $details_section;
			?>
		</div>
		<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO, true );
		?>
	</div>

	<?php
	return ob_get_clean();
};
