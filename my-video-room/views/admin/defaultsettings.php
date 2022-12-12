<?php
/**
 * Outputs the Permissions Settings for the video plugin
 *
 * @package MyVideoRoomPlugin/views/admin/defaultsettings.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\RoomBuilder\Module;

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
	$target                  = null;
	$index                   = 690;

	?>
<!-- Module Header -->
<div class="myvideoroom-menu-settings">
	<div class="myvideoroom-header-table-left-reduced">
		<h1><i title="<?php esc_html_e( 'Visit Module Control Center to change or add modules.', 'myvideoroom' ); ?>"
				class="myvideoroom-header-dashicons dashicons-admin-tools"></i><?php esc_html_e( 'Settings and Control Panel', 'myvideoroom' ); ?>
		</h1>

	</div>
	<div class="myvideoroom-header-table-right-wide">
			<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Modules:', 'myvideoroom' ); ?>
			<a href="<?php echo \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ); ?>">
			<i class="myvideoroom-header-dashicons dashicons-admin-plugins"></i></a>	
		</h3>
	</div>
</div>
<p style>
	<?php
		echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
			\esc_html__(
				'You can visit the %s section to add extra room modules, features, and expand the power of you rooms by addin packs and plugin integration modules.',
				'myvideoroom'
			),
			'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ) . '">' .
			\esc_html__( 'Modules', 'myvideoroom' ) .
			'</a>'
		)
	?>
</p>

<!-- Module State and Description Marker -->

			<?php esc_html_e( 'This section allows you manage the default room appearance as well as permissions, guest/host decisions, and room security settings across all of your rooms.', 'myvideoroom' ); ?>
		</p>
		<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
			<ul class="mvr-ul-style-top-menu">
				<li>
					<a class="nav-tab nav-tab-active"
						href="#defhosts<?php echo esc_attr( $html_library->get_id( $string_randomizer_input ) ); ?>">
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
	<article class="mvr-admin-page-wrap"
		id="defhosts<?php echo esc_attr( $html_library->get_id( $string_randomizer_input ) ); ?>">
	<!-- Module Header -->
	<div class="myvideoroom-menu-settings <?php echo esc_attr( $target ); ?>">
		<div class="myvideoroom-header-table-left-reduced">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-video-alt2"></i><?php esc_html_e( 'Default Hosts (For Custom Shortcodes)', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right-wide">
		<h3 class="myvideoroom-settings-offset"><?php esc_html_e( 'Settings:', 'myvideoroom' ); ?><i data-target="<?php echo esc_attr( $target ); ?>" class="myvideoroom-header-dashicons dashicons-admin-settings " title="<?php esc_html_e( 'Go to Settings - Personal Meeting Rooms', 'myvideoroom' ); ?>"></i>
			</h3>
		</div>
	</div>

<!-- Module State and Description Marker -->
<div class="myvideoroom-feature-outer-table">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'What This Does', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">

			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<h2><?php esc_html_e( 'Set Default Hosting Rights for Shortcodes', 'myvideoroom' ); ?></h2>
						<p style>
	<?php
		echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
			\esc_html__(
				'This setting governs who is a host and who is not in shortcodes if generated in %s, or manually. You can either generate two shortcodes where one is for the host, and one for guest. Alternatively you can generate a single shortcode, and use these setting to configure who the video engine will treat as a host.',
				'myvideoroom'
			),
			'<a href="' . \esc_url( \menu_page_url( Module::PAGE_SLUG_BUILDER, false ) ) . '">' .
			\esc_html__( 'Room Design', 'myvideoroom' ) .
			'</a>'
		)
	?>
</p>
			<p>
				<?php
				esc_html_e(
					'If you use Conference Rooms, Personal Video Rooms, or Plugin rooms like WooCommerce stores then the hosts are set automatically. This default setting is only applied to standalone shortcodes not created by the room manager engine.',
					'myvideoroom'
				);
				?>
			</p>
		</div>
	</div>

<!-- Role Description Marker -->
<div class="myvideoroom-feature-outer-table">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Default Permissions', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
<p><?php esc_html_e( 'WordPress Roles Available', 'myvideoroom' ); ?></p>
<p><?php esc_html_e( 'Checked Roles will be made Hosts', 'myvideoroom' ); ?></p>
			</div>
		</div>
		<div class="myvideoroom-feature-table-large table-item-output">
		<form method="post" action="">
					<div class="">
					<?php
					$index = 0;
					foreach ( $all_wp_roles as $role_name => $role_details ) {
						++ $index;
						$role         = \get_role( $role_name );
						$has_host_cap = $role->has_cap( Plugin::CAP_GLOBAL_HOST );
						?>
						<div class="table-output-permissions">
						<label for="<?php echo \esc_attr( $html_library->get_id( 'role_' . $role_name ) ); ?>">
								<?php echo \esc_html( $role_details['name'] ); ?>
						</label>

						<input class="myvideoroom-admin-table-format"
									id="<?php echo \esc_attr( $html_library->get_id( 'role_' . $role_name ) ); ?>"
									name="<?php echo \esc_attr( $html_library->get_field_name( 'role_' . $role_name ) ); ?>"
									type="checkbox" <?php echo $has_host_cap ? ' checked="checked" ' : ''; ?>" value="on" />
					</div>
						<?php
					}

					?>
					</div>

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --internally created sanitised function
			echo Factory::get_instance( HttpPost::class )->create_admin_form_submit( 'update_permissions' );
			?>
		</form>
		</div>
	</div>

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
