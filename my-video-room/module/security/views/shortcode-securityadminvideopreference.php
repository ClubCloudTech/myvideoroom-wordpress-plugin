<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Dao\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Library\Dependencies;

return function (
	SecurityVideoPreference $current_user_setting = null,
	string $room_name,
	int $id_index = 0,
	int $user_id = null
	): string {
	wp_enqueue_style( 'mvr-template' );
	wp_enqueue_style( 'mvr-menutab-header' );
	ob_start();
	?>
<div id="security-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">

			<?php
			// room permissions info.
			$site_override              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
			$room_disabled              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'room_disabled' );
			$anonymous_enabled          = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
			$allow_role_control_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
			$block_role_control         = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'block_role_control_enabled' );
			$output                     = null;

			if ( ! $site_override ) {
				if ( ! $room_disabled ) {
					$output .= '<p class="mvr-main-button-enabled" >' . esc_html__( 'Site Enabled', 'my-video-room' ) . '</p>';
				} else {
					$output .= '<p class="mvr-main-button-disabled button" >' . esc_html__( 'Site Disabled', 'my-video-room' ) . '</p>';
				}
				if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
					$restrict_group_to_members_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'restrict_group_to_members_enabled' );
					$restrict_to_friends               = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
					if ( $restrict_group_to_members_enabled ) {
						$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Restricted to Members', 'my-video-room' ) . '</p>';
					}
					if ( $restrict_to_friends ) {
						$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Restricted to Friends', 'my-video-room' ) . '</p>';
					}
				}
				if ( $allow_role_control_enabled ) {
					$db_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allowed_roles' );
					if ( ! $db_setting ) {
						$db_setting = 'No One';
					}
					if ( $block_role_control ) {
						$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Member Restrictions are Excluding : ', 'my-video-room' ) . $db_setting . '</p>';
					} else {
						$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Member Restrictions Only Allowing : ', 'my-video-room' ) . '' . $db_setting . '</p>';
					}
				}

				if ( $anonymous_enabled ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Anonymous Disabled', 'my-video-room' ) . '</p>';
				}
			} else {
				$output .= Factory::get_instance( SecurityButtons::class )->site_wide_enabled( 'nourl' );
				$output .= '<p class="mvr-preferences-paragraph">' . esc_html__( 'You are overriding User and Room settings with ones applied centrally below.', 'my-video-room' ) . '</p>';
			}
			// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - Not Needed already escaped.
			echo '<div class="mvr-button-table"> ' . $output . ' </div>';
			?>
				<form method="post" action="">
				<input name="myvideoroom_security_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
				<input name="myvideoroom_security_user_id" type="hidden" value="
								<?php
								if ( function_exists( 'bp_is_groups_component' ) && \bp_is_groups_component() ) {
									global $bp;
									$group_id = $bp->groups->current_group->id;
									echo esc_textarea( $group_id );
								}
								?>
								" />
					<br>
					<h2 class="mvr-title-header"><?php esc_html_e( 'Override User preferences', 'my-video-room' ); ?></h1>
					<input
					type="checkbox"
					class="myvideoroom_override_all_preferences"
					name="myvideoroom_override_all_preferences"
					id="myvideoroom_override_all_preferences_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->check_site_override_setting() ? 'checked' : ''; ?> />

					<p><?php esc_html_e( 'Use this setting to ignore user and group individual room settings and enforce security settings across all of', 'my-video-room' ); ?>
					<?php
					// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - get-bloginfo is a core function already escaped.
					echo get_bloginfo( 'name' ) . '.' . esc_html__( 'If you do not enable this setting, the below options have no effect.', 'my-video-room' );
					?>
					</p>
					<hr>
					<h1 class="mvr-title-header"><?php esc_html_e( 'Settings', 'my-video-room' ); ?></h1><br><br>
					<h2 class="mvr-title-header"><i class="dashicons mvr-icons dashicons-dismiss"></i><?php esc_html_e( 'Disable All Rooms in Site', 'my-video-room' ); ?></h2>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_security_room_disabled_preference"
					name="myvideoroom_security_room_disabled_preference"
					id="myvideoroom_security_room_disabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_room_disabled() ? 'checked' : ''; ?> />
				<p>
					<?php esc_html_e( 'Enable this setting to switch off all rooms. All Users will be Blocked from Access and will be notified Video is Offline if they try to join rooms.', 'my-video-room' ); ?> 
				</p>
				<label for="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<h2 class="mvr-title-header"><i class="dashicons mvr-icons dashicons-admin-users"></i><?php esc_html_e( 'Restrict Anonymous Access (Force Users to Register)', 'my-video-room' ); ?></h2>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_security_anonymous_enabled_preference"
					name="myvideoroom_security_anonymous_enabled_preference"
					id="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_anonymous_enabled() ? 'checked' : ''; ?>/><br>
				<p>
					<?php
					esc_html_e(
						'If you enable this setting, anonymous users from the Internet WILL NOT be able to enter your room. The only way
						someone can enter your room is if they have an account on your website. This means that external users, will have 
						to go through whatever registration process exists for your website. Default is disabled, which means anonymous access is allowed.',
						'my-video-room'
					);
					?>
				</p>
					<label for="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<h2 class="mvr-title-header"><i class="dashicons mvr-icons dashicons-id"></i><?php esc_html_e( 'Enable Role Control - For Allowed Roles', 'my-video-room' ); ?></h2>
					</label>
					<input
							type="checkbox"
							class="myvideoroom_security_allow_role_control_enabled_preference"
							name="myvideoroom_security_allow_role_control_enabled_preference"
							id="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_allow_role_control_enabled() ? 'checked' : ''; ?>	/>
					<br>
					<p>
						<?php
						esc_html_e(
							"If you enable this setting only the following roles will be allowed to access your rooms. If you want to reverse the setting, then click 
							'block these roles instead' which will allow all roles - except for the ones you select. ",
							'my-video-room'
						);
						?>
					</p>
				<label for="myvideoroom_security_allowed_roles_preference_<?php echo esc_attr( $id_index ); ?>">
					<?php esc_html_e( 'Allowed Roles setting: ', 'my-video-room' ); ?>
				</label>
				<select multiple="multiple"
						class="myvideoroom_security_allowed_roles_preference"
						name="myvideoroom_security_allowed_roles_preference[]"
						style="width:50%"
						id="myvideoroom_security_allowed_roles_preference">
					<?php
					$output = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_multi_checkbox_admin_roles( $user_id, $room_name, 'allowed_roles' );
					//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
					echo $output;
					?>
					</select>
					<label for="myvideoroom_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<b><?php esc_html_e( 'Block', 'my-video-room' ); ?></b><?php esc_html_e( 'These Roles Instead', 'my-video-room' ); ?>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_security_block_role_control_enabled_preference"
					name="myvideoroom_security_block_role_control_enabled_preference"
					id="myvideoroom_security_block_role_control_enabled_preference_<?php	echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_block_role_control_enabled() ? 'checked' : ''; ?>/>
					<br>
					<br>
				<p><?php esc_html_e( 'Use this setting to determine what user roles you want to explicitly allow or - the reverse (block all users but a specific role) if you tick the Block Role option.', 'my-video-room' ); ?></p>
				<hr>
				<?php
				// This needs to be an action hook.
				if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {

					// Group setting from BP.
					if ( ( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_GROUP_ID ) ) &&
					( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_ID ) ) ) {
						echo esc_attr( Factory::get_instance( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPressConfig::class )->render_group_menu_options( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, $id_index ) );
					}
					// Friends Setting from BP.
					if ( ( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_FRIENDS_ID ) ) &&
					( Factory::get_instance( ModuleConfig::class )->module_activation_status( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_ID ) ) ) {
						echo esc_attr( Factory::get_instance( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPressConfig::class )->render_friends_menu_options( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, $id_index ) );
					}
				}
				?>
				<?php wp_nonce_field( 'myvideoroom_update_security_video_preference', 'nonce' ); ?>
				<hr>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  />
			</form>
			<hr>
</div>

	<?php
	return ob_get_clean();
};
