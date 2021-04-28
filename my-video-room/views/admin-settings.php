<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare(strict_types=1);
global $wp_roles;

use MyVideoRoomPlugin\Plugin;

/**
 * Render the admin page
 *
 * @param array $messages     A list of messages to show. Takes the form [type=:string, message=:message][]
 * @param array $all_wp_roles A list of WordPress roles. @see global $wp_roles->roles.
 */
return function (
	array $messages = array(),
	array $all_wp_roles = array()
): string {
	ob_start();
	?>

	<div class="wrap">
		<h1><?php esc_html_e( 'My Video Room Short Code Settings', 'myvideoroom' ); ?></h1>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab" href="?page=my-video-room&amp">Reference</a>
			<a class="nav-tab nav-tab-active" href="?page=my-video-room&amp;tab=settings">Advanced Settings</a>
		</h2>

		<ul>
			<?php
			foreach ( $messages as $message ) {
				echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
			}
			?>
		</ul>

		<h3><?php esc_html_e( 'Default Admins', 'myvideoroom' ); ?></h3>
		<p>
			<?php esc_html_e( 'If the "admin" flag is not passed to the shortcode then it will fall back to use this table based on the current user\'s role.', 'myvideoroom' ); ?>
		</p>
		<form method="post" action="">
			<fieldset>
				<table class="form-table" role="presentation">
					<tbody>
						<?php
						foreach ( $all_wp_roles as $key => $single_role ) {
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

	<?php
	return ob_get_clean();
};

