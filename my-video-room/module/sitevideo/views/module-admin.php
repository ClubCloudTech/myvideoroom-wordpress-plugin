<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Render the admin page
 *
 * @return string
 */
return function (
	$details_section = null
): string {
	ob_start();
	$settings_url = \add_query_arg( \esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ) );
	?>
	<div class="mvr-admin-page-wrap">
		<h2><?php esc_html_e( 'Site Conference Center Settings', 'my-video-room' ); ?></h2>
		<?php Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRSiteVideo::MODULE_SITE_VIDEO_ID ); ?>
		<p>
			<?php
			esc_html_e(
				'The site conference module suite is available for team wide meetings, events, or any need for central rooms at the website level. These permanent rooms are created automatically by the module, at activation, and can be renamed. They can be individually secured such that any site role group can host the room. Room permissions, reception settings, templates, and custom reception videos are all available for each conference room. You can connect permanent WebRTC enabled devices like projectors, and microphones to rooms permanently',
				'my-video-room'
			);
			?>
		</p>

		<p>
			<?php
			printf(
			/* translators: %s is a link to the site conference center */
				esc_html__( 'To add additional rooms, click add new room', 'myvideoroom' )
			);
			?>
		</p>
		<button class="button button-primary myvideoroom-sitevideo-add-room-button">
		<i class="dashicons dashicons-plus-alt"></i>
		<?php esc_html_e( 'Add new room', 'my-video-room' ); ?>
		</button>

	<button class="button button-primary">
	<a href="<?php echo esc_url_raw( $settings_url ) . '&room_id=' . esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>" data-room-id="<?php echo esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>" data-input-type="<?php echo esc_attr( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ); ?>" class="myvideoroom-sitevideo-settings myvideoroom-button-link">
	<i class="dashicons dashicons-admin-settings"></i>
	<?php esc_html_e( 'Default Site Conference Appearance', 'my-video-room' ); ?></a>
	</button>

	<hr />

	<div class="myvideoroom-sitevideo-add-room">
		<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/add-new-room.php' )();
		?>
		<hr />
	</div>

	<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
	?>
		<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host"
			data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>">
			<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $details_section;
			?>
		</div>
		<hr />
	</div>
	<?php
	return ob_get_clean();
};
