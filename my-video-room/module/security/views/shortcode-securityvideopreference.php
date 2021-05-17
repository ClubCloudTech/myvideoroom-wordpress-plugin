<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public
 */

use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\DAO\ModuleConfig;
use MyVideoRoomPlugin\Core\DAO\RoomMap;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Dao\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons;

return function (
	SecurityVideoPreference $current_user_setting = null,
	string $room_name,
	int $id_index = 0,
	int $user_id = null
	): string {
	wp_enqueue_style( 'mvr-template' );
	wp_enqueue_style( 'mvr-menutab-header' );
	ob_start(); ?>
<div id="security-video-host-wrap"class="mvr-nav-settingstabs-outer-wrap">
			<h1><?php echo esc_html__( 'Security Settings for ', 'my-video-room' ); ?>
			<?php
			$output = str_replace( '-', ' ', $room_name );
			echo esc_attr( ucwords( $output ) );
			?>
				</h1>
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
						$output .= '<p class="mvr-main-button-enabled" >' . esc_html__( 'Room Enabled', 'my-video-room' ) . '</p>';
					} else {
						$output .= '<p class="mvr-main-button-disabled" >' . esc_html__( 'Room Disabled', 'my-video-room' ) . '</p>';
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
					$output .= '<p class="mvr-preferences-paragraph">' . esc_html__( 'An Administrator is overriding your settings with ones applied centrally. Certains Settings stored here may not be applied', 'my-video-room' ) . '</p>';
				}
				// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - Not Needed already escaped.
				echo '<div class="mvr-button-table"> ' . $output . ' </div>';
				?>
				<form method="post" action="">
				<input name="myvideoroom_extras_security_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
				<input name="myvideoroom_extras_security_user_id" type="hidden" value="
					<?php
					if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
						if ( function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() ) {
							global $bp;
							$group_id = $bp->groups->current_group->id;
							echo esc_attr( $group_id );
						}
					}
					?>
					"/>
					<hr>
					<h2 class="mvr-title-header"><i class="dashicons mvr-icons dashicons-dismiss"></i><?php echo esc_html__( 'Disable Room', 'my-video-room' ); ?></h2>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_extras_security_room_disabled_preference"
					name="myvideoroom_extras_security_room_disabled_preference"
					id="myvideoroom_extras_security_room_disabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_room_disabled() ? 'checked' : ''; ?>
				/>
				<p class="mvr-preferences-paragraph">
				<?php echo esc_html__( 'Enable this setting to switch off your room. No one will be able to join it. ', 'my-video-room' ); ?>
				</p>
				<hr />
				<label for="myvideoroom_extras_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<h2 class ="mvr-title-header"><i class="dashicons mvr-icons dashicons-admin-users"></i><?php echo esc_html__( 'Restrict Anonymous Access ( Force Users to Sign In )', 'my-video-room' ); ?></h2>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_extras_security_anonymous_enabled_preference"
					name="myvideoroom_extras_security_anonymous_enabled_preference"
					id="myvideoroom_extras_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_anonymous_enabled() ? 'checked' : ''; ?>
				/>
				<p class="mvr-preferences-paragraph">
				<?php
				echo esc_html__(
					'If you enable this setting, anonymous users from the Internet WILL NOT be able to enter your room. The only way
					someone can enter your room is if they have an account on your website. This means that external users, will have 
					to go through whatever registration process exists for your website. Default is disabled, which means anonymous access is allowed.',
					'my-video-room'
				);
				?>
				</p>

				<hr />
					<label for="myvideoroom_extras_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<h2 class ="mvr-title-header"><i class="dashicons mvr-icons dashicons-id"></i><?php echo esc_html__( 'Enable Role Control - For Allowed Roles', 'my-video-room' ); ?></h2>
					</label>
					<input
							type="checkbox"
							class="myvideoroom_extras_security_allow_role_control_enabled_preference"
							name="myvideoroom_extras_security_allow_role_control_enabled_preference"
							id="myvideoroom_extras_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_allow_role_control_enabled() ? 'checked' : ''; ?>
					/>
					<br>
					<p class="mvr-preferences-paragraph">
					<?php
					echo esc_html__(
						'If you enable this setting only the following roles will be allowed to access your rooms. If you want to reverse the setting, then click 
						"block these roles instead" which will allow all roles - except for the ones you select. ',
						'my-video-room'
					);
					?>
					</p><br>

				<label for="myvideoroom_extras_security_allowed_roles_preference_<?php echo esc_attr( $id_index ); ?>" class="mvr-preferences-paragraph">
				<?php echo esc_html__( 'Allowed Roles setting:', 'my-video-room' ); ?>
				</label>
				<select multiple="multiple"
						class="mvr-roles-multiselect mvr-select-box"
						name="myvideoroom_extras_security_allowed_roles_preference[]"
						id="myvideoroom_extras_security_allowed_roles_preference">
					<?php
					$roles_output = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_multi_checkbox_admin_roles( $user_id, $room_name, 'allowed_roles' );
					//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already escapes HTML properly upstream.
					echo $roles_output;
					?>
				</select>
				<br>
					<label for="myvideoroom_extras_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>" class="mvr-preferences-paragraph">
					<br><?php echo esc_html__( 'Block These Roles Instead', 'my-video-room' ); ?>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_extras_security_block_role_control_enabled_preference"
					name="myvideoroom_extras_security_block_role_control_enabled_preference"
					id="myvideoroom_extras_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php
						echo $current_user_setting && $current_user_setting->is_block_role_control_enabled() ? 'checked' : '';
					?>
				/>
					<br>
					<br>
					<p class="mvr-preferences-paragraph">
						<?php
						echo esc_html__(
							'Use this setting to determine what user roles you want to explicitly allow or - the reverse (block all users but a specific role) if you tick the Block Role option. Please Note: 
							If you choose to Block a Role, you must still decide if you would like Anonymous Users to access the room separately in the Restrict Anonymous option above.',
							'my-video-room'
						);
						?>
					</p>
				<hr>
				<?php

				// TODO This needs to be an action hook.
				if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
					global $bp;
					$is_group_page = $bp->groups->current_group->slug;
					$room_object   = Factory::get_instance( RoomMap::class )->get_room_info( $user_id );
					// Group setting from BP.
					if ( ( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_GROUP_ID ) ) && ! $room_object &&
					( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_ID ) ) && $is_group_page ) {
						echo esc_attr( Factory::get_instance( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPressConfig::class )->render_group_menu_options( $bp->groups->current_group->creator_id, $room_name, $id_index ) );
					} elseif ( SiteDefaults::USER_ID_SITE_DEFAULTS === $user_id ) {
						echo esc_attr( Factory::get_instance( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPressConfig::class )->render_group_menu_options( SiteDefaults::USER_ID_SITE_DEFAULTS, MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_GROUP_NAME, $id_index ) );
					}
					// Friends Setting from BP.

					if ( ( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_FRIENDS_ID ) ) &&
					( Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::MODULE_BUDDYPRESS_ID ) ) && ! $room_object && ! $is_group_page ) {
						echo esc_attr( Factory::get_instance( MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPressConfig::class )->render_friends_menu_options( $user_id, $room_name, $id_index ) );
					}
				}
				?>


				<?php wp_nonce_field( 'myvideoroom_extras_update_security_video_preference', 'nonce' ); ?>

				<input type="submit" name="submit" id="submit" class="mvr-form-button" value="Save Changes"  />
			</form>
</div>

	<?php
	return ob_get_clean();
};
