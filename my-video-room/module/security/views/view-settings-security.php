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
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons;

return function (
	array $messages = array()
	): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	ob_start(); ?>
<div class="wrap">

		<div class="mvr-outer-box-wrap">
		<h1><?php esc_html_e( 'Advanced Room Permissions Control', 'my-video-room' ); ?></h1>
		<?php
		$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( Dependencies::MODULE_SECURITY_ID );
		if ( $security_enabled ) {
			echo esc_html( Factory::get_instance( SecurityButtons::class )->site_wide_enabled() );
		}
		?>
		<p> 
		<?php
			esc_html_e(
				'The advanced room permissions control module allows users, to precisely control the type of room access permissions they would like for their room. For example
				users can select logged in users, specific site roles, disable rooms entirely, or work in conjunction with other modules (like groups and friends in Buddypress). The module also
				provides central enforcement and override capability which allows central control of specific room settings, and configuration.',
				'my-video-room'
			);
		?>
		<br></p>

		<?php
				// Activation/module.
		if ( ! Factory::get_instance( ModuleConfig::class )->module_activation_button( Security::MODULE_SECURITY_ID ) ) {
			return '';
		}
		?>
		</div>
		<div class="mvr-outer-box-wrap">
		<div class="mvr-nav-shortcode-outer-wrap">
			<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
				<ul class="menu" >
					<a class="nav-tab nav-tab-active" href="#page1" style><?php esc_html_e( 'Default Permissions', 'my-video-room' ); ?> </a>
					<a class="nav-tab" href="#page2" style><?php esc_html_e( 'Override Permissions', 'my-video-room' ); ?> </a>
					<a class="nav-tab" href="#page4" style><?php esc_html_e( 'Security Shortcodes', 'my-video-room' ); ?></a>
				</ul>
			</nav>
				<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
					<article id="page1" style>
						<p> 
						<?php
						esc_html_e(
							'These are the Default Room Permissions. These settings will be used by the Room, if the user has not yet set up a permissions preference.
							Users\' preferences override these defaults if they choose them. To enforce settings use the Override Permissions tab.',
							'my-video-room'
						);
						?>
						</p>
						<?php
							$default_setting = Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
								SiteDefaults::USER_ID_SITE_DEFAULTS,
								Security::PERMISSIONS_TABLE,
								null,
							);
							// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
							echo $default_setting;
						?>
						</p>
					</article>
					<article id="page2" style>
					<br>
					<?php
						esc_html_e(
							'These are the enforced/mandatory room permissions. These settings will be used by the Room regardless of the User\'s preference.
							To allow the settings to be overriden please use the Default Permissions tab.',
							'my-video-room'
						);
					?>
							<br>
							<p>
							<?php
							$override_setting = Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
								SiteDefaults::USER_ID_SITE_DEFAULTS,
								SiteDefaults::ROOM_NAME_SITE_DEFAULT,
								null,
								'admin'
							);
						// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
						echo $override_setting;
							?>
							</p>
					</article>
					<article id="page4"></article>
				</div>	
			</div>
		</div>
		<?php
		return ob_get_clean();
};
