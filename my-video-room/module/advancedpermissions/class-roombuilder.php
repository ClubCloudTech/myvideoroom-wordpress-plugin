<?php
/**
 * The entry point for the AdvancedPermissions module
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\AdvancedPermissions;

use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\RoomPermissionsOption;

/**
 * Class Module
 */
class RoomBuilder {


	public function __construct() {
		add_filter(
			'myvideoroom_roombuilder_create_shortcode',
			function ( AppShortcodeConstructor $shortcode_constructor ) {

				$use_advanced_permissions = $this->get_radio_post_parameter( 'room_permissions_preference' ) === 'use_advanced_permissions';

				if ( $use_advanced_permissions ) {
					$admin_strings = array();

					$user_permissions = $this->get_multi_post_parameter( 'advanced_permissions_users' );
					$role_permissions = $this->get_multi_post_parameter( 'advanced_permissions_roles' );

					if ( $user_permissions ) {
						$admin_strings[] = 'user:' . implode( ',', $user_permissions );
					}

					if ( $role_permissions ) {
						$admin_strings[] = 'role:' . implode( ',', $role_permissions );
					}

					if ( $admin_strings ) {
						$shortcode_constructor->add_custom_string_param( 'host', implode( ';', $admin_strings ) );
					}
				}

				return $shortcode_constructor;
			},
			0,
			1
		);

		add_filter(
			'myvideoroom_roombuilder_permission_options',
			function ( array $options ) {
				$use_advanced_permissions = $this->get_radio_post_parameter( 'room_permissions_preference' ) === 'use_advanced_permissions';

				foreach ( $options as $permission ) {
					$permission->set_checked( $permission->is_checked() && ! $use_advanced_permissions );
				}

				$options[] = new RoomPermissionsOption(
					'use_advanced_permissions',
					$use_advanced_permissions,
					__( 'Use advanced permissions', 'myvideoroom' ),
					esc_html__(
						'This will allow you to select which users or roles will be an admin for this shortcode.',
						'myvideoroom'
					),
				);

				return $options;
			}
		);

		add_action(
			'myvideoroom_roombuilder_permission_section',
			function ($id_index) {
				ob_start();
				?>
			<fieldset>
			<legend><?php echo esc_html__( 'Advanced permissions', 'myvideoroom' ); ?></legend>

<label for="myvideoroom_room_builder_advanced_permissions_users<?php echo esc_attr( $id_index ); ?>">Users</label>
<select
	name="myvideoroom_room_builder_advanced_permissions_users[]"
	id="myvideoroom_room_builder_advanced_permissions_users<?php echo esc_attr( $id_index ); ?>"
	multiple
>
	<option value="" selected>— None —</option>
				<?php
				$all_users = \get_users();
				foreach ( $all_users as $user ) {
					echo '<option value="' . esc_attr( $user->user_login ) . '">' . esc_html( $user->display_name ) . '</option>';
				}
				?>
</select>
<br />

<label for="myvideoroom_room_builder_advanced_permissions_roles<?php echo esc_attr( $id_index ); ?>">Roles</label>
<select
	name="myvideoroom_room_builder_advanced_permissions_roles[]"
	id="myvideoroom_room_builder_advanced_permissions_roles<?php echo esc_attr( $id_index ); ?>"
	multiple
>
	<option value="" selected>— None —</option>
				<?php
				global $wp_roles;
				$all_roles = $wp_roles->roles;

				foreach ( $all_roles as $role_name => $role_details ) {
					echo '<option value="' . esc_attr( $role_name ) . '">' . esc_html( $role_details['name'] ) . '</option>';
				}
				?>
</select>
</fieldset>
				<?php
				echo ob_get_clean();
			}
		);
	}

	/**
	 * Get a string from the $_POST
	 *
	 * @param string $name The name of the field.
	 *
	 * @return array
	 */
	private function get_multi_post_parameter( string $name ): array {
		$options = $_POST[ 'myvideoroom_room_builder_' . $name ] ?? array();

		$return = array();

		foreach ( $options as $option ) {
			$value = trim( sanitize_text_field( wp_unslash( $option ) ) );
			if ( $value ) {
				$return[] = $value;
			}
		}

		return $return;
	}

	/**
	 * Get a value from a $_POST radio field
	 *
	 * @param string $name    The name of the field.
	 *
	 * @return string
	 */
	private function get_radio_post_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return sanitize_text_field( wp_unslash( $_POST[ 'myvideoroom_room_builder_' . $name ] ?? '' ) );
	}


}
