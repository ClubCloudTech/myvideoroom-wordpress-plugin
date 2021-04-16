<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare(strict_types=1);
global $wp_roles;

use MyVideoRoomPlugin\Plugin;

?>

<div class="wrap">
	<h1>My Video Room Short Code Settings</h1>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="?page=myvideoroom&amp">Reference</a>
		<a class="nav-tab nav-tab-active" href="?page=myvideoroom&amp;tab=settings">Advanced Settings</a>
	</h2>

	<ul>
		<?php
		foreach ( $messages as $message ) {
			echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
		}
		?>
	</ul>

	<h3>Default Admins</h3>
	<p>
		If the "admin" flag is not passed to the shortcode then it will fall back to use this table based on the current user's role.
	</p>
	<form method="post" action="">
		<fieldset>
			<table class="form-table" role="presentation">
				<tbody>
					<?php
					$all_roles = $wp_roles->roles;

					foreach ( $all_roles as $key => $single_role ) {
						$role_object   = get_role( $key );
						$has_admin_cap = $role_object->has_cap( Plugin::CAP_GLOBAL_ADMIN );

						echo '<tr>';
						echo '<th scope="row"><label for="role_' . esc_attr( $key ) . '">' . esc_html( $single_role['name'] ) . '</label></th>';
						echo '<td><input id="role_' . esc_attr( $key ) . '" name="role_' . esc_attr( $key ) . '" type="checkbox" ' . ( $has_admin_cap ? 'checked="checked" ' : '' ) . '/></td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</fieldset>

		<?php wp_nonce_field( 'update_caps', 'nonce' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
