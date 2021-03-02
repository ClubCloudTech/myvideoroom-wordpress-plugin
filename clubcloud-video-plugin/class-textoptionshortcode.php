<?php
/**
 * Allows passing in more complex text options
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

/**
 * Class TextOptionShortcode
 */
class TextOptionShortcode extends Shortcode {
	const SHORTCODE_TAGS = array(
		'clubcloud_text_option',
	);

	/**
	 * Install the shortcode
	 */
	public function install() {
		foreach ( self::SHORTCODE_TAGS as $shortcode_tag ) {
			add_shortcode( $shortcode_tag, array( $this, 'output_shortcode' ) );
		}
	}

	/**
	 * Output the shortcode
	 *
	 * @return string
	 */
	public function output_shortcode(): string {
		$error = 'The clubcloud_text_option should be called from within the clubcloud_monitor shortcode';

		if (
			defined( WP_DEBUG ) &&
			WP_DEBUG &&
			defined( WP_DEBUG_LOG ) &&
			WP_DEBUG_LOG
		) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is only in debug mode
			error_log( $error );
		}

		if (
			defined( WP_DEBUG ) &&
			WP_DEBUG &&
			defined( WP_DEBUG_DISPLAY ) &&
			WP_DEBUG_DISPLAY
		) {
			return '<span style="color: red;">' . $error . '</span>';
		}

		return '';
	}

}
