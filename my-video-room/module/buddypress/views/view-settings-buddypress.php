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
use MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressConfig;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;

return function (): string {
	ob_start();
	$index = 1;
	?>
<div class="wrap">
	<h1><?php esc_html_e( 'BuddyPress Integration and Video Rooms', 'myvideoroom' ); ?></h1>
	<div class="myvideoroom-feature-outer-table">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Module State', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
				<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
							echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_ID );
				?>
			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<p>
				<?php
				esc_html_e(
					'Integration features between MyVideoRoom and the popular BuddyPress Social Networking plugin. This pack provides integration to BuddyPress Groups, BuddyPress User Profile Pages, and can set room permissions and security, and hosts based on BuddyPress friends, and group moderators/administrators. See each tab for more details',
					'myvideoroom'
				);
				?>
			</p>
		</div>
	</div>

	<?php
	if ( Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
		?>
	<div class="mvr-nav-shortcode-outer-wrap">
		<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
			<ul class="mvr-ul-style-top-menu">
				<li><a class="nav-tab nav-tab-active" href="#page11"
						style><?php esc_html_e( 'User Profile Rooms', 'myvideoroom' ); ?> </a></li>
				<li><a class="nav-tab" href="#page12" style><?php esc_html_e( 'Group Rooms', 'myvideoroom' ); ?> </a>
				</li>
				<li><a class="nav-tab" href="#page13"
						style><?php esc_html_e( 'Friends Permissions Support', 'myvideoroom' ); ?> </a></li>
			</ul>
		</nav>
		<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
			<article id="page11" class="myvideoroom-content-tab">
				<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Feature is:', 'myvideoroom' ); ?></h2>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
							echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_USER_ID );
							Factory::get_instance( BuddyPressConfig::class )->render_dependencies( 'user' );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'BuddyPress Profile Room Support', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'This module adds a personal meeting room of the user straight into their BuddyPress profile. This Video Room is the same room as in the Personal Meeting Module, and is completely controlled by the WordPress user.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'Guest entrances, invitations and reception settings work the same as the users classic personal room. Guests can enter from BuddyPress profile wall, or the user reception page, email links etc. The user also gains options in security (if enabled) about allowing friends and connections into the room, blocking users, or even hiding the room from non-friends.',
								'myvideoroom'
							);
							?>
						</p>
					</div>
				</div>


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


				<h2><?php esc_html_e( 'Personal and BuddyPress Profile Room Default Settings', 'myvideoroom' ); ?>
				</h2>
				<p> <?php esc_html_e( ' Default Room Privacy (reception) and Layout settings. These settings will be used by all Rooms, until users set their own room preference', 'myvideoroom' ); ?>
				</p>
				<?php
						$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
							SiteDefaults::USER_ID_SITE_DEFAULTS,
							MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING_SITE_DEFAULT
						);
						// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
						echo $layout_setting;
				?>
			</article>
			<article id="page12" class="myvideoroom-content-tab">
			<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Feature is:', 'myvideoroom' ); ?></h2>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
								echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_GROUP_ID );
								Factory::get_instance( BuddyPressConfig::class )->render_dependencies( 'group' );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'BuddyPress Group Room Support', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'This module will add a Video Room to each BuddyPress group. It will allow a room admin or moderator of a BuddyPress group to be a Host of a group room. Regular members will be guests, room hosts can decide to admit just members, admins, moderators, or even signed out users and non-members. ',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'The moderators/admins can change Room privacy and security, as well as room and reception layout templates by accessing on the Video Tab of the Group and clicking on the Host tab.',
								'myvideoroom'
							);
							?>
						</p>
					</div>
				</div>

				<div class="myvideoroom-feature-outer-table">
					<div class="myvideoroom-feature-table">
						<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
						<img class=""
						src="<?php echo esc_url( plugins_url( '/../../../admin/img/groupscreenshot.JPG', __FILE__ ) ); ?>"	
						alt="Video Call in Progress">
					</div>
					<div class="myvideoroom-feature-table">
						<br>
						<img class=""
						src="<?php echo esc_url( plugins_url( '/../../../admin/img/bpdenied.jpg', __FILE__ ) ); ?>"													
						alt="Settings">
					</div>
					<div class="myvideoroom-feature-table">
						<br><br>
						<img class=""
							src="<?php echo esc_url( plugins_url( '/../../../admin/img/groupsecurity.jpg', __FILE__ ) ); ?>"
							alt="Video Call in Progress" >
					</div>
				</div>

				<div class="mvr-outer-box-wrap">
					<h2><?php esc_html_e( 'Groups Default Video Settings', 'myvideoroom' ); ?></h2>
					<p><?php esc_html_e( 'These are the Default Room Privacy (reception) and Room Layout settings. These settings will be used by Groups, if the owner has not yet set up a room preference', 'myvideoroom' ); ?>
					</p>
					<?php
							$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
								SiteDefaults::USER_ID_SITE_DEFAULTS,
								BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS_SITE_DEFAULT
							);
					// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
					echo $layout_setting;
					?>
			</article>
			<article id="page13" class="myvideoroom-content-tab">
			<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Feature is:', 'myvideoroom' ); ?></h2>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
								echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_FRIENDS_ID );
								Factory::get_instance( BuddyPressConfig::class )->render_dependencies( 'friends' );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'BuddyPress Friends Permissions Control', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'Controls BuddyPress Friends behaviour and whether you want to enable access control restrictions for BuddyPress Friends. Users have the option to restrict access to their video rooms to friends only. This setting works for BuddyPress personal rooms, and User Rooms only (as other room types don\'t have friends)',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'This feature works through the the Room Security Engine and has no effect if this module is not turned on. Users control their preferred setting in the Room Security Tab of their room.',
								'myvideoroom'
							);
							?>
						</p>
					</div>
				</div>	
				<div class="myvideoroom-feature-outer-table">
					<div class="myvideoroom-feature-table">
						<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
						<img class=""
						src="<?php echo esc_url( plugins_url( '/../../../admin/img/bpfriendscontrol.jpg', __FILE__ ) ); ?>"	
							alt="BuddyPress Friends Control" title="<?php esc_html_e( 'Friends Mode Room Security is set in the Security Tab as Normal', 'myvideoroom' ); ?>">
					</div>
					<div class="myvideoroom-feature-table">
						<br>
						<img class=""
						src="<?php echo esc_url( plugins_url( '/../../../admin/img/bpfriendsblock.jpg', __FILE__ ) ); ?>"							
						alt="Settings" title="<?php esc_html_e( 'Do Not Disturb Mode shows the room, but blocks entrance to non friends', 'myvideoroom' ); ?>">
					</div>
					<div class="myvideoroom-feature-table">
					<br>	
					<img class=""
							src="<?php echo esc_url( plugins_url( '/../../../admin/img/bpfriendsstealthmode.jpg', __FILE__ ) ); ?>"
							alt="Stealth Mode" title="<?php esc_html_e( ' Stealth Mode hides the Video Room from Non Friends', 'myvideoroom' ); ?>">
					</div>
				</div>
			</article>
		</div>
	</div>
</div>

		<?php
	} else {
		echo '<h2>BuddyPress is not Installed - Settings Disabled</h2>';
	}
		return ob_get_clean();
};
