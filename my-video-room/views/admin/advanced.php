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
 */
return function (
	string $video_server,
	int $id_index = 0
): string {

	ob_start();

	?>

	<h2><?php esc_html_e( 'Advanced Settings', 'myvideoroom' ); ?></h2>

	<form method="post">
		<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<fieldset>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN . '_' . $id_index ); ?>">
								<?php esc_html_e( 'ClubCloud Server Domain', 'myvideoroom' ); ?>
							</label>
						</th>

						<td>
							<input
									type="text"
									name="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>"
									value="<?php echo esc_attr( $video_server ); ?>"
									id="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN . '_' . $id_index ); ?>"
							/>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="delete_activation_<?php echo esc_attr( $id_index ); ?>">
								<?php esc_html_e( 'Delete activation settings', 'myvideoroom' ); ?>
							</label>
						</th>

						<td>
							<input
									type="checkbox"
									name="delete_activation"
									value="on"
									id="delete_activation_<?php echo esc_attr( $id_index ); ?>"
							/>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<?php wp_nonce_field( 'update_settings', 'myvideoroom_advanced_settings_nonce' ); ?>
		<?php submit_button(); ?>
	</form>

	<?php
	return ob_get_clean();
};
