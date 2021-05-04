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
 * Render the Security Permissions Admin page
 *
 * @param array $all_wp_roles A list of WordPress roles. @see global $wp_roles->roles.
 */
return function (
	array $all_wp_roles = array()
): string {
	ob_start();
	?>
	<h2><?php esc_html_e( 'Video Security', 'myvideoroom' ); ?></h2>

	<h3><?php esc_html_e( 'Default Video Room Hosts', 'myvideoroom' ); ?></h3>
	<p>
		<?php
		esc_html_e(
			'By default you have two shortcodes generated for your pages by the room builder. One is for the Host, and one for guest.
            This setting configures, who the Video Engine will treat as a Host in case you haven\'t provided a Host Shortcode. 
            By default, the Application will take your Site Administrators Group as its default host. You can override that section below, and add additional WordPress roles to your Host permissions Matrix. ',
			'myvideoroom'
		);
		?>
	</p>
	<form method="post" action="">
		<fieldset>
			<table class="form-table" role="presentation">
			<?php
			foreach ( $all_wp_roles as $key => $single_role ) {
				$role_object   = get_role( $key );
				$has_admin_cap = $role_object->has_cap( Plugin::CAP_GLOBAL_ADMIN );

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
								type="checkbox"<?php echo $has_admin_cap ? ' checked="checked" ' : ''; ?>"
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
