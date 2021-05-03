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
	<table style="width: 100%">
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
                <th><?php echo esc_html__( 'Host shortcode', 'myvideoroom' ); ?></th>
                <th><?php echo esc_html__( 'Guest shortcode', 'myvideoroom' ); ?></th>
            </tr>

            <tr>
                <td><code>[<?php echo esc_html( $text_shortcode_host ); ?>]</code></td>
                <td><code>[<?php echo esc_html( $text_shortcode_guest ); ?>]</code></td>
            </tr>
        </tbody>
	</table>

	<?php

	return ob_get_clean();
};
