<?php
/**
 * Renders the form for changing the host.
 *
 * @param string|null $current_user_setting
 * @param string      $room_name - the room name.
 * @param int         $id_index - to version element ids.
 * @param array       $roles_output - roles for checkbox selection.
 * @param int         $user_id - the user_id.
 * @param bool        $admin_view - Flag to denote admin view and not to render titles and Headers as the admin views do that.
 *
 * @package MyVideoRoomPlugin\Views\Public
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;

return function (
	?SecurityVideoPreference $current_user_setting,
	string $room_name,
	int $id_index = 0,
	string $roles_output = null,
	int $user_id = null,
	bool $admin_view = null
): string {
	ob_start();

	?>
	<div id="security-video-host-wrap" class="mvr-nav-shortcode-outer-wrap mvr-nav-settingstabs-outer-wrap">
	<?php
	if ( ! $admin_view ) {
		?>
		<h2 class="mvr-admin-hide mvr-override-h2">
		<?php esc_html_e( 'Security Settings for ', 'myvideoroom' ); ?>
			<?php
			$output = str_replace( '-', ' ', $room_name );
			echo esc_attr( ucwords( $output ) );
			?>
		</h2>

		<?php
	}
		$output = null;
		$output = apply_filters( 'myvideoroom_security_roomhosts_preference_buttons', $output, $user_id, $room_name );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function escaped upstream.
		echo '<div class="mvr-button-table"> ' . $output . ' </div>';

		$user_id = apply_filters( 'myvideoroom_security_admin_preference_user_id_intercept', $user_id );
	?>
		<form method="post" action="">
			<input name="myvideoroom_security_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
			<input name="myvideoroom_security_user_id" type="hidden" value="<?php echo esc_html( $user_id ); ?>" />
			<h2 class="mvr-title-header">
				<label
					for="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<i class="myvideoroom-dashicons mvr-icons dashicons-id myvideoroom-dashicons-override"></i><?php esc_html_e( 'Enable custom hosts for this room', 'myvideoroom' ); ?>
				</label>
			</h2>
			<input
				type="checkbox"
				class="myvideoroom_security_allow_role_control_enabled_preference"
				name="myvideoroom_security_allow_role_control_enabled_preference"
				id="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->is_allow_role_control_enabled() ? 'checked' : ''; ?>
			/>
			<br>
			<p class="mvr-preferences-paragraph mvr-paragraph-override">
				<?php esc_html_e( 'Please select the User Role Groups you would like to be able to host your room. Anyone not setup here will be a guest of your room.', 'myvideoroom' ); ?>
			</p><br>

			<label for="myvideoroom_security_allowed_roles_preference_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Allowed Host Groups', 'myvideoroom' ); ?>
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
			<label for="myvideoroom_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<br><?php esc_html_e( 'Block These Roles from Hosting Instead (allow anyone EXCEPT the above).', 'myvideoroom' ); ?>
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
			<p class="mvr-preferences-paragraph mvr-paragraph-override">
				<?php esc_html_e( 'Use this setting to determine what group roles you want to explicitly allow to host your room or - the reverse (block all users but a specific role from hosting). Please Note: If you choose to Block a Role, you must still decide if you would like Anonymous Users to host the room separately below.', 'myvideoroom' ); ?>
			</p>
			<hr>
			<h2 class="mvr-title-header">
				<label for="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<i class="myvideoroom-dashicons mvr-icons dashicons-admin-users myvideoroom-dashicons-override"></i><?php esc_html_e( 'Allow Anonymous Users to Host Rooms (not recommended)', 'myvideoroom' ); ?>
				</label>
			</h2>
			<input
				type="checkbox"
				class="myvideoroom_security_anonymous_enabled_preference"
				name="myvideoroom_security_anonymous_enabled_preference"
				id="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->is_anonymous_enabled() ? 'checked' : ''; ?>
			/>
			<p class="mvr-preferences-paragraph mvr-paragraph-override">
				<?php esc_html_e( 'If you enable this setting, anonymous users from the Internet will be able to enter your room and have full control of it as a host. Please consider the security ramifications of this setting. ', 'myvideoroom' ); ?>
			</p>
			<hr />
			<input type="hidden" name="myvideoroom_room_name" value="<?php echo esc_attr( $room_name ); ?>" />
			<input type="hidden" name="myvideoroom_user_id" value="<?php echo esc_attr( $user_id ); ?>" />

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( HttpPost::class )->create_form_submit(
				'update_security_video_preference',
				\esc_html__( 'Save changes', 'myvideoroom' )
			);
			?>
		</form>
	</div>

	<?php
	return ob_get_clean();
};
