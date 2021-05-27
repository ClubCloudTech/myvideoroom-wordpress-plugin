<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @param string|null $current_user_setting
 * @param array       $available_layouts
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Settings\Field as InputField;

return function (
	?SecurityVideoPreference $current_user_setting,
	string $room_name,
	int $id_index,
	string $roles_output,
	int $user_id = null
): string {
	ob_start();

	$html_library = Factory::get_instance( HTML::class, array( 'security' ) );

	/**
	 * This should be moved to the controller.
	 *
	 * @var InputField[] $fields
	 */
	$fields = array();

	do_action(
		'myvideoroom_security_preference_settings',
		function ( InputField $field ) use ( &$fields ) {
			$fields[] = $field;
		},
		$current_user_setting
	);
	?>

	<div id="security-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
		<h1><?php esc_html_e( 'Security Settings for ', 'my-video-room' ); ?>
			<?php
			$output = str_replace( '-', ' ', $room_name );
			echo esc_attr( ucwords( $output ) );
			?>
		</h1>
		<?php
		$output = null;
		$output = apply_filters( 'myvideoroom_security_settings_preference_buttons', $output, $user_id, $room_name );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function escaped upstream.
		echo '<div class="mvr-button-table"> ' . $output . ' </div>';
		?>
		<form method="post" action="">
			<h2 class="mvr-title-header"><i
					class="dashicons mvr-icons dashicons-dismiss"></i><?php esc_html_e( 'Disable Room', 'my-video-room' ); ?>
			</h2>
			<input
				type="checkbox"
				class="myvideoroom_security_room_disabled_preference"
				name="myvideoroom_security_room_disabled_preference"
				id="myvideoroom_security_room_disabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->is_room_disabled() ? 'checked' : ''; ?>
			/>
			<p class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Enable this setting to switch off your room. No one will be able to join it. ', 'my-video-room' ); ?>
			</p>
			<hr />
			<h2 class="mvr-title-header">
				<label for="myvideoroom_security_anonymous_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<i class="dashicons mvr-icons dashicons-admin-users"></i><?php esc_html_e( 'Restrict Anonymous Access ( Force Users to Sign In )', 'my-video-room' ); ?>
				</label>
			</h2>
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
					'If you enable this setting, anonymous users from the Internet WILL NOT be able to enter your room. The only way someone can enter your room is if they have an account on your website. This means that external users, will have to go through whatever registration process exists for your website. Default is disabled, which means anonymous access is allowed.',
					'my-video-room'
				);
				?>
			</p>

			<hr />
			<h2 class="mvr-title-header">
				<label
					for="myvideoroom_security_allow_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<i class="dashicons mvr-icons dashicons-id"></i><?php esc_html_e( 'Enable Role Control - For Allowed Roles', 'my-video-room' ); ?>
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
			<p class="mvr-preferences-paragraph">
				<?php
				esc_html_e(
					'If you enable this setting only the following roles will be allowed to access your rooms. If you want to reverse the setting, then click "block these roles instead" which will allow all roles - except for the ones you select. ',
					'my-video-room'
				);
				?>
			</p><br>

			<label for="myvideoroom_security_allowed_roles_preference_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Allowed Roles setting:', 'my-video-room' ); ?>
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
				<br><?php esc_html_e( 'Block These Roles Instead', 'my-video-room' ); ?>
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
					'Use this setting to determine what user roles you want to explicitly allow or - the reverse (block all users but a specific role) if you tick the Block Role option. Please Note: If you choose to Block a Role, you must still decide if you would like Anonymous Users to access the room separately in the Restrict Anonymous option above.',
					'my-video-room'
				);
				?>
			</p>
			<hr>
			<?php
			// Action Hook to Display additional Form Entries from other Modules.
			do_action( 'myvideoroom_security_preference_form', $user_id, $room_name, $id_index, $current_user_setting );

			foreach ( $fields as $field ) {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $field->to_string( $html_library );
				echo '<br />';
			}
			?>

			<input name="myvideoroom_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
			<input name="myvideoroom_user_id" type="hidden" value="
					<?php
					$user_id = apply_filters( 'myvideoroom_security_admin_preference_user_id_intercept', $user_id );
					echo esc_html( $user_id );
					?>
					" />

			<?php
			if ( $current_user_setting && $current_user_setting->is_site_override_enabled() ) {
				$site_override = true;
			} else {
				$site_override = false;
			}
			if ( false === $site_override ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( HttpPost::class )->create_form_submit(
					'update_security_video_preference',
					\esc_html__( 'Save changes', 'myvideoroom' )
				);
			}
			?>
		</form>
	</div>

	<?php
	return ob_get_clean();
};
