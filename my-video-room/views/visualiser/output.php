<?php
/**
 * Render the Visualiser Results page
 *
 * @package MyVideoRoomPlugin\Views\Admin
 * @param string $shortcode_host - The active shortcode to render - Host.
 * @param string $shortcode_guest - The active shortcode to render - Guest.
 * @param string $text_shortcode_host - The text version of shortcode - Host.
 * @param string $text_shortcode_guest - The text version of shortcode - Guest.
 * @param array $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

return function (
	AppShortcodeConstructor $shortcode_host,
	AppShortcodeConstructor $shortcode_guest,
	AppShortcodeConstructor $text_shortcode_host,
	AppShortcodeConstructor $text_shortcode_guest
): string {

	ob_start();
	?>
	<table class="myvideoroom-visualiser-output" data-copied-text="<?php echo esc_attr__( 'Copied!', 'myvideoroom' ); ?>">
		<thead>
			<tr>
				<th style="width:50%">
					<h3><?php echo esc_html__( 'Host View', 'myvideoroom' ); ?></h3>
				</th>

				<th style="width:50%">
					<h2><?php echo esc_html__( 'Guest View', 'myvideoroom' ); ?></h2>
				</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>
					<?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Shortcode function already sanitised by its constructor function.
						echo $shortcode_host->output_shortcode();
					?>
				</td>

				<td>
					<?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Shortcode function already sanitised by its constructor function.
						echo $shortcode_guest->output_shortcode();
					?>
				</td>
			</tr>

			<tr>
				<?php if ( $text_shortcode_host->is_admin() ) { ?>
					<td>
						<code class="myvideoroom-shortcode-example"><?php echo esc_html( $text_shortcode_host->output_shortcode( true ) ); ?></code>
						<br />
						<input class="copy-to-clipboard button-secondary" type="button" value="<?php esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>" />
					</td>

					<td>
						<code class="myvideoroom-shortcode-example"><?php echo esc_html( $text_shortcode_guest->output_shortcode( true ) ); ?></code>
						<br />
						<input class="copy-to-clipboard button-secondary" type="button" value="<?php esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>" />
					</td>
				<?php } else { ?>
					<td colspan="2">
						<code class="myvideoroom-shortcode-example"><?php echo esc_html( $text_shortcode_host->output_shortcode( true ) ); ?></code>
						<br />
						<input class="copy-to-clipboard button-secondary" type="button" value="<?php esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>" />
					</td>
				<?php } ?>
			</tr>
		</tbody>
	</table>

	<?php

	return ob_get_clean();
};
