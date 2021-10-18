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
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */
use \MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;

return function (): string {
	ob_start();
	$index = 1;
	?>

<div class="mvr-nav-shortcode-outer-wrap mvr-nav-shortcode-outer-border">
	<!-- Module Header -->
	<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-video-alt"></i><?php esc_html_e( 'Personal Meeting Rooms for Users', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right-wide">
		</div>
	</div>
	<!-- Module State and Description Marker -->
	<div class="myvideoroom-feature-outer-table">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Feature', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
				<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
					echo Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID );
				?>
			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<h2><?php esc_html_e( 'Personal Meeting Rooms for Each User in WordPress', 'myvideoroom' ); ?></h2>
			<p>
				<?php
				esc_html_e(
					'A Personal Meeting Room is an individually controlled meeting room with its own Reception Area, Room Layout Selection, Privacy, and Room Permissions. A Reception page is created automatically with the module to handle Guest Arrival. A user is the host of their own room, and everyone else is a guest. Users can send invites by email, or by special unique invite code.',
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
								'This Addin Pack provides support for the Personal Meeting Room engine, and any plugins/extensions that require the pack.',
								'myvideoroom'
							);
					?>
				</p>
				<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
					<p><?php esc_html_e( 'Dependency Check - There are No Dependencies for this Module', 'myvideoroom' ); ?>
					</p>
				</div>
			</div>
		</div>
		<!-- Screenshot Marker -->
		<div class="myvideoroom-feature-outer-table">
			<div class="myvideoroom-feature-table">
				<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
				<img class=""
					src="<?php echo esc_url( plugins_url( '/../../../admin/img/videoroombp.jpg', __FILE__ ) ); ?>"
					alt="BuddyPress Room">
			</div>
			<div class="myvideoroom-feature-table">
				<br>
				<img class=""
					src="<?php echo esc_url( plugins_url( '/../../../admin/img/bpsettings.jpg', __FILE__ ) ); ?>"
					alt="Settings">
			</div>
			<div class="myvideoroom-feature-table">
				<img class=""
					src="<?php echo esc_url( plugins_url( '/../../../admin/img/screenshot-2.PNG', __FILE__ ) ); ?>"
					alt="Video Call in Progress">
			</div>
		</div>
		<!-- Settings Marker -->
		<div class="myvideoroom-menu-settings">
			<div class="myvideoroom-header-table-left">
				<h1><i
						class="myvideoroom-header-dashicons dashicons-admin-settings"></i><?php esc_html_e( 'Settings - Personal Meeting Rooms', 'myvideoroom' ); ?>
				</h1>
			</div>
			<div class="myvideoroom-header-table-right">

			</div>
		</div>
		<!-- Reception Area -->
		<div class="myvideoroom-feature-outer-table">
			<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
				<h2><?php esc_html_e( 'Reception Room', 'myvideoroom' ); ?></h2>
			</div>
			<div class="myvideoroom-feature-table-large">
				<div class="myvideoroom-inline">
					<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already escaped above.
							echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
					?>
					<h4><?php esc_html_e( 'Customizing the Reception', 'my-video-room' ); ?></h4>
					<p><?php esc_html_e( 'You can edit your room look and feel with any page editor of your choice. You can also add multiple reception centers by adding the shortcode: ', 'my-video-room' ); ?>
						<strong> [myvideoroom_meetswitch]</strong>
						<?php esc_html_e( ' to the page. ', 'my-video-room' ); ?>
					</p>
					<p>
						<?php
						esc_html_e(
							"You can change the room name, its URL, and its parent page in the normal pages interface of WordPress. Please note whilst the system updates its internal links if you change the meeting page URL external emails, or other invites may not be updated by your users' guests. Its a good idea to link to reception page from the main area of your site.",
							'my-video-room'
						);
						?>
					</p>
					<h4><?php esc_html_e( 'Who is a Host ?', 'my-video-room' ); ?></h4>
					<p>
						<?php
						esc_html_e(
							'Any signed in user will be the host of their own room, and everyone else will be a guest. Users can change their privacy, as well as room and reception layout templates by accessing their own room, and clicking on the Settings tab, or the Permissions tab if you have the Security Module enabled. ',
							'myvideoroom'
						);
						?>
					</p>
					<?php
					esc_html_e(
						'Site Administrators are also guests, and have no special permissions or access rights to user\'s private rooms. Rooms are encrypted for protection, admins, and even ClubCloud can not see inside meetings. ',
						'myvideoroom'
					);
					?>
					</p>
				</div>
			</div>
		</div>
		<!-- Default Video Section  -->
		<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
			<div class="myvideoroom-feature-outer-table">
				<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
					<h2><?php esc_html_e( 'Default Video Settings', 'myvideoroom' ); ?></h2>
				</div>
				<div class="myvideoroom-feature-table-large">
					<p>
						<?php
							esc_html_e(
								'Default Room Privacy (reception) and Layout settings. These settings will be used by all Personal Video Rooms as their base layout, and reception, until users set their own room preferences',
								'myvideoroom'
							);
						?>
					</p>
					<?php
					if ( Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
						?>
					<p><strong><?php esc_html_e( 'Note', 'myvideoroom' ); ?></strong><?php esc_html_e( '- This setting is shared with BuddyPress personal profile room integration as they are the same room.', 'myvideoroom' ); ?>
					</p>
						<?php
					}
						$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
							SiteDefaults::USER_ID_SITE_DEFAULTS,
							MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
							null,
							true
						);
						//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  - Layout Setting already Escaped in function.
						echo $layout_setting;
	?>
				</div>
			</div>
		</div>
	</div>
	<?php
		return ob_get_clean();
};
