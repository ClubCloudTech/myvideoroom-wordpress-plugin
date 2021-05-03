<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Plugin;

/**
 * Render the admin page
 *
 * @param string $video_server The host of the video server
 * @param array $messages An list of messages to show. Takes the form [type=:string, message=:message][]
 */
return function (
	string $video_server,
	$available_myvideoroom_plugins,
	$installed_myvideoroom_plugins,
	$active_myvideoroom_plugins
): string {

	ob_start();

	?>

	<h2><?php esc_html_e( 'Activation Settings', 'myvideoroom' ); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<fieldset>
			<table class="form-table" role="presentation">
				<tbody>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>">
							<?php esc_html_e( 'My Video Room Activation Key', 'myvideoroom' ); ?>
						</label>
					</th>
					<td>
						<input
								type="text"
								name="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
								value="<?php echo esc_attr( get_option( Plugin::SETTING_ACTIVATION_KEY ) ); ?>"
								placeholder="
								<?php
								if ( get_option( Plugin::SETTING_PRIVATE_KEY ) ) {
									esc_html_e( '(hidden)', 'myvideoroom' );
								} else {
									esc_html_e( '(Provided by ClubCloud)', 'myvideoroom' );
								}
								?>
								"
								id="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
								size="100"
						/>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>">
							<?php esc_html_e( 'ClubCloud Server Domain', 'myvideoroom' ); ?><br />
							<em><?php esc_html_e( 'for advanced usage only', 'myvideoroom' ); ?></em>
						</label>
					</th>
					<td>
						<input
								type="text"
								name="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>"
								value="<?php echo esc_attr( $video_server ); ?>"
								id="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>"
								size="100"
						/>
					</td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php submit_button(); ?>
	</form>

	<h2><?php esc_html_e( 'The MyVideoRoom WordPress Suite', 'myvideoroom' ); ?></h2>
	<table class="widefat fixed">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Plugin Name', 'myvideoroom' ); ?></th>
				<th><?php esc_html_e( 'Installed', 'myvideoroom' ); ?></th>
				<th><?php esc_html_e( 'Activated', 'myvideoroom' ); ?></th>
				<th><?php esc_html_e( 'Settings', 'myvideoroom' ); ?></th>
			</tr>
		</thead>

		<tbody>
		<?php

		foreach ( $available_myvideoroom_plugins as $available_myvideoroom_plugin_id => $available_myvideoroom_plugin_details ) {
			if ( ! $available_myvideoroom_plugin_details['visible'] ) {
				continue;
			}

			?>
				<tr>
					<th scope="row"><?php echo esc_html( $available_myvideoroom_plugin_details['name'] ); ?></th>
					<td>
					<?php
					if ( in_array( $available_myvideoroom_plugin_id, str_replace( 'myvideoroom-extras', 'my-video-room-extras', $installed_myvideoroom_plugins ), true ) ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no"></span>';
					}
					?>
					</td>
					<td>
					<?php
					if ( in_array( $available_myvideoroom_plugin_id, str_replace( 'myvideoroom-extras', 'my-video-room-extras', $active_myvideoroom_plugins ), true ) ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no"></span>';
					}
					?>
					</td>
					<td>
					<?php
					if ( in_array( $available_myvideoroom_plugin_id, str_replace( 'myvideoroom-extras', 'my-video-room-extras', $active_myvideoroom_plugins ), true ) ) {
						?>
							<a href="?page=<?php echo esc_attr( $available_myvideoroom_plugin_id ); ?>">
								<span class="dashicons dashicons-admin-generic"></span>
							</a>
							<?php
					}
					?>
					</td>
				</tr>
				<?php
		}
		?>
		</tbody>
	</table>

	<?php
	return ob_get_clean();
};
