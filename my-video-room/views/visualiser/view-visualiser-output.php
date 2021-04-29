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

	wp_enqueue_script( 'frametab' );
	wp_enqueue_style( 'visualiser' );

	ob_start();
	?>
	<table class ="cc-table" style=" width:100% ;border: 3px solid #969696;	background: #ebedf1; padding: 12px;	margin: 5px;">

			<tr>
				<th style="width:50%" ><h3>Host View</h3>
				<th style="width:50%" ><h3>Guest View</h3></th>
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
						<strong>Shortcode</strong><br>
						<code style="user-select: all">[<?php echo esc_html( $text_shortcode_host ); ?>]</code>
					</td>
					<td>
						<strong>Shortcode</strong><br>
						<code style="user-select: all">[<?php echo esc_html( $text_shortcode_guest ); ?>]</code>
					</td>
			</tr>

			</table>

			<?php

			return ob_get_clean();
};

