<?php
/**
 * Outputs the additional modules
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

/**
 * @param Plugable[] $modules
 */

use MyVideoRoomPlugin\Modules\Plugable;

return function (
	$modules
): string {

	ob_start();

	?>

	<h2><?php esc_html_e( 'Additional Modules', 'myvideoroom' ); ?></h2>
	<table class="widefat fixed">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Module Name', 'myvideoroom' ); ?></th>
				<th><?php esc_html_e( 'Installed', 'myvideoroom' ); ?></th>
				<th><?php esc_html_e( 'Status', 'myvideoroom' ); ?></th>
				<th><?php esc_html_e( 'Settings', 'myvideoroom' ); ?></th>
			</tr>
		</thead>

		<tbody>
		<?php
		foreach ( $modules as $key => $module ) {
			if ( ! $module->is_available() ) {
				continue;
			}

			?>
						<tr>
							<th scope="row"><?php echo esc_html( $module->get_name() ); ?></th>
							<td>
								<?php
								if ( $module->is_installed() ) {
									echo '<span class="dashicons dashicons-yes"></span>';
								} else {
									echo '<a class="button button-primary" href="#">Install</a>';
								}
								?>
							</td>
							<td>
								<?php
								if ( $module->is_active() ) {
									echo '<a class="button button-primary" href="#">Activate</a>';
								} else {
									echo '<a class="button button-primary negative" href="#">Deactivate</a>';
								}
								?>
							</td>
							<td>
								<a class="button button-primary"
									href="<?php menu_page_url( 'my-video-room-modules' ); ?>&module=<?php echo esc_html( $key ); ?>"
								>Settings</a>
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
