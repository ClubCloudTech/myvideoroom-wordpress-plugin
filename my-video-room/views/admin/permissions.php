<?php
/**
 * Outputs the Permissions Settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );
global $wp_roles;

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
			'The room builder can either generate two shortcodes where one is for the Host, and one for guest. 
			Alternatively you can generate a single shortcode, and use these setting to configure who the video engine 
			will treat as a host. This section allows you to add and remove  WordPress roles to your host permissions 
			matrix.',
			'myvideoroom'
		);
		?>
	</p>

	<form method="post" action="">
		<fieldset>
			<table class="form-table" role="presentation">
			<?php
			foreach ( $all_wp_roles as $key => $single_role ) {
				$role_object  = get_role( $key );
				$has_host_cap = $role_object->has_cap( Plugin::CAP_GLOBAL_HOST );

				?>
					<tr>
						<th scope="row">
							<label for="role_<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $single_role['name'] ); ?>
							</label>
						</th>

						<td>
							<input class="myvideoroom-admin-table-format"
								id="role_<?php echo esc_attr( $key ); ?>"
								name="role_<?php echo esc_attr( $key ); ?>"
								type="checkbox"<?php echo $has_host_cap ? ' checked="checked" ' : ''; ?>"
							/>
						</td>
					</tr>
				<?php
			}
			?>
			</table>
		</fieldset>

		<?php wp_nonce_field( 'update_caps', 'nonce' ); ?>
		<?php submit_button(); ?>
	</form>

	<?php
	return ob_get_clean();
};
