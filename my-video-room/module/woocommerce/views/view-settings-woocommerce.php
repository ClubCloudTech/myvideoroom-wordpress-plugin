<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\Security\Views\view-settings-woocommerce.php
 */

/**
 * Render the admin page for WooCommerce
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
	$index              = 996;
	$target_storefront  = 'myvideoroom-wcstorefront';
	$target_room_stores = 'myvideoroom-wcroomstores';
	$target_basket      = 'myvideoroom-wcbaskets';
	?>
<div class="mvr-nav-shortcode-outer-wrap mvr-nav-shortcode-outer-border">
	<!-- Module Header -->
	<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">

		<div class = "myvideoroom-partner-logo " ><img id = "myvideoroom-partner-logo"
			src="<?php echo esc_url( plugins_url( '../img/woocommerce-logo.png', __FILE__ ) ); ?>"
			alt="MyVideoroom Logo"></div><h1><?php esc_html_e( 'Integration and Video Rooms', 'myvideoroom' ); ?>
			</h1>
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
				echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_ID, null, Factory::get_instance( BuddyPress::class )->is_buddypress_available() );
				?>
			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<p>
				<?php
				esc_html_e(
					'Connects the WooCommerce E-commerce plugin to MyVideoRoom and unlocks the power of video, for better experience and conversions. This pack provides integration to WooCoommerce to bring the power of Video to unlock new customer opportunities and revenue. A site level video store, the ability to share a basket, and products on a call, and giving each room its own room store give customers new opportunities to interact with your WooCommerce store. See each tab for more details',
					'myvideoroom'
				);
				?>
			</p>
		</div>
	</div>
	<!-- Navigation Menu Section -->
	<?php
	if ( Factory::get_instance( BuddyPress::class )->is_buddypress_available() ) {
		?>
	<div class="mvr-nav-shortcode-outer-wrap">
		<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
			<ul class="mvr-ul-style-top-menu">
				<li><a class="nav-tab nav-tab-active" href="#<?php echo esc_attr( $target_storefront ); ?>"
						style><?php esc_html_e( 'Site Storefront', 'myvideoroom' ); ?> </a></li>
				<li><a class="nav-tab" href="#<?php echo esc_attr( $target_room_stores ); ?>" style><?php esc_html_e( 'Room Level Stores', 'myvideoroom' ); ?> </a>
				</li>
				<li><a class="nav-tab" href="#<?php echo esc_attr( $target_basket ); ?>"
						style><?php esc_html_e( 'Shared Baskets and Products', 'myvideoroom' ); ?> </a></li>
			</ul>
		</nav>

		<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
<!-- 
		Site Level Video Store
-->
			<article id="<?php echo esc_attr( $target_storefront ); ?>" class="myvideoroom-content-tab">
				<!-- Module Header -->
				<div class="myvideoroom-menu-settings <?php echo esc_attr( $target_storefront ); ?>">
					<div class="myvideoroom-header-table-left-reduced">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-cart"></i><?php esc_html_e( 'Site Level Video Store', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right-wide">
						<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Settings:', 'myvideoroom' ); ?><i
								data-target="<?php echo esc_attr( $target_storefront ); ?>"
								class="myvideoroom-header-dashicons dashicons-admin-settings mvideoroom-information-menu-toggle-selector"
								title="<?php esc_html_e( 'Go to Settings - Site Level Video Store', 'myvideoroom' ); ?>"></i>
						</h3>
					</div>
				</div>
				<!-- Settings Marker -->
				<div class="myvideoroom-menu-settings <?php echo esc_attr( $target_storefront ); ?>" style="display: none;">
					<div class="myvideoroom-header-table-left-reduced">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-admin-settings "></i><?php esc_html_e( 'Settings - Site Level Video Store', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right-wide">
						<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Main:', 'myvideoroom' ); ?><i
								class="myvideoroom-header-dashicons dashicons-info-outline mvideoroom-information-menu-toggle-selector"
								data-target="<?php echo esc_attr( $target_storefront ); ?>"
								title="<?php esc_html_e( 'Go to- Module State and Information', 'myvideoroom' ); ?>"></i>
						</h3>
					</div>
				</div>
				<!-- Information Toggle -->
				<div id="toggle-info_<?php echo esc_attr( $index++ ); ?>" class="mvideoroom-information-menu-toggle-target-<?php echo esc_attr( $target_storefront ); ?>"	style="">
				<!-- Module State and Description Marker -->
				<div class="myvideoroom-feature-outer-table">
					<div id="module-state<?php echo esc_attr( $index++ ); ?>"
						class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Feature', 'myvideoroom' ); ?></h2>
						<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
							echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_USER_ID, null, Factory::get_instance( BuddyPress::class )->is_user_module_available() );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'User Profile and Wall Enabled Rooms', 'myvideoroom' ); ?>
						</h2>
						<p>
							<?php
							esc_html_e(
								'This module adds a personal meeting room of the user straight into their WooCommerce profile. This Video Room is the same room as in the Personal Meeting Module, and is completely controlled by the WordPress user.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'Guest entrances, invitations and reception settings work the same as the users classic personal room. Guests can enter from WooCommerce profile wall, or the user reception page, email links etc. The user also gains options in security (if enabled) about allowing friends and connections into the room, blocking users, or even hiding the room from non-friends.',
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
										<?php Factory::get_instance( BuddyPressConfig::class )->render_dependencies( 'user' ); ?>
									</div>
						</div>
				</div>
				<!-- Screenshot Marker -->
				<div class="myvideoroom-feature-outer-table">
					<div class="myvideoroom-feature-table">
						<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
						<img class=""
							src="<?php echo esc_url( plugins_url( '/../../../admin/img/videoroombp.jpg', __FILE__ ) ); ?>"
							alt="WooCommerce Room">
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
						<!-- end Toggle Section -->
				</div>
				</div>
		<!--Begin Settings Toggle Section -->
			<div id="toggle-info_<?php echo esc_attr( $index++ ); ?>" class="mvideoroom-settings-menu-toggle-target-<?php echo esc_attr( $target_storefront ); ?>" style="display:none;">
						<!-- User Room Tab Naming Marker -->
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state_<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Name of Tab:', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
								<div class="myvideoroom-inline">
									<img class=""
										src="<?php echo esc_url( plugins_url( '/../../../admin/img/tabdisplay.jpg', __FILE__ ) ); ?>"
										alt="Settings">
									<br>
									<?php
									esc_html_e(
										'What should we call your Room Tab in WooCommerce ?',
										'myvideoroom'
									);
									?>

								</div>

								<div class="myvideoroom-inline">
									<input id="user-profile-input" type="text" min="5" max="20" name="user-tab"
										value="<?php echo esc_textarea( get_option( 'myvideoroom-buddypress-user-tab' ) ); ?>"
										placeholder=""
										class="myvideoroom-inline myvideoroom-input-restrict-alphanumeric-space" />
								</div>
								<input id="save-user-tab" type="button" value="Save"
									class="myvideoroom-welcome-buttons mvr-main-button-notice" style=" display:none;" />
							</div>
						</div>
						<!-- Default Video Section  -->
						<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
							class="mvr-nav-settingstabs-outer-wrap">
							<div class="myvideoroom-feature-outer-table">
								<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
									class="myvideoroom-feature-table-small">
									<h2><?php esc_html_e( 'Default Video Settings', 'myvideoroom' ); ?></h2>
								</div>
								<div class="myvideoroom-feature-table-large">
									<h2><?php esc_html_e( 'Personal and WooCommerce Profile Room Default Settings', 'myvideoroom' ); ?>
									</h2>
									<p> <?php esc_html_e( ' Default Room Privacy (reception) and Layout settings. These settings will be used by all Rooms, until users set their own room preference', 'myvideoroom' ); ?>
									</p>
									<?php
									$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
										SiteDefaults::USER_ID_SITE_DEFAULTS,
										MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING
									);
									// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
									echo $layout_setting;
									?>
								</div>
							</div>
						</div>
			</article>
<!-- 
		Room Level Stores
-->

			<article id="<?php echo esc_attr( $target_room_stores ); ?>" class="myvideoroom-content-tab">
			<!-- Module Header -->
			<div class="myvideoroom-menu-settings <?php echo esc_attr( $target_room_stores ); ?>">
					<div class="myvideoroom-header-table-left-reduced">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-cart"></i><?php esc_html_e( 'Room Level Stores', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right-wide">
						<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Settings:', 'myvideoroom' ); ?><i
								data-target="<?php echo esc_attr( $target_room_stores ); ?>"
								class="myvideoroom-header-dashicons dashicons-admin-settings mvideoroom-information-menu-toggle-selector"
								title="<?php esc_html_e( 'Go to Settings - Room Level Stores', 'myvideoroom' ); ?>"></i>
						</h3>
					</div>
				</div>
				<!-- Settings Marker -->
				<div class="myvideoroom-menu-settings <?php echo esc_attr( $target_room_stores ); ?>" style="display: none;">
					<div class="myvideoroom-header-table-left-reduced">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-admin-settings "></i><?php esc_html_e( 'Settings - Room Level Stores', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right-wide">
						<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Main:', 'myvideoroom' ); ?><i
								class="myvideoroom-header-dashicons dashicons-info-outline mvideoroom-information-menu-toggle-selector"
								data-target="<?php echo esc_attr( $target_room_stores ); ?>"
								title="<?php esc_html_e( 'Go to- Module State and Information', 'myvideoroom' ); ?>"></i>
						</h3>
					</div>
				</div>

				<!-- Information Toggle -->
								<div id="toggle-info_<?php echo esc_attr( $index++ ); ?>" class="mvideoroom-information-menu-toggle-target-<?php echo esc_attr( $target_room_stores ); ?>"	style="">
				<!-- Module State and Description Marker -->


				<!-- Module State and Description Marker -->
				<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Feature is:', 'myvideoroom' ); ?></h2>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
								echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_GROUP_ID, null, Factory::get_instance( BuddyPress::class )->is_group_module_available() );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'WooCommerce Group Room Support', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'This module will add a Video Room to each WooCommerce group. It will allow a room admin or moderator of a WooCommerce group to be a Host of a group room. Regular members will be guests, room hosts can decide to admit just members, admins, moderators, or even signed out users and non-members. ',
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
				<!-- Dependencies and Requirements Marker -->
				<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
					<div class="myvideoroom-feature-outer-table">
						<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
							class="myvideoroom-feature-table-small">
							<h2><?php esc_html_e( 'Requirements:', 'myvideoroom' ); ?></h2>
						</div>
						<div class="myvideoroom-feature-table-large">
							<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
								<p><?php esc_html_e( 'No Modules Depend on this Component.', 'myvideoroom' ); ?>
								</p>
								<?php
									Factory::get_instance( BuddyPressConfig::class )->render_dependencies( 'group' );
								?>
							</div>
						</div>
					</div>
					<!-- Screenshot Marker -->
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
								alt="Video Call in Progress">
						</div>
					</div>
				<!-- end Toggle Section -->
				</div>
			</div>
				<!--Begin Settings Toggle Section -->
				<div id="toggle-info_<?php echo esc_attr( $index++ ); ?>" class="mvideoroom-settings-menu-toggle-target-<?php echo esc_attr( $target_room_stores ); ?>" style="display:none;">
						<!-- User Room Tab Naming Marker -->
					<!-- User Room Tab Naming Marker -->
					<div class="myvideoroom-feature-outer-table">
						<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
							class="myvideoroom-feature-table-small">
							<h2><?php esc_html_e( 'Name of Tab:', 'myvideoroom' ); ?></h2>
						</div>
						<div class="myvideoroom-feature-table-large">
							<div class="myvideoroom-inline">
								<img class=""
									src="<?php echo esc_url( plugins_url( '/../../../admin/img/tabdisplay.jpg', __FILE__ ) ); ?>"
									alt="Settings">
								<br>
								<?php
								esc_html_e(
									'What should we call your Group Room Tabs in WooCommerce ?',
									'myvideoroom'
								);
								?>
							</div>
							<div class="myvideoroom-inline">
								<input id="group-profile-input" type="text" min="5" max="20" name="user-tab"
									value="<?php echo esc_textarea( get_option( 'myvideoroom-buddypress-group-tab' ) ); ?>"
									placeholder=""
									class="myvideoroom-inline myvideoroom-input-restrict-alphanumeric-space" />
							</div>
							<input id="save-group-tab" type="button" value="Save"
								class="myvideoroom-welcome-buttons mvr-main-button-notice" style=" display:none;" />
						</div>
					</div>
					<!-- Default Video Section  -->
					<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
						class="mvr-nav-settingstabs-outer-wrap">
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Default Video Settings', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
								<h2><?php esc_html_e( 'Groups Default Video Settings', 'myvideoroom' ); ?></h2>
								</h2>
								<p><?php esc_html_e( 'These are the Default Group Room Privacy (reception) and Room Layout settings. These settings will be used by Groups, if the owner has not yet set up a room preference', 'myvideoroom' ); ?>
								</p>
								<?php
									$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
										SiteDefaults::USER_ID_SITE_DEFAULTS,
										BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS_SITE_DEFAULT
									);
									// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
									echo $layout_setting;
								?>
							</div>
						</div>
					</div>
			</article>

<!-- 
		Shared Baskets and Products
-->

			<article id="<?php echo esc_attr( $target_basket ); ?>" class="myvideoroom-content-tab">
							<!-- Module Header -->
			<div class="myvideoroom-menu-settings <?php echo esc_attr( $target_basket ); ?>">
					<div class="myvideoroom-header-table-left-reduced">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-cart"></i><?php esc_html_e( 'Shared Baskets and Products', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right-wide">
						<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Settings:', 'myvideoroom' ); ?><i
								data-target="<?php echo esc_attr( $target_basket ); ?>"
								class="myvideoroom-header-dashicons dashicons-admin-settings mvideoroom-information-menu-toggle-selector"
								title="<?php esc_html_e( 'Go to Settings - Shared Baskets and Products', 'myvideoroom' ); ?>"></i>
						</h3>
					</div>
				</div>
				<!-- Settings Marker -->
				<div class="myvideoroom-menu-settings <?php echo esc_attr( $target_basket ); ?>" style="display: none;">
					<div class="myvideoroom-header-table-left-reduced">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-admin-settings "></i><?php esc_html_e( 'Settings - Shared Baskets and Products', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right-wide">
						<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Main:', 'myvideoroom' ); ?><i
								class="myvideoroom-header-dashicons dashicons-info-outline mvideoroom-information-menu-toggle-selector"
								data-target="<?php echo esc_attr( $target_basket ); ?>"
								title="<?php esc_html_e( 'Go to- Module State and Information', 'myvideoroom' ); ?>"></i>
						</h3>
					</div>
				</div>

				<!-- Information Toggle -->
								<div id="toggle-info_<?php echo esc_attr( $index++ ); ?>" class="mvideoroom-information-menu-toggle-target-<?php echo esc_attr( $target_basket ); ?>"	style="">
				<!-- Module State and Description Marker -->


				<!-- Module State and Description Marker -->
				<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Feature is:', 'myvideoroom' ); ?></h2>
						<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal function already escaped
								echo Factory::get_instance( ModuleConfig::class )->module_activation_button( BuddyPress::MODULE_BUDDYPRESS_GROUP_ID, null, Factory::get_instance( BuddyPress::class )->is_group_module_available() );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'WooCommerce Group Room Support', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'This module will add a Video Room to each WooCommerce group. It will allow a room admin or moderator of a WooCommerce group to be a Host of a group room. Regular members will be guests, room hosts can decide to admit just members, admins, moderators, or even signed out users and non-members. ',
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
				<!-- Dependencies and Requirements Marker -->
				<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
					<div class="myvideoroom-feature-outer-table">
						<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
							class="myvideoroom-feature-table-small">
							<h2><?php esc_html_e( 'Requirements:', 'myvideoroom' ); ?></h2>
						</div>
						<div class="myvideoroom-feature-table-large">
							<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
								<p><?php esc_html_e( 'No Modules Depend on this Component.', 'myvideoroom' ); ?>
								</p>
								<?php
									Factory::get_instance( BuddyPressConfig::class )->render_dependencies( 'group' );
								?>
							</div>
						</div>
					</div>
					<!-- Screenshot Marker -->
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
								alt="Video Call in Progress">
						</div>
					</div>
				<!-- end Toggle Section -->
				</div>
			</div>
				<!--Begin Settings Toggle Section -->
				<div id="toggle-info_<?php echo esc_attr( $index++ ); ?>" class="mvideoroom-settings-menu-toggle-target-<?php echo esc_attr( $target_basket ); ?>" style="display:none;">
						<!-- User Room Tab Naming Marker -->
					<!-- User Room Tab Naming Marker -->
					<div class="myvideoroom-feature-outer-table">
						<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
							class="myvideoroom-feature-table-small">
							<h2><?php esc_html_e( 'Name of Tab:', 'myvideoroom' ); ?></h2>
						</div>
						<div class="myvideoroom-feature-table-large">
							<div class="myvideoroom-inline">
								<img class=""
									src="<?php echo esc_url( plugins_url( '/../../../admin/img/tabdisplay.jpg', __FILE__ ) ); ?>"
									alt="Settings">
								<br>
								<?php
								esc_html_e(
									'What should we call your Group Room Tabs in WooCommerce ?',
									'myvideoroom'
								);
								?>
							</div>
							<div class="myvideoroom-inline">
								<input id="group-profile-input" type="text" min="5" max="20" name="user-tab"
									value="<?php echo esc_textarea( get_option( 'myvideoroom-buddypress-group-tab' ) ); ?>"
									placeholder=""
									class="myvideoroom-inline myvideoroom-input-restrict-alphanumeric-space" />
							</div>
							<input id="save-group-tab" type="button" value="Save"
								class="myvideoroom-welcome-buttons mvr-main-button-notice" style=" display:none;" />
						</div>
					</div>
					<!-- Default Video Section  -->
					<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
						class="mvr-nav-settingstabs-outer-wrap">
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Default Video Settings', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
								<h2><?php esc_html_e( 'Groups Default Video Settings', 'myvideoroom' ); ?></h2>
								</h2>
								<p><?php esc_html_e( 'These are the Default Group Room Privacy (reception) and Room Layout settings. These settings will be used by Groups, if the owner has not yet set up a room preference', 'myvideoroom' ); ?>
								</p>
								<?php
									$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
										SiteDefaults::USER_ID_SITE_DEFAULTS,
										BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS_SITE_DEFAULT
									);
									// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
									echo $layout_setting;
								?>
							</div>
						</div>
					</div>
			</article>
		</div>
	</div>
</div>

		<?php
	} else {
		echo '<h2>WooCommerce is not Installed - Settings Disabled</h2>';
	}
		return ob_get_clean();
};
