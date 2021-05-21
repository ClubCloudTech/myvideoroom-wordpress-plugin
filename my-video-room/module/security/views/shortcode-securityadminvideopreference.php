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
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Settings\Field as InputField;

return function (
	?SecurityVideoPreference $current_user_setting,
	string $room_name,
	int $id_index = 0,
	$roles_output = null,
	int $user_id = null
	): string {

	$html_library = Factory::get_instance( HTML::class, array( 'security' ) );

	/**
	 * This should be moved to the controller.
	 *
	 * @var InputField[] $fields
	 */
	$fields = array();

	do_action(
		'myvideoroom_security_preference_settings',
		function( InputField $field ) use ( &$fields ) {
			$fields[] = $field;
		},
		$current_user_setting
	);

	ob_start();
	?>
<div id="security-video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">

			<?php
			$output = null;
			$output = apply_filters( 'myvideoroom_security_admin_preference_buttons', $output, $user_id, $room_name );
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
					" />
					<br>
					<h2 class="mvr-title-header"><?php esc_html_e( 'Override User preferences', 'my-video-room' ); ?></h2>
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
					//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
					echo $roles_output;
					?>
					</select>
					<label for="myvideoroom_security_block_role_control_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<strong><?php esc_html_e( 'Block', 'my-video-room' ); ?></strong><?php esc_html_e( 'These Roles Instead', 'my-video-room' ); ?>
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
					// Action Hook to Display additional Form Entries from other Modules.
					do_action( 'myvideoroom_security_preference_form', $user_id, $room_name, $id_index, $current_user_setting );

				foreach ( $fields as $field ) {
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $field->to_string( $html_library );
					echo '<br />';
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
