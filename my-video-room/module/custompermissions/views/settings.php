<?php
/**
 * Output the custom permissions section for the room builder.
 *
 * @package MyVideoRoomPlugin\Module\CustomPermissions
 */

/**
 * Output the custom permissions section for the room builder.
 *
 * @param string[] $user_permissions The list of selected user permissions
 * @param string[] $role_permissions The list of selected role permissions
 * @param integer  $id_index         A unique id to generate unique id names
 *
 * @return string
 */
return function (
	array $user_permissions,
	array $role_permissions,
	int $id_index = 0
): string {
	ob_start();

	?>
	<fieldset class="custom-permissions">
		<legend><?php echo esc_html__( 'Custom permissions', 'myvideoroom' ); ?></legend>

		<label for="myvideoroom_room_builder_custom_permissions_users_<?php echo esc_attr( $id_index ); ?>">Users</label>
		<select
				name="myvideoroom_room_builder_custom_permissions_users[]"
				id="myvideoroom_room_builder_custom_permissions_users_<?php echo esc_attr( $id_index ); ?>"
				multiple
		>
			<option value=""<?php echo $user_permissions ? '' : ' selected'; ?>>— Any —</option>
			<?php
			$all_users = \get_users();
			foreach ( $all_users as $user ) {
				$selected = in_array( $user->user_nicename, $user_permissions, true ) ? ' selected' : '';
				echo '<option value="' . esc_attr( $user->user_nicename ) . '" ' . esc_attr( $selected ) . '>' .
					esc_html( $user->display_name ) . ' (' . esc_attr( $user->user_nicename ) . ')' .
					'</option>';
			}
			?>
		</select>
		<br />
		<strong>— OR —</strong>

		<label for="myvideoroom_room_builder_custom_permissions_roles_<?php echo esc_attr( $id_index ); ?>">Roles</label>
		<select
				name="myvideoroom_room_builder_custom_permissions_roles[]"
				id="myvideoroom_room_builder_custom_permissions_roles_<?php echo esc_attr( $id_index ); ?>"
				multiple
		>
			<option value=""<?php echo $role_permissions ? '' : ' selected'; ?>>— Any —</option>
			<?php
			global $wp_roles;
			$all_roles = $wp_roles->roles;

			foreach ( $all_roles as $role_name => $role_details ) {
				$selected = in_array( $role_name, $role_permissions, true ) ? ' selected' : '';
				echo '<option value="' . esc_attr( $role_name ) . '" ' . esc_attr( $selected ) . '>' .
					esc_html( $role_details['name'] ) . ' (' . esc_attr( $role_name ) . ')' .
					'</option>';
			}
			?>
		</select>
	</fieldset>

	<?php
	return ob_get_clean();
};
