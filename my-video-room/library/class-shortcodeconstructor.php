<?php
/**
 * Represents a shortcode
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class Shortcode Constructor
 */
class ShortcodeConstructor {
	/**
	 * The shortcode tag
	 *
	 * @var string
	 */
	private string $shortcode_tag;

	/**
	 * ShortcodeConstructor constructor.
	 *
	 * @param string $shortcode_tag The shortcode tag.
	 */
	public function __construct( string $shortcode_tag ) {
		$this->shortcode_tag = $shortcode_tag;
	}

	/**
	 * Output the shortcode
	 *
	 * @param array $attributes A list of attributes.
	 *
	 * @return string
	 */
	public function get_shortcode_text( array $attributes ): string {
		$output = $this->shortcode_tag;

		foreach ( $attributes as $key => $value ) {
			if ( \is_bool( $value ) ) {
				if ( $value ) {
					$output .= ' ' . $key . '=true';
				} else {
					$output .= ' ' . $key . '=false';
				}
			} else {
				$output .= ' ' . $key . '="' . $value . '"';
			}
		}

		return '[' . $output . ']';
	}
}
