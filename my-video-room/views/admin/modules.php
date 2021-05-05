<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\Module\Plugable;

/**
 * Render the admin page
 *
 * @param \MyVideoRoomPlugin\Module\Module[]  $modules
 * @param string[] $activated_module_keys
 */
return function (
	array $modules = array(),
	array $activated_module_keys = array()
): string {

	ob_start();

	?>

	<h2><?php esc_html_e( 'Additional Modules', 'myvideoroom' ); ?></h2>

	<p>
		<?php
		esc_html_e(
			'Connect MyVideoRoom into your existing plugins, allowing for more complex use cases.',
			'myvideoroom'
		);
		?>
	</p>

	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Module', 'myvideoroom' ); ?></th>
				<th scope="col" class="manage-column column-description"><?php esc_html_e( 'Description', 'myvideoroom' ); ?></th>
			</tr>
		</thead>

		<tbody id="the-list">
		<?php
		foreach ( $modules as $key => $module ) {

			// @TODO add nonces to activate/deactivate;

			if ( in_array( $key, $activated_module_keys, true ) ) {
				$is_active = true;
				$row_class = 'active';
			} else {
				$is_active = false;
				$row_class = 'inactive';
			}

			?>
			<tr class="<?php echo esc_attr( $row_class ); ?>">
				<td class="plugin-title column-primary">
					<strong><?php echo esc_html( $module->get_name() ); ?></strong>

					<div class="row-actions visible">
						<?php if ( $module->is_published() && $module->get_instance()->is_compatible() ) { ?>
							<?php if ( $is_active ) { ?>
							<span class="deactivate">
								<a class="delete" href="<?php menu_page_url( 'my-video-room-modules' ); ?>&module=<?php echo esc_html( $key ); ?>&action=deactivate"
									aria-label="<?php printf( /* translators: %s is the name of the module */ esc_html__( 'Deactivate %s ', 'myvideoroom' ), esc_html( $module->get_name() ) ); ?>"
								>
									Deactivate
								</a>
							</span>
						<?php } else { ?>
							<span class="activate">
								<a href="<?php menu_page_url( 'my-video-room-modules' ); ?>&module=<?php echo esc_html( $key ); ?>&action=activate"
									aria-label="<?php printf( /* translators: %s is the name of the module */ esc_html__( 'Activate %s ', 'myvideoroom' ), esc_html( $module->get_name() ) ); ?>"
								>
									Activate
								</a>
							</span>
						<?php } ?>
						|
						<span class="settings">
							<a href="<?php menu_page_url( 'my-video-room-modules' ); ?>&module=<?php echo esc_html( $key ); ?>"
								aria-label="<?php printf( /* translators: %s is the name of the module */ esc_html__( 'Settings for %s ', 'myvideoroom' ), esc_html( $module->get_name() ) ); ?>"
							>
								Settings
							</a>
						</span>

						<?php } elseif ( ! $module->is_published() ) { ?>
							<em><?php esc_html_e( '(coming soon)', 'myvideoroom' ); ?></em>
						<?php } else { ?>
							<em><?php esc_html_e( '(not compatible)', 'myvideoroom' ); ?></em>
						<?php } ?>
					</div>
				</td>

				<td class="column-description desc">
					<div class="plugin-description"><p><?php echo esc_html( $module->get_description() ); ?></p></div>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Module', 'myvideoroom' ); ?></th>
				<th scope="col" class="manage-column column-description"><?php esc_html_e( 'Description', 'myvideoroom' ); ?></th>
			</tr>
		</tfoot>
	</table>

	<?php
	return ob_get_clean();
};
