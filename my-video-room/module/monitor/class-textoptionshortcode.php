<?php
/**
 * Allows passing in more complex text options
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Monitor;

use MyVideoRoomPlugin\AppShortcode;
use MyVideoRoomPlugin\Shortcode;

/**
 * Class TextOptionShortcode
 */
class TextOptionShortcode extends Shortcode {
	const SHORTCODE_TAG = AppShortcode::SHORTCODE_TAG . '_text_option';

	/**
	 * Install the shortcode
	 */
	public function init() {
		\add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );
	}

	/**
	 * Output the shortcode
	 *
	 * @return string
	 */
	public function output_shortcode(): string {
		$message = \sprintf(
		/* translators: First %s is it ths shortcode tag for the text-option and the second for the monitor tag */
			\esc_html__( 'The %1$s should be called from within the %2$s shortcode', 'myvideoroom' ),
			self::SHORTCODE_TAG,
			Module::SHORTCODE_TAG
		);

		return $this->return_error( $message );
	}

}
