<?php
/**
 * Outputs the Permissions Settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\HttpPost;

/**
 * Render the Room Permissions Admin page
 *
 * @param array $all_wp_roles A list of WordPress roles. @see global $wp_roles->roles.
 */
return function (
	array $all_wp_roles = array()
): string {
	\ob_start();

	$html_lib = Factory::get_instance( HTML::class, array( 'permissions' ) );

	?>
	<h2><?php \esc_html_e( 'Default room permissions', 'myvideoroom' ); ?></h2>

	<p>
		<?php
		\esc_html_e(
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
					<th><?php \esc_html_e( 'WordPress role', 'myvideoroom' ); ?></th>
					<th><?php \esc_html_e( 'Has default host permission', 'myvideoroom' ); ?></th>
				</tr>
				</thead>
				<tbody>
			<?php
			$index = 0;
			foreach ( $all_wp_roles as $role_name => $role_details ) {
				++$index;

				$role         = \get_role( $role_name );
				$has_host_cap = $role->has_cap( Plugin::CAP_GLOBAL_HOST );

				?>
					<tr<?php echo $index % 2 ? ' class="alternate"' : ''; ?>>
						<th scope="row">
							<label for="<?php echo \esc_attr( $html_lib->get_id( 'role_' . $role_name ) ); ?>">
								<?php echo \esc_html( $role_details['name'] ); ?>
							</label>
						</th>

						<td>
							<input class="myvideoroom-admin-table-format"
								id="<?php echo \esc_attr( $html_lib->get_id( 'role_' . $role_name ) ); ?>"
								name="<?php echo \esc_attr( $html_lib->get_field_name( 'role_' . $role_name ) ); ?>"
								type="checkbox"<?php echo $has_host_cap ? ' checked="checked" ' : ''; ?>"
								value="on"
							/>
						</td>
					</tr>
				<?php
			}
			?>
				</tbody>
			</table>
		</fieldset>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( HttpPost::class )->create_admin_form_submit( 'update_permissions' );
		?>
	</form>

	<?php
	return \ob_get_clean();
};
