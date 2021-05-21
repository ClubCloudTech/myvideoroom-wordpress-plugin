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
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;

return function (
	SecurityVideoPreference $current_user_setting = null,
	string $room_name,
	int $id_index = 0,
	string $roles_output = null,
	int $user_id = null
	): string {
	ob_start();
	$room_object       = Factory::get_instance( RoomMap::class )->get_room_info( $user_id );
	$room_display_name = $room_object->room_name;
	?>
<div id="security-video-host-wrap" class="mvr-nav-shortcode-outer-wrap mvr-nav-settingstabs-outer-wrap">
			<h1><?php esc_html_e( 'Room Host Settings for ', 'my-video-room' ); ?>
			<?php
			$output_header = str_replace( '-', ' ', $room_display_name );
			echo esc_attr( ucwords( $output_header ) );
			?>
			</h1>
			<?php
			$output = null;
			$output = apply_filters( 'myvideoroom_security_roomhosts_preference_buttons', $output, $user_id, $room_name );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function escaped upstream.
			echo '<div class="mvr-button-table"> ' . $output . ' </div>';
			?>
			<form method="post" action="">
			<input name="myvideoroom_security_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
			<input name="myvideoroom_security_user_id" type="hidden" value="
				<?php
				$user_id = apply_filters( 'myvideoroom_security_admin_preference_user_id_intercept', $user_id );
				echo esc_html( $user_id );
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
				'If you enable this setting, anonymous users from the Internet will be able to enter your room and have full control of it as a host. Please consider the security ramifications of this setting. ',
				'my-video-room'
			);
			?>
			</p>
			<hr />
			<input type="hidden" name="myvideoroom_room_name" value="<?php echo esc_attr( $room_name ); ?>" />
			<input type="hidden" name="myvideoroom_user_id" value="<?php echo esc_attr( $user_id ); ?>" />

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( \MyVideoRoomPlugin\Library\HttpPost::class )->create_form_submit(
				'update_security_video_preference',
				\esc_html__( 'Save changes', 'myvideoroom' )
			);
			?>
		</form>
</div>

	<?php
	return ob_get_clean();
};
