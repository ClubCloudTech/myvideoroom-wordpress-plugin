<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Module\Security\Views\view-settings-security.php
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons;

return function() {
	ob_start();
	?>
<div class="mvr-admin-page-wrap">
	<h1><?php esc_html_e( 'Room Permissions Control', 'my-video-room' ); ?></h1>
	<?php
		Factory::get_instance( SecurityButtons::class )->site_wide_enabled();
	?>
	<p>
		<?php
			esc_html_e(
				'The host and room permissions control module allows users, to precisely control the type of room access permissions they would like for their room. For example users can select logged in users, specific site roles, disable rooms entirely, or work in conjunction with other modules (like groups and friends in Buddypress). The module also provides central enforcement and override capability which allows central control of specific room settings, and configuration. Most settings are in the rooms and modules themselves and not in this section.',
				'my-video-room'
			);
		?>
		<br>
	</p>
	<?php
				// Activation/module.
			Factory::get_instance( ModuleConfig::class )->module_activation_button( Security::MODULE_SECURITY_ID );
	?>
</div>

	<?php
		return ob_get_clean();
};
