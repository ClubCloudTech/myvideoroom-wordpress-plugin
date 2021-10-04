<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Public\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array  $tabs
 * @param array  $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\SiteDefaults;


return function (): string {
	ob_start();

	?>
	<div class="mvr-admin-page-wrap">
		<h1><?php esc_html_e( 'Video Room Site Default Configuration', 'my-video-room' ); ?></h1>
		<p>
			<?php
			esc_html_e(
				'The following settings define site wide video default parameters in case other modules have not set parameters there. These defaults will be used if a user has not selected a setting for the room configuration, or it hasnt been defined at the module level. You can use the Template Browser tab to view room selection templates.',
				'my-video-room'
			);
			?>
		</p>
	</div>

	<div class="mvr-admin-page-wrap">

		<?php
		$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);
		//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
		echo $layout_setting;
		?>
	</div>
	<?php
	return ob_get_clean();
};

