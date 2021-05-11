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
	$reference_section_render = require __DIR__ . '/reference-section.php';
	$reference_sections       = array();

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

	<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
		<ul>
			<?php
			$active_class = ' nav-tab-active';

			foreach ( $shortcodes as $shortcode ) {
				$id                   = $html_lib->get_id( $shortcode->get_shortcode_tag() );
				$reference_sections[] = $reference_section_render( $shortcode, $id );

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
	foreach ( $reference_sections as $reference_section ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaping is handled by the shortcode reference render function
		echo $reference_section;
	}
	?>

	<?php
	return \ob_get_clean();
};
