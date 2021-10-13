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
use MyVideoRoomPlugin\Library\ShortcodeDocuments;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;

return function (): string {
	ob_start(); ?>
<div class="wrap">

		<div class="mvr-outer-box-wrap">
		<h1><?php esc_html_e( 'BuddyPress Integration and Video Rooms', 'my-video-room' ); ?></h1>

		<p> 
		<?php
			esc_html_e(
				'MyVideoRoom includes integration to BuddyPress which adds Video Rooms to the User Profile Page and to each Group.
				For Users, they get their Personal Video room rendered in the BuddyPress Profile Loop as a Separate Video Meeting Tab with control
				of their own Video Room settings and permissions- including whether to show the room to non-friends. Guests viewing a User Profile
				in BuddyPress can enter a Video Room straight from the User’s profile page. For Groups- Owners, and Moderators can Enable or Disable
				Group Video Rooms, as well as control their Layouts, Templates, Room Permissions and Reception Settings, including members only groups.',
				'my-video-room'
			);
		?>
		<br></p>

		<?php
				// Activation/module.
		if ( ! Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_ID ) ) {
			return '';
		}
		?>
		</div>
			<?php
			if ( Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
				?>
	<div class="mvr-outer-box-wrap">
		<div class="mvr-nav-shortcode-outer-wrap">
				<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
					<ul class="menu" >
							<a class="nav-tab nav-tab-active" href="#page1" style><?php esc_html_e( 'User Profile Rooms', 'my-video-room' ); ?> </a>
							<a class="nav-tab" href="#page2" style><?php esc_html_e( 'Group Rooms', 'my-video-room' ); ?> </a>
							<a class="nav-tab" href="#page3" style><?php esc_html_e( 'BuddyPress Friends Video', 'my-video-room' ); ?> </a>
							<a class="nav-tab" href="#page4" style><?php esc_html_e( 'BuddyPress Shortcodes', 'my-video-room' ); ?></a>

					</ul>
				</nav>
			<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
				<article id="page1" style>
				<div class="mvr-outer-box-wrap">
					<h2><?php esc_html_e( 'BuddyPress Profile Room Support', 'my-video-room' ); ?></h2>
					<p> 
					<?php
					esc_html_e(
						"This module adds a personal meeting room of the user straight into the BuddyPress profile of the user. This Video Room is the same room as in the Personal Meeting Tab, and entrances, settings, invitations and reception settings work the same across both
						rooms. The room functions as a BuddyPress specific entrance from the BuddyPress environment into the User's personal Video Space.",
						'my-video-room'
					);
					?>
					</p>
				</div>
				<div class="mvr-outer-box-wrap">						
						<h2><?php esc_html_e( 'Personal Room (Profile and User Video) Default Video Settings', 'my-video-room' ); ?></h2>
						<p> <?php esc_html_e( 'These are the Default Room Privacy (reception) and Room Layout settings. These settings will be used by the Room, if the user has not yet set up a room preference', 'my-video-room' ); ?></p>
				</div>
					<?php
						$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
							SiteDefaults::USER_ID_SITE_DEFAULTS,
							MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING_SITE_DEFAULT,
							array( 'basic', 'premium' )
						);
						// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
						echo $layout_setting;
					?>
				</article>
				<article id="page2" style>
				<div class="mvr-outer-box-wrap">	
					<h2><?php esc_html_e( 'BuddyPress Group Room Support', 'my-video-room' ); ?></h2>
						<p>
						<?php
						esc_html_e(
							'This module will add a Video Room to each BuddyPress group. It will allow a room admin or moderator of a BuddyPress group to be a Host of a group room. Regular members will be guests, signed out users are not allowed in group rooms. The moderators/admins can change Room privacy, as well as room and reception layout templates by accessing on the Video Tab of the Group and clicking on the Host tab. This will take effect at the next page refresh.',
							'my-video-room'
						);
						?>
						</p>
					</div>
					<div class="mvr-outer-box-wrap">
							<h2><?php esc_html_e( 'Groups Default Video Settings', 'my-video-room' ); ?></h2>
							<p><?php esc_html_e( 'These are the Default Room Privacy (reception) and Room Layout settings. These settings will be used by Groups, if the owner has not yet set up a room preference', 'my-video-room' ); ?> </p>
					<?php
							$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
								SiteDefaults::USER_ID_SITE_DEFAULTS,
								BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS_SITE_DEFAULT
							);
					// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
					echo $layout_setting;
					?>

				</article>

				<article id="page3" style>

					<h2><?php esc_html_e( 'BuddyPress Friends Video Control', 'my-video-room' ); ?></h2>

						<p> 
						<?php
						esc_html_e(
							'Controls BuddyPress Friends behaviour and whether you want to enable access control restrictions for BuddyPress Friends. Users have the option to 
							restrict access to their video rooms to friends only. This section turns on or off support for the feature in the Room Security Engine and is configured in the Room Security Tab.',
							'my-video-room'
						);
						?>
						</p>

						<br>
						</article>
						<article id="page4" style>
						<?php
						if ( ! factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
							return 'BuddyPress not active';
						}
						// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped- function already escaped.
						echo Factory::get_instance( ShortcodeDocuments::class )->render_buddypress_shortcode_docs();
						?>
						</article>
						<br>
				</div>
			</div>
		</div>
	</div>
					<?php
			} else {
				echo '<h2>BuddyPress is not Installed - Settings Disabled</h2>';
			}
			return ob_get_clean();
};
