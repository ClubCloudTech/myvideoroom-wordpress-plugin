<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\Security\Views\view-settings-security.php
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

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons;

return function () {
	ob_start();
	$index = 434;
	?>
	<div id="outer" class="mvr-nav-shortcode-outer-wrap mvr-nav-shortcode-outer-border">
<!-- Module Header -->
<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-lock"></i><?php esc_html_e( 'Room Security and Hosts Control', 'myvideoroom' ); ?>
			</h1>
			<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped*/ echo Factory::get_instance( SecurityButtons::class )->site_wide_enabled(); ?>
		</div>
		<div class="myvideoroom-header-table-right">
		</div>
	</div>
<!-- Module State and Description Marker -->
		<div class="myvideoroom-feature-outer-table myvideoroom-clear">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Module', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
				echo Factory::get_instance( ModuleConfig::class )->module_activation_button( Security::MODULE_SECURITY_ID );
				?>
			</div>
		</div>

		<div class="myvideoroom-feature-table-large">
			<p>
				<?php
				esc_html_e(
					'The host and room permissions control module allows users, to precisely control the type of room access permissions they would like for their room. For example users can select logged in users, specific site roles, disable rooms entirely, or work in conjunction with other modules (like groups and friends in Buddypress).',
					'myvideoroom'
				);
				?>
			</p>
			<p>
				<?php
				esc_html_e(
					'The module also provides central enforcement and override capability which allows central control of specific room settings, and configuration. Most settings are in the rooms and modules themselves and not in this section. Lastly, the module provides custom hosts capability allowing users to select hosts for certain room types like Conference Rooms',
					'myvideoroom'
				);
				?>
			</p>
		</div>
	</div>
<!-- Navigation Menu Section -->
	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
		<ul class="mvr-ul-style-top-menu">
			<li>
				<a class="nav-tab nav-tab-active" href="#defaultperms">
					<?php esc_html_e( 'Default Permissions', 'my-video-room' ); ?>
				</a>
			</li>
			<li>
				<a class="nav-tab" href="#overrideperms">
					<?php esc_html_e( 'Override Permissions', 'my-video-room' ); ?>
				</a>
			</li>
		</ul>
	</nav>

	<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
<!-- 
		Default Permissions.
-->		
		<article id="defaultperms">
		<div class="myvideoroom-menu-settings">
					<div class="myvideoroom-header-table-left">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-unlock"></i><?php esc_html_e( 'Default Permissions', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right">
					</div>
				</div>	
<!-- Module State and Description Marker -->

				<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'What This Does', 'myvideoroom' ); ?></h2>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<p>
							<?php
							esc_html_e(
								'These are the Default Room Permissions. These settings will be used by the Room, if the user has not yet set up a permissions preference. Users\' preferences override these defaults if they choose them. To enforce settings use the Override Permissions tab.',
								'myvideoroom'
							);
							?>
						</p>
						<h4><?php esc_html_e( 'An Example', 'myvideoroom' ); ?></h4>
						<p>
							<?php
							esc_html_e(
								'If you would like to restrict anonymous (signed out) across all new rooms by default, you can apply a default permission here. Your users could still chose to allow anonymous users, but they would have to change the preference themselves.',
								'myvideoroom'
							);
							?>
						</p>
					</div>
				</div>
<!-- Default Video Section  -->
					<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
						class="mvr-nav-settingstabs-outer-wrap">
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Default Settings', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
								<h2><?php esc_html_e( 'Personal and BuddyPress Profile Room Default Settings', 'myvideoroom' ); ?>
								</h2>
								<p> <?php esc_html_e( ' Default Room Privacy (reception) and Layout settings. These settings will be used by all Rooms, until users set their own room preference', 'myvideoroom' ); ?>
								</p>
								<?php
									$default_setting = Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
										SiteDefaults::USER_ID_SITE_DEFAULTS,
										Security::PERMISSIONS_TABLE
									);
									// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
									echo $default_setting;
								?>
							</div>
						</div>




		
		</article>
		<article id="overrideperms">
			<br>
			<?php
			esc_html_e(
				'These are the enforced/mandatory room permissions. These settings will be used by the Room regardless of the User\'s preference. To allow the settings to be overriden please use the Default Permissions tab.',
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
	</div>

	<?php
	return ob_get_clean();
};
