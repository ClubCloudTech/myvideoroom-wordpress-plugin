<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package my-video-room/module/woocommerce/views/view-settings-woocommerce.php
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
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\WooCommerce\Library\NotificationHelpers;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
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
		Site Level Video Storefront
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
							echo Factory::get_instance( ModuleConfig::class )->module_activation_button( WooCommerce::MODULE_WOOCOMMERCE_STORE_ID, null, Factory::get_instance( WooCommerce::class )->is_woocommerce_active() );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'Site Video Storefront', 'myvideoroom' ); ?>
						</h2>
						<p>
							<?php
							esc_html_e(
								'This module adds a dedicated video store to your WooCommerce site that can provide a central arrival point for your customers where you host and interact with them at any time. You can use the monitor module, or the reception module to keep an eye on any atendees waiting to enter a room, and admit them, or allow them straight into the store front. You can allow multiple staff members by WordPress group to host the room and separate kiosks to keep customer conversations separate in your main store.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'This is a dedicated automatically created room. However this module also provides support to any other room type (Personal Video Room, BuddyPress Group, Conference Rooms, etc) to also host products, you can thus use multiple store fronts with different settings.',
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
											'This module depends on WooCommerce. If you disable it, the room will redirect to your main site page.',
											'myvideoroom'
										);
								?>
							</p>
							<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
										<?php Factory::get_instance( NotificationHelpers::class )->render_dependencies(); ?>
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
			<!-- Reception Area -->
		<div class="myvideoroom-feature-outer-table">
			<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
				<h2><?php esc_html_e( 'Reception Room', 'myvideoroom' ); ?></h2>
			</div>
			<div class="myvideoroom-feature-table-large">
				<div class="myvideoroom-inline">
					<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already escaped above.
							echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( WooCommerce::MODULE_WOOCOMMERCE_ROOM );
					?>
					<p>
						<?php
						esc_html_e(
							"You can change the room name, its URL, and its parent page in the normal pages interface of WordPress. Please note whilst the system updates its internal links if you change the meeting page URL external emails, or other invites may not be updated by your users' guests. Its a good idea to link to reception page from the main area of your site.",
							'myvideoroom'
						);
						?>
					</p>
					<h4><?php esc_html_e( 'Who is a Host ?', 'myvideoroom' ); ?></h4>
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
								echo Factory::get_instance( ModuleConfig::class )->module_activation_button( WooCommerce::MODULE_WOOCOMMERCE_ROOM_ID, null, Factory::get_instance( WooCommerce::class )->is_woocommerce_active() );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'WooCommerce Room Level Stores', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'This module will add a Store to each Video Room, and track the store as a WooCommerce Category. It will allow any host of the room to add products to the room store, and visitors to the room can revisit the room and see its products even after the call. ',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'This feature works great for multi-level marketing, and events driven sales, and special rooms can be created with bespoke and custom products to support a given event. If you have shared basket enabled the Room Store will also remember the last basket shared in the room. Making it super easy for guests to return after an event to finish a purchase. ',
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
									Factory::get_instance( NotificationHelpers::class )->render_dependencies();
								?>
							</div>
						</div>
					</div>
					<!-- Screenshot Marker -->
					<div class="myvideoroom-feature-outer-table">
						<div class="myvideoroom-feature-table">
							<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
							<img class=""
								src="<?php echo esc_url( plugins_url( '/../img/previousbasket.JPG', __FILE__ ) ); ?>"
								alt="Settings">

						</div>
						<div class="myvideoroom-feature-table">
							<br><br>
							<img class=""
								src="<?php echo esc_url( plugins_url( '/../img/managestore.JPG', __FILE__ ) ); ?>"
								alt="Video Call in Progress">
						</div>
						<div class="myvideoroom-feature-table">
							<br><br>
							<img class=""
								src="<?php echo esc_url( plugins_url( '/../img/roomstore.JPG', __FILE__ ) ); ?>"
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
								echo Factory::get_instance( ModuleConfig::class )->module_activation_button( WooCommerce::MODULE_WOOCOMMERCE_BASKET_ID, null, Factory::get_instance( WooCommerce::class )->is_woocommerce_active() );
							?>
						</div>
					</div>
					<div class="myvideoroom-feature-table-large">
						<h2><?php esc_html_e( 'Shared Shopping Baskets, and in call Product Sharing', 'myvideoroom' ); ?></h2>
						<p>
							<?php
							esc_html_e(
								'This module adds support for viewing your shopping basket inside your video call, and for room hosts to be able to auto-share their baskets with the room. It also allows any participant to share a single product with the room from their own shopping basket. Participants see their own shared queue and can accept or reject shared products ',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'This module takes 1:1 and 1:many shopping to a new level. With it you can take your client through a guided shopping experience and add products into their basket for them to finish the purchase on their side. It works with any standard or variable product/subscription added to a WooCommerce basket. ',
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
									Factory::get_instance( NotificationHelpers::class )->render_dependencies();
								?>
							</div>
						</div>
					</div>
					<!-- Screenshot Marker -->
					<div class="myvideoroom-feature-outer-table">
						<div class="myvideoroom-feature-table">
							<h2><?php esc_html_e( 'Screenshots', 'myvideoroom' ); ?></h2>
							<img class=""
								src="<?php echo esc_url( plugins_url( '/../img/basketauto.JPG', __FILE__ ) ); ?>"
								alt="Video Call in Progress">
						</div>
						<div class="myvideoroom-feature-table">
							<br>
							<img class=""
								src="<?php echo esc_url( plugins_url( '/../img/previousbasket.JPG', __FILE__ ) ); ?>"
								alt="Settings">
						</div>
						<div class="myvideoroom-feature-table">
							<br><br>
							<img class=""
								src="<?php echo esc_url( plugins_url( '/../img/sharedbasket.jpg', __FILE__ ) ); ?>"
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
