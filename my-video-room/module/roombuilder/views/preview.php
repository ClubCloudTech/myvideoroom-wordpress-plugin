<?php
/**
 * Render the Room Builder Results page
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

namespace MyVideoRoomPlugin\Module\RoomBuilder;

use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

/**
 * Function to render the room builder preview
 *
 * @param AppShortcodeConstructor $shortcode_host       - The active shortcode to render - Host.
 * @param AppShortcodeConstructor $shortcode_guest      - The active shortcode to render - Guest.
 * @param AppShortcodeConstructor $text_shortcode_host  - The text version of shortcode - Host.
 * @param AppShortcodeConstructor $text_shortcode_guest - The text version of shortcode - Guest.
 *
 * @return string
 */
return function (
	AppShortcodeConstructor $shortcode_host,
	AppShortcodeConstructor $shortcode_guest,
	AppShortcodeConstructor $text_shortcode_host,
	AppShortcodeConstructor $text_shortcode_guest
): string {

	\ob_start();
	?>

	<p>
		<?php
		if ( $shortcode_host->get_name() ) {
			\printf(
			/* translators: %s is the text user supplied room name */
				\esc_html__( 'Configuration for room %s created.', 'myvideoroom' ),
				\esc_html( \str_replace( '-', ' ', $shortcode_host->get_name() ) )
			);
		} else {
			\esc_html_e( 'Configuration for room created.', 'myvideoroom' );
		}
		?>
	</p>

	<table class="myvideoroom-room-builder-output"
		data-copied-text="<?php echo \esc_attr__( 'Copied!', 'myvideoroom' ); ?>">
		<thead>
		<tr>
			<th style="width:50%">
				<h3><?php echo \esc_html__( 'Host View', 'myvideoroom' ); ?></h3>
			</th>

			<th style="width:50%">
				<h2><?php echo \esc_html__( 'Guest View', 'myvideoroom' ); ?></h2>
			</th>
		</tr>
		</thead>

		<tbody>
		<tr>
			<td><?php echo \do_shortcode( $shortcode_host->output_shortcode_text() ); ?></td>
			<td><?php echo \do_shortcode( $shortcode_guest->output_shortcode_text() ); ?></td>
		</tr>

		<tr>
			<?php if ( $text_shortcode_host->is_host() ) { ?>
				<td>
					<code
						class="myvideoroom-shortcode-example"><?php echo \esc_html( $text_shortcode_host->output_shortcode_text() ); ?></code>
					<br />
					<input class="copy-to-clipboard button-secondary" type="button"
						value="<?php \esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>" />
				</td>

				<td>
					<code
						class="myvideoroom-shortcode-example"><?php echo \esc_html( $shortcode_guest->output_shortcode_text() ); ?></code>
					<br />
					<input class="copy-to-clipboard button-secondary" type="button"
						value="<?php \esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>" />
				</td>
			<?php } else { ?>
				<td colspan="2">
					<code
						class="myvideoroom-shortcode-example"><?php echo \esc_html( $text_shortcode_guest->output_shortcode_text() ); ?></code>
					<br />
					<input class="copy-to-clipboard button-secondary" type="button"
						value="<?php \esc_attr_e( 'Copy to clipboard', 'myvideoroom' ); ?>" />
				</td>
			<?php } ?>
		</tr>
		</tbody>
	</table>

	<?php

	return \ob_get_clean();
};
