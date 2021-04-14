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

	<h3>The ClubCloud WordPress Suite</h3>
	<table class="widefat fixed">
		<thead>
			<tr>
				<th>Plugin Name</th>
				<th>Installed</th>
				<th>Activated</th>
				<th>Settings</th>
			</tr>
		</thead>

		<tbody>
		<?php
			$available_clubcloud_plugins = array(
				'clubcloud-video'                       => array(
					'name'    => 'ClubCloud Video',
					'visible' => true,
				),
				'clubcloud-extras'                      => array(
					'name'    => 'ClubCloud Extras',
					'visible' => true,
				),
				'clubcloud-woocommerce-assisted-buying' => array(
					'name'    => 'ClubCloud Woocommerce Assisted Buying',
					'visible' => false,
				),
				'clubcloud-games'                       => array(
					'name'    => 'ClubCloud Games',
					'visible' => false,
				),
			);

			/**
			 * Get the plugin id from the path
			 *
			 * @param string $path The plugin path.
			 *
			 * @return string
			 */
			function get_plugin_id( string $path ) : string {
				return preg_replace( '/(-[0-9]+|)\/.*$/', '', $path );
			}

			$installed_clubcloud_plugins = array_filter(
				array_map(
					'get_plugin_id',
					array_keys( get_plugins() )
				),
				fn( $id) => strpos( $id, 'clubcloud-' ) === 0
			);

			$active_clubcloud_plugins = array_filter(
				array_map(
					'get_plugin_id',
					get_option( 'active_plugins' ),
				),
				fn( $id) => strpos( $id, 'clubcloud-' ) === 0
			);

			foreach ( $available_clubcloud_plugins as $available_clubcloud_plugin_id => $available_clubcloud_plugin_details ) {
				if ( ! $available_clubcloud_plugin_details['visible'] ) {
					continue;
				}

				?>
				<tr>
					<th scope="row"><?php echo esc_html( $available_clubcloud_plugin_details['name'] ); ?></th>
					<td>
						<?php
						if ( in_array( $available_clubcloud_plugin_id, $installed_clubcloud_plugins, true ) ) {
							echo '<span class="dashicons dashicons-yes"></span>';
						} else {
							echo '<span class="dashicons dashicons-no"></span>';
						}
						?>
					</td>
					<td>
						<?php
						if ( in_array( $available_clubcloud_plugin_id, $active_clubcloud_plugins, true ) ) {
							echo '<span class="dashicons dashicons-yes"></span>';
						} else {
							echo '<span class="dashicons dashicons-no"></span>';
						}
						?>
					</td>
					<td>
						<?php
						if ( in_array( $available_clubcloud_plugin_id, $active_clubcloud_plugins, true ) ) {
							?>
							<a href="?page=<?php echo esc_attr( $available_clubcloud_plugin_id ); ?>">
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
</div>
