<?php
/**
 * Outputs the Permissions Settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Plugin;

/**
 * Render the Room Permissions Admin page
 *
 * @param array $all_wp_roles A list of WordPress roles. @see global $wp_roles->roles.
 */
return function (
	array $all_wp_roles = array()
): string {
	ob_start();
	?>
	<h2><?php esc_html_e( 'Default room permissions', 'myvideoroom' ); ?></h2>

	<p>
		<?php
		esc_html_e(
			'You can either generate two shortcodes where one is for the host, and one for guest. Alternatively you can generate a single shortcode, and use these setting to configure who the video engine will treat as a host. This section allows you to add and remove WordPress roles to your host permissions matrix.',
			'myvideoroom'
		);
		?>
	</p>

	<form method="post" action="">
		<fieldset>
			<table class="myvideoroom-permissions widefat" role="presentation">
				<thead>
				<tr>
					<th><?php esc_html_e( 'WordPress role', 'myvideoroom' ); ?></th>
					<th><?php esc_html_e( 'Has default host permission', 'myvideoroom' ); ?></th>
				</tr>
				</thead>
				<tbody>
			<?php
			$index = 0;
			foreach ( $all_wp_roles as $role_name => $role_details ) {
				++$index;

				$role         = get_role( $role_name );
				$has_host_cap = $role->has_cap( Plugin::CAP_GLOBAL_HOST );

				?>
					<tr<?php echo $index % 2 ? ' class="alternate"' : ''; ?>>
						<th scope="row">
							<label for="role_<?php echo esc_attr( $role_name ); ?>">
								<?php echo esc_html( $role_details['name'] ); ?>
							</label>
						</th>

						<td>
							<input class="myvideoroom-admin-table-format"
								id="role_<?php echo esc_attr( $role_name ); ?>"
								name="role_<?php echo esc_attr( $role_name ); ?>"
								type="checkbox"<?php echo $has_host_cap ? ' checked="checked" ' : ''; ?>"
							/>
						</td>
					</tr>
				<?php
			}
			?>
				</tbody>
			</table>
		</fieldset>

		<?php wp_nonce_field( 'update_caps', 'myvideoroom_permissions_nonce' ); ?>
		<?php submit_button(); ?>
	</form>

	<?php
	return ob_get_clean();
};
