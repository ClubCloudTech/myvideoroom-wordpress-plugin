<?php
/**
 * Outputs the configuration settings for all shortcodes
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Reference\Shortcode;

/**
 * Render the shortcode reference page
 *
 * @param Shortcode[] $shortcodes A list of shortcode
 */
return function (
	array $shortcodes = array()
): string {
	$html_lib = Factory::get_instance( HTML::class, array( 'reference' ) );

	\ob_start();

	?>
	<h2><?php \esc_html_e( 'Shortcode reference', 'myvideoroom' ); ?></h2>

	<p>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The translated text will be escaped, but we want to render the link correctly.
		\printf(
		/* translators: %s is the text "WordPress Shortcodes" and links to the WordPress help page for shortcodes */
			\esc_html__( 'You can use the following %s to add the MyVideoRoom widgets to a page.', 'myvideoroom' ),
			'<a href="https://support.wordpress.com/shortcodes/" target="_blank">' . \esc_html__( 'WordPress shortcodes', 'myvideoroom' ) . '</a>'
		);
		?>
	</p>

	<?php if ( count( $shortcodes ) > 1 ) { ?>
		<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
			<ul>
				<?php
				$active_class = ' nav-tab-active';

				foreach ( $shortcodes as $shortcode ) {
					$id = $html_lib->get_id( $shortcode->get_shortcode_tag() );

					?>
					<li>
						<a class="nav-tab<?php echo \esc_attr( $active_class ); ?>" href="#<?php echo \esc_attr( $id ); ?>">
							<?php echo \esc_html( $shortcode->get_name() ); ?>
						</a>
					</li>
					<?php

					$active_class = '';
				}
				?>
			</ul>
		</nav>
		<?php
	} else {
		echo '<hr />';
	}

	$reference_section_render = require __DIR__ . '/reference-section.php';
	foreach ( $shortcodes as $shortcode ) {
		$id = $html_lib->get_id( $shortcode->get_shortcode_tag() );

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaping is handled by the shortcode reference render function
		echo $reference_section_render( $shortcode, $id );
	}
	?>

	<?php
	return \ob_get_clean();
};
