<?php
/**
 * Outputs the configuration settings for the login page
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Login-settings.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\LoginForm;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Render the admin page
 *
 * @param ?string $details_section Optional details section.
 *
 * @return string
 */
return function (): string {

	ob_start();

	?>
<h2><?php \esc_html_e( 'Login Tab Settings', 'myvideoroom' ); ?></h2>

<p>
	<?php
	\esc_html_e(
		'This setting defines the login tabs that will be shown to signed out users to allow them to login, you can use the default tab, or add a shortcode from your own Login solution',
		'myvideoroom'
	);
	?>
</p>
<div class = "mvr-nav-shortcode-outer-wrap ">
	<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --internally created sanitised function
		echo Factory::get_instance( LoginForm::class )->get_login_settings_page();
	?>
</div>

	<?php

		return ob_get_clean();
};
