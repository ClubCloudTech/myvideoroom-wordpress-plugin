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
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;


return function() {
	wp_enqueue_script( 'myvideoroom-outer-tabs' );
	ob_start();
	$page = require __DIR__ . '/../views/view-settings-securityheader.php';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- View is already escaped and sanitised.
	echo $page();
	?>
		</div>
		<div class="mvr-admin-page-wrap">
			<nav class="myvideoroom-nav-tab-wrapper">
				<ul>
					<li>
						<a class="nav-tab nav-tab-active" href="#page4231">
							<?php esc_html_e( 'Default Permissions', 'my-video-room' ); ?>
						</a>
					</li>

					<li>
						<a class="nav-tab" href="#page4312">
							<?php esc_html_e( 'Override Permissions', 'my-video-room' ); ?>
						</a>
					</li>

					<li>
						<a class="nav-tab" href="#page434">
							<?php esc_html_e( 'Security Shortcodes', 'my-video-room' ); ?>
						</a>
					</li>
				</ul>
			</nav>
			<br><br>
				<div id="video-host-wrap" class="mvr-admin-page-wrap">
					<div id="page4231">
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
						</div>
					<div id="page4312" >
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
						</div>
					<div id="page434">Content</div>
				</div>

		<?php
		return ob_get_clean();
};
