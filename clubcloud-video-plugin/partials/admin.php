<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package ClubCloudVideoPlugin\Admin
 */

declare(strict_types=1);

use ClubCloudVideoPlugin\Plugin;

if ( esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) ) ) {
	$video_server = esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) );
} else {
	$video_server = 'clubcloud.tech';
}

?>

<div class="wrap">
	<h1>ClubCloud Settings</h1>

	<ul>
	<?php
	foreach ( $messages as $message ) {
		echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
	}
	?>
	</ul>

	<h2>Settings</h2>
	<form method="post" action="options.php">
		<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<fieldset>
			<table class="form-table" role="presentation">
				<tbody>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>">
							ClubCloud Server Domain
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

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>">
							ClubCloud Activation Key
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
									echo '(hidden)';
								} else {
									echo '(Provided by ClubCloud)'; }
								?>
								"
								id="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
								size="100"
						/>
					</td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php submit_button(); ?>
	</form>
</div>
