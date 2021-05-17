<?php
/**
 * Abstract class for all shortcodes
 *
 * @package MyVideoRoomExtrasPlugin\ValueObjects
 */

declare(strict_types=1);

namespace MyVideoRoomPlugin\Core\Shortcode;

/**
 * Abstract Shortcode
 * Renders Shortcode from Extras Modules.
 */
abstract class Shortcode {

	/**
	 * Render and return a shortcode
	 *
	 * @param string $shortcode The shortcode.
	 * @param array  $params Key=>Value dictionary of params for the shortcode.
	 * @param string $text_safe - Flag to output just Shortcode info without Execution.
	 *
	 * @return string
	 */
	protected function render_shortcode( string $shortcode, array $params, string $text_safe = null ): string {
		$output = $shortcode;

		foreach ( $params as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$output .= ' ' . $key . '=true';
				} else {
					$output .= ' ' . $key . '=false';
				}
			} else {

				$output .= ' ' . $key . '="' . $value . '"';
			}
		}
		// Function Change to allow just the return of the Shortcode text rather than execution.
		if ( 'shortcode-view-only' === $text_safe ) {
			return $output;
		}

		$output = '[' . $output . ']';

		$return = \do_shortcode( $output );

		return $return;

	}
}
