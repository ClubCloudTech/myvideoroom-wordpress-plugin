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

return function (
	string $shortcode_host,
	string $shortcode_guest,
	string $text_shortcode_host,
	string $text_shortcode_guest
): string {

	ob_start();
	?>
	<table>
		<tr>
			<th style="width:50%">
				<h2><?php echo esc_html__( 'Host View', 'myvideoroom' ); ?></h2>
			</th>

			<th style="width:50%">
				<h2><?php echo esc_html__( 'Guest View', 'myvideoroom' ); ?></h2>
			</th>
		</tr>
		<tr>
			<td>
				<?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Shortcode function already sanitised by its constructor function.
					echo $shortcode_host;
				?>
			</td>

			<td>
				<?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Shortcode function already sanitised by its constructor function.
					echo $shortcode_guest;
				?>
			</td>
		</tr>

		<tr>
			<td>
				<strong><?php echo esc_html__( 'Shortcode', 'myvideoroom' ); ?></strong><br>
				<code style="user-select: all">[<?php echo esc_html( $text_shortcode_host ); ?>]</code>
			</td>
			<td>
				<strong><?php echo esc_html__( 'Shortcode', 'myvideoroom' ); ?></strong><br>
				<code style="user-select: all">[<?php echo esc_html( $text_shortcode_guest ); ?>]</code>
			</td>
		</tr>
	</table>

	<?php

	return ob_get_clean();
};
