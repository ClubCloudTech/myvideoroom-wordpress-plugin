<?php
/**
 * Output the custom permissions section for the room builder.
 *
 * @package MyVideoRoomPlugin\Module\CustomPermissions
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\CustomPermissions;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;

/**
 * Output the custom permissions section for the room builder.
 *
 * @param string[] $user_permissions The list of selected user permissions
 * @param string[] $role_permissions The list of selected role permissions
 *
 * @return string
 */
return function (
	array $user_permissions,
	array $role_permissions
): string {
	\ob_start();

	$html_lib = Factory::get_instance( HTML::class, array( 'room_builder_custom_permissions' ) );

	?>
	<fieldset class="custom-permissions">
		<legend><?php echo \esc_html__( 'Custom permissions', 'myvideoroom' ); ?></legend>

		<label for="<?php echo \esc_attr( $html_lib->get_id( 'users' ) ); ?>">Users</label>
		<select
			name="<?php echo \esc_attr( $html_lib->get_field_name( 'users' ) ); ?>[]"
			id="<?php echo \esc_attr( $html_lib->get_id( 'users' ) ); ?>"
			multiple
		>
			<option value=""<?php echo $user_permissions ? '' : ' selected'; ?>>— Any —</option>
			<?php
			$all_users = \get_users();
			foreach ( $all_users as $user ) {
				$selected = \in_array( $user->user_nicename, $user_permissions, true ) ? ' selected' : '';
				echo '<option value="' . \esc_attr( $user->user_nicename ) . '" ' . \esc_attr( $selected ) . '>' .
					\esc_html( $user->display_name ) . ' (' . \esc_attr( $user->user_nicename ) . ')' .
					'</option>';
			}
			?>
		</select>
		<br />
		<strong>— OR —</strong>

		<label for="<?php echo \esc_attr( $html_lib->get_id( 'roles' ) ); ?>">Roles</label>
		<select
			name="<?php echo \esc_attr( $html_lib->get_field_name( 'roles' ) ); ?>[]"
			id="<?php echo \esc_attr( $html_lib->get_id( 'roles' ) ); ?>"
			multiple
		>
			<option value=""<?php echo $role_permissions ? '' : ' selected'; ?>>— Any —</option>
			<?php
			global $wp_roles;
			$all_roles = $wp_roles->roles;

			foreach ( $all_roles as $role_name => $role_details ) {
				$selected = \in_array( $role_name, $role_permissions, true ) ? ' selected' : '';
				echo '<option value="' . \esc_attr( $role_name ) . '" ' . \esc_attr( $selected ) . '>' .
					\esc_html( $role_details['name'] ) . ' (' . \esc_attr( $role_name ) . ')' .
					'</option>';
			}
			?>
		</select>
	</fieldset>

	<?php
	return \ob_get_clean();
};
