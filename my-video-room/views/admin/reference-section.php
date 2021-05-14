<?php
/**
 * Outputs the configuration settings a single shortcode
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\ShortcodeConstructor;
use MyVideoRoomPlugin\Reference\Shortcode;

/**
 * Render the shortcode reference page
 *
 * @param Shortcode $shortcode The shortcode reference.
 * @param string    $id        A unique id for this section.
 */
return function (
	Shortcode $shortcode,
	string $id
): string {
	ob_start();

	?>
	<article id="<?php echo esc_attr( $id ); ?>">
		<h3>
			<?php
			printf(
				/* translators: %s is the text the shortcode name */
				esc_html__(
					'%s shortcode reference',
					'myvideoroom'
				),
				esc_html( $shortcode->get_name() )
			);
			?>
		</h3>
		<p><?php echo esc_html( $shortcode->get_description() ); ?></p>
		<code class="myvideoroom-shortcode-example">
			<?php
			echo esc_html(
				Factory::get_instance( ShortcodeConstructor::class, array( $shortcode->get_shortcode_tag() ) )
				->get_shortcode_text( $shortcode->get_example_shortcode_params() )
			);
			?>
		</code>

		<?php
		if ( $shortcode->get_example_description() ) {
			echo '<p>' . esc_html( $shortcode->get_example_description() ) . '</p>';
		}
		?>

		<br />
		<table class="wp-list-table widefat plugins">
			<thead>
			<tr>
				<th class="manage-column column-name column-primary">
					<?php esc_html_e( 'Attribute', 'myvideoroom' ); ?>
				</th>
				<th class="manage-column column-name column-primary">
					<?php esc_html_e( 'Details', 'myvideoroom' ); ?>
				</th>
				<th class="manage-column column-name column-primary">
					<?php esc_html_e( 'Default', 'myvideoroom' ); ?>
				</th>
			</tr>
			</thead>

			<?php
			foreach ( $shortcode->get_sections() as $section ) {
				echo '<tbody>';

				if ( $section->get_name() ) {
					?>
					<tr class="active">
						<th class="manage-column column-name column-primary" colspan="4">
							<strong><?php echo esc_html( $section->get_name() ); ?></strong>
						</th>
					</tr>
					<?php
				}

				foreach ( $section->get_options() as $option ) {
					?>
						<tr class="inactive">
							<td class="column-primary">
								<em><?php echo esc_html( $option->get_param() ); ?></em>
							</td>

							<td class="column-description">
							<?php
							foreach ( $option->get_description() as $paragraph ) {
							    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Allow HTML from plugins
								echo '<p>' . $paragraph . '</p>';
							}
							?>
							</td>

							<td>
								<?php echo esc_html( $option->get_default() ); ?>
							</td>
						</tr>
					<?php
				}

				echo '</tbody>';
			}
			?>
		</table>
	</article>
	<?php
	return ob_get_clean();
};
