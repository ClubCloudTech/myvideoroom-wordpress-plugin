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
	string $video_server
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

	<?php
	return ob_get_clean();
};
