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
	$index        = 331;
	?>
	<div class="mvr-nav-shortcode-outer-wrap mvr-nav-shortcode-outer-border">
<!-- Module Header -->
	<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-groups"></i><?php esc_html_e( 'Conference Center Permanent Rooms', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right">
		</div>
	</div>
<!-- Module State and Description Marker -->
	<div class="myvideoroom-feature-outer-table">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Module State', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
				<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
					echo Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRSiteVideo::MODULE_SITE_VIDEO_ID );
				?>
			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
		<h2><?php esc_html_e( 'Permanent Rooms with Custom Hosts, Reception, and Security', 'myvideoroom' ); ?></h2>	
			<p>
				<?php
				esc_html_e(
					'The site conference module suite is available for team wide meetings, events, or any need for central rooms at the website level. These permanent rooms are created automatically by the module, at activation, and can be renamed. They can be individually secured such that any site role group can host the room. Room permissions, reception settings, templates, and custom reception videos are all available for each conference room. You can connect permanent WebRTC enabled devices like projectors, and microphones to rooms permanently.',
					'myvideoroom'
				);
				?>
			</p>
		</div>
	</div>
<!-- Dependencies and Requirements Marker -->
	<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
				<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Requirements:', 'myvideoroom' ); ?></h2>
					</div>
					<div class="myvideoroom-feature-table-large">
						<p>
							<?php
							esc_html_e(
								'This module has no dependencies. If you disable it all rooms that are created still exist, but the shortcodes will not return rooms. WordPress pages created by rooms will remain in case you have customised the layout or design.',
								'myvideoroom'
							);
							?>
						</p>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
						<p><?php esc_html_e( 'You can enable and disable the custom conference center rooms without affecting other room types safely. Users will receive an access denied page on accessing a room with a disabled module.', 'myvideoroom' ); ?></p>
						</div>
					</div>
				</div>
<!-- Screenshot Marker -->
<div class="myvideoroom-feature-outer-table">
					<div class="myvideoroom-feature-table">
						<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
						<img class=""
							src="<?php echo esc_url( plugins_url( '/../../../admin/img/receptioncenter.jpg', __FILE__ ) ); ?>"
							alt="Reception Center">

					</div>
					<div class="myvideoroom-feature-table">
						<br>
						<img class=""
							src="<?php echo esc_url( plugins_url( '/../../../admin/img/sitevideoroom.jpg', __FILE__ ) ); ?>"
							alt="Site Video Room">
					</div>
					<div class="myvideoroom-feature-table">
					<img class=""
							src="<?php echo esc_url( plugins_url( '/../../../admin/img/sitevideocustomhosts.jpg', __FILE__ ) ); ?>"
							alt="Custom Hosts">
					</div>
				</div>
<!-- Settings Marker -->
				<div class="myvideoroom-menu-settings">
						<div class="myvideoroom-header-table-left">
							<h1><i
									class="myvideoroom-header-dashicons dashicons-admin-settings"></i><?php esc_html_e( 'Settings - Conference Center Permanent Rooms', 'myvideoroom' ); ?>
							</h1>
						</div>
						<div class="myvideoroom-header-table-right">

						</div>
					</div>

<!-- Reception Area -->					
		<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Room Management', 'myvideoroom' ); ?></h2>
					</div>
					<div class="myvideoroom-feature-table-large">
					<div class="myvideoroom-inline">

						<button class="button button-primary myvideoroom-sitevideo-add-room-button">
							<i class="dashicons dashicons-plus-alt"></i>
							<?php esc_html_e( 'Add new room', 'my-video-room' ); ?>
						</button>

						<button class="button button-primary myvideoroom-sitevideo-settings myvideoroom-button-link"
						data-room-id="<?php echo esc_attr( SiteDefaults::USER_ID_SITE_DEFAULTS ); ?>"
						data-input-type="<?php echo esc_attr( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ); ?>"
						>
						<i class="dashicons dashicons-admin-settings"></i>
							<?php esc_html_e( 'Default Room Settings', 'my-video-room' ); ?>
						</button>
						<?php $index_num = $index++; ?>
						<button id= "mvr-close_<?php echo esc_attr( $index_num ); ?>"
						class="button button-primary myvideoroom-sitevideo-hide-button myvideoroom-sitevideo-settings"
						data-room-id="<?php echo esc_attr( $index_num ); ?>"
						data-input-type="close" style="display:none;">
							<i class="dashicons dashicons-dismiss"></i>
							<?php esc_html_e( 'Close', 'my-video-room' ); ?>
						</button>


						<div class="myvideoroom-sitevideo-add-room mvr-nav-shortcode-outer-border myvideoroom-addroom">
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
					<!-- Frame Target Ajax -->		
						<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host"
							data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>">
							<?php
							//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $details_section;
							?>
						</div>


			<h4><?php esc_html_e( 'Customizing the Reception', 'my-video-room' ); ?></h4>
				<?php esc_html_e( 'You can edit your room look and feel with any page editor of your choice. You can also add multiple reception centers by adding the shortcode: ', 'my-video-room' ); ?> 
				<strong> [myvideoroom_meetswitch]</strong>	
				<?php esc_html_e( ' to the page. ', 'my-video-room' ); ?>

			<?php
			esc_html_e(
				"You can change the room name, its URL, and its parent page in the normal pages interface of WordPress. Please note whilst the system updates its internal links if you change the meeting page URL external emails, or other invites may not be updated by your users' guests. Its a good idea to link to reception page from the main area of your site.",
				'my-video-room'
			);
			?>

			<h4><?php esc_html_e( 'Who is a Host ?', 'my-video-room' ); ?></h4>

			<?php
			esc_html_e(
				'You can edit a room host by clicking on the room settings cog, and editing the host configuration you wish, you can use groups, anonymous users, and even allow all except a group. ',
				'myvideoroom'
			);
			?>

			<?php
			esc_html_e(
				'Host settings also apply to Site Admins who will follow the same permissions as any other user. Rooms are encrypted for protection, admins, and even ClubCloud can not see inside meetings. ',
				'myvideoroom'
			);
			?>

						</div>
					</div>
				</div>

	</div>
	<?php
	return ob_get_clean();
};
