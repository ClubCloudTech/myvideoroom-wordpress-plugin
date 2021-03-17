<?php
/**
 * Abstract class for all shortcodes
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

/**
 * Abstract Shortcode
 */
abstract class Shortcode {
	/**
	 * Get the current version of the installed plugin
	 * Used for cache-busting.
	 *
	 * @return string
	 */
	protected function get_plugin_version(): string {
		$plugin_data = get_plugin_data( __DIR__ . '/index.php' );

		return $plugin_data['Version'] . '-' . time();
	}

	/**
	 * Format a text for output into a data attribute
	 *
	 * @param string|null $text The text to format.
	 *
	 * @return string|null
	 */
	protected function format_data_attribute_text( string $text = null ): ?string {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for passing data to javascript
		return $text ? base64_encode( htmlspecialchars_decode( $text ) ) : null;
	}
}
