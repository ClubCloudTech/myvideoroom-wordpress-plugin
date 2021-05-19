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
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Dao\SecurityVideoPreference as SecurityVideoPreferenceDAO;

return function (
	SecurityVideoPreference $current_user_setting = null,
	string $room_name,
	int $id_index = 0,
	int $user_id = null
	): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	ob_start();
	$room_object       = Factory::get_instance( RoomMap::class )->get_room_info( $user_id );
	$room_display_name = $room_object->room_name;
	?>
<div id="security-video-host-wrap" class="mvr-nav-shortcode-outer-wrap mvr-nav-settingstabs-outer-wrap">
			<h1><?php esc_html_e( 'Room Host Settings for ', 'my-video-room' ); ?>
			<?php
			$output = str_replace( '-', ' ', $room_display_name );
			echo esc_attr( ucwords( $output ) );
			?>
				</h1>
				<?php
				// room permissions info.
				$site_override              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
				$anonymous_enabled          = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
				$allow_role_control_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
				$block_role_control         = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'block_role_control_enabled' );
				$output                     = null;

				if ( ! $site_override ) {

					if ( $allow_role_control_enabled ) {
						$db_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allowed_roles' );
						if ( ! $db_setting ) {
							$db_setting = 'No One';
						}
						if ( $block_role_control ) {
							$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Hosts All Except : ', 'my-video-room' ) . str_replace( '|', ' - ', $db_setting ) . '</p>';
						} else {
							$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Hosts Only Allowing : ', 'my-video-room' ) . str_replace( '|', ' - ', $db_setting ) . '</p>';
						}
					}

					if ( $anonymous_enabled ) {
						$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Anonymous Hosting Enabled', 'my-video-room' ) . '</p>';
					}
				} else {
					$output .= Factory::get_instance( SecurityButtons::class )->site_wide_enabled( 'nourl' );
					$output .= '<p class="mvr-preferences-paragraph">' . esc_html__( 'An Administrator is overriding your settings with ones applied centrally. Certains Settings stored here may not be applied', 'my-video-room' ) . '</p>';
				}
				// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - Not Needed already escaped.
				echo '<div class="mvr-button-table"> ' . $output . ' </div>';
				?>
				<form method="post" action="">
				<input name="myvideoroom_security_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
				<input name="myvideoroom_security_user_id" type="hidden" value="
					<?php

					$buddypress_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( Dependencies::MODULE_BUDDYPRESS_ID );
					if ( $buddypress_enabled ) {
						echo esc_html( Factory::get_instance( \MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons::class )->site_wide_enabled() );
					}

					if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
						if ( bp_is_active( 'groups' ) ) {
							if ( function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() ) {
								global $bp;
								$group_id = $bp->groups->current_group->id;
								echo esc_attr( $group_id );
							}
						}
					}
					?>
					"/>
					<label for="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<h2 class ="mvr-title-header"><i class="dashicons mvr-icons dashicons-id"></i><?php esc_html_e( 'Enable custom hosts for this room', 'my-video-room' ); ?></h2>
					</label>
					<input
							type="checkbox"
							class="myvideoroom_security_allow_role_control_enabled_preference"
							name="myvideoroom_security_allow_role_control_enabled_preference"
							id="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_allow_role_control_enabled() ? 'checked' : ''; ?>
					/>
					<br>
					<p class="mvr-preferences-paragraph">
					<?php
					esc_html_e(
						'Please select the User Role Groups you would like to be able to host your room. Anyone not setup here will be a guest of your room.',
						'my-video-room'
					);
					?>
					</p><br>

				<label for="myvideoroom_security_allowed_roles_preference_<?php echo esc_attr( $id_index ); ?>" class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Allowed Host Groups', 'my-video-room' ); ?>
				</label>
				<select multiple="multiple"
						class="mvr-roles-multiselect mvr-select-box"
						name="myvideoroom_security_allowed_roles_preference[]"
						id="myvideoroom_security_allowed_roles_preference">
					<?php
					$roles_output = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_multi_checkbox_admin_roles( $user_id, $room_name, 'allowed_roles' );
					//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already escapes HTML properly upstream.
					echo $roles_output;
					?>
				</select>
				<br>
					<label for="myvideoroom_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>" class="mvr-preferences-paragraph">
					<br><?php esc_html_e( 'Block These Roles from Hosting Instead (allow anyone EXCEPT the above).', 'my-video-room' ); ?>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_security_block_role_control_enabled_preference"
					name="myvideoroom_security_block_role_control_enabled_preference"
					id="myvideoroom_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php
						echo $current_user_setting && $current_user_setting->is_block_role_control_enabled() ? 'checked' : '';
					?>
				/>
					<br>
					<br>
					<p class="mvr-preferences-paragraph">
						<?php
						esc_html_e(
							'Use this setting to determine what group roles you want to explicitly allow to host your room or - the reverse (block all users but a specific role from hosting). Please Note: 
							If you choose to Block a Role, you must still decide if you would like Anonymous Users to host the room separately below.',
							'my-video-room'
						);
						?>
					</p>
				<hr>
				<label for="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<h2 class ="mvr-title-header"><i class="dashicons mvr-icons dashicons-admin-users"></i><?php esc_html_e( 'Allow Anonymous Users to Host Rooms (not recommended)', 'my-video-room' ); ?></h2>
				</label>
				<input
					type="checkbox"
					class="myvideoroom_security_anonymous_enabled_preference"
					name="myvideoroom_security_anonymous_enabled_preference"
					id="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $current_user_setting && $current_user_setting->is_anonymous_enabled() ? 'checked' : ''; ?>
				/>
				<p class="mvr-preferences-paragraph">
				<?php
				esc_html_e(
					'If you enable this setting, anonymous users from the Internet will be able to enter your room and have full control of it as a host. Please
					consider the security ramifications of this setting. ',
					'my-video-room'
				);
				?>
				</p>

				<hr />

				<input type="hidden" name="myvideoroom_room_name" value="<?php echo esc_attr( $room_name ); ?>" />
				<input type="hidden" name="myvideoroom_user_id" value="<?php echo esc_attr( $user_id ); ?>" />
				<input type="hidden" name="myvideoroom_type" value="securityroomhost" />

				<?php wp_nonce_field( 'myvideoroom_update_security_video_preference', 'nonce' ); ?>

				<input type="submit" name="submit" id="submit" class="mvr-form-button" value="Save Changes"  />
			</form>
</div>

	<?php
	return ob_get_clean();
};
