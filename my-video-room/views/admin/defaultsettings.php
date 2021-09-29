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
 * Render the Default Settings Admin page
 *
 * @param array $all_wp_roles A list of WordPress roles. @see global $wp_roles->roles.
 */
return function (
	array $all_wp_roles = array()
): string {
	\ob_start();
	$string_randomizer_input = 'defaulthosts';
	$html_library            = Factory::get_instance( HTML::class, array( $string_randomizer_input ) );
	$inbound_tabs            = array();
	$tabs                    = apply_filters( 'myvideoroom_permissions_manager_menu', $inbound_tabs );
	$tab_count               = count( $tabs );

	if ( $tab_count >= 1 ) {
		?>
		<h2><?php esc_html_e( 'MyVideoRoom Default Settings', 'my-video-room' ); ?></h2>
		<p>
			<?php esc_html_e( 'This section allows you manage the default room appearance as well as permissions, guest/host decisions, and room security settings across all of your rooms.', 'myvideoroom' ); ?>
		</p>
		<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
			<ul>
				<li>
					<a class="nav-tab nav-tab-active"
						href="#<?php echo esc_attr( $html_library->get_id( $string_randomizer_input ) ); ?>">
						<?php esc_html_e( 'Site Default Hosts', 'myvideoroom' ); ?>
					</a>
				</li>
				<?php
				foreach ( $tabs as $menu_output ) {
					$tab_display_name = $menu_output->get_tab_display_name();
					$tab_slug         = $menu_output->get_tab_slug();
					?>

					<li>
						<a class="nav-tab" href="#<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
							<?php echo esc_html( $tab_display_name ); ?>
						</a>
					</li>
					<?php
				}
				?>
			</ul>
		</nav>
		<?php
	}
	?>
	<article class="mvr-admin-page-wrap"
		id="<?php echo esc_attr( $html_library->get_id( $string_randomizer_input ) ); ?>">
		<h2><?php \esc_html_e( 'Site Level Default Hosts', 'myvideoroom' ); ?></h2>

		<p>
			<?php
			\esc_html_e(
				'This setting governs who is a host and who is not in shortcodes, where you do not supply that information, or your module is unsure how to treat a host. You can either generate two shortcodes where one is for the host, and one for guest. Alternatively you can generate a single shortcode, and use these setting to configure who the video engine will treat as a host. This section allows you to add and remove WordPress roles to your host permissions matrix.',
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
						++ $index;

						$role         = \get_role( $role_name );
						$has_host_cap = $role->has_cap( Plugin::CAP_GLOBAL_HOST );

						?>
						<tr<?php echo $index % 2 ? ' class="alternate"' : ''; ?>>
							<th scope="row">
								<label for="<?php echo \esc_attr( $html_library->get_id( 'role_' . $role_name ) ); ?>">
									<?php echo \esc_html( $role_details['name'] ); ?>
								</label>
							</th>

							<td>
								<input class="myvideoroom-admin-table-format"
									id="<?php echo \esc_attr( $html_library->get_id( 'role_' . $role_name ) ); ?>"
									name="<?php echo \esc_attr( $html_library->get_field_name( 'role_' . $role_name ) ); ?>"
									type="checkbox" <?php echo $has_host_cap ? ' checked="checked" ' : ''; ?>" value="on" />
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
	</article>
	<?php
	foreach ( $tabs as $article_output ) {

		$tab_slug = $article_output->get_tab_slug();
		?>
		<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $article_output->get_function_callback();
			?>
		</article>
		<?php
	}

	return \ob_get_clean();
};
