<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference as UserVideoPreference;
use MyVideoRoomPlugin\SiteDefaults as MyVideoRoomPluginSiteDefaults;

/**
 * Render the admin page
 *
 * @return string
 */
return function (): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	wp_enqueue_script( 'myvideoroom-protect-input' );
	ob_start();

	?>

	<h2>
		<?php esc_html_e( 'Site Conference Center Settings', 'my-video-room' ); ?>
	</h2>

	<?php
		// Activation/module.
		if ( ! Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
			return '';
		}
	?>

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
		$site_conference_url = \menu_page_url( MVRSiteVideo::PAGE_SLUG_SITE_CONFERENCE, false );
		printf(
			/* translators: %s is a link to the site conference center */
			esc_html__( 'To get started please create your first room in the %s', 'myvideoroom' ),
			'<a href="' . esc_url_raw( $site_conference_url ) . '">' . esc_html__( 'Site conference center', 'myvideoroom' ) . '</a>'
		);
		?>
	</p>

	<hr />
	<h3>
		<?php esc_html_e( 'Default settings', 'my-video-room' ); ?>
	</h3>
	<p>
		<?php esc_html_e( 'These will be the default settings used for all site conference rooms.', 'my-video-room' ); ?>
	</p>
	<?php

	//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Escaped.
	echo Factory::get_instance( UserVideoPreference::class )->choose_settings(
		MyVideoRoomPluginSiteDefaults::USER_ID_SITE_DEFAULTS,
		esc_attr( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ),
		array( 'basic', 'premium' )
	);

	return ob_get_clean();
};