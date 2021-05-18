<?php
/**
 * Abstract class for all shortcodes
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Library\Host;
use MyVideoRoomPlugin\Library\Version;
use function defined;

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
		return Factory::get_instance( Version::class )->get_plugin_version();
	}

	/**
	 * Get the current host this instance is on, and strip the scheme
	 *
	 * @return string
	 */
	protected function get_host(): string {
		$host = Factory::get_instance( Host::class )->get_host();

		if ( ! $host ) {
			return '';
		}

		return $host;
	}
	/**
	 * Registers a shortcode in WordPress
	 *
	 * @param string   $tag The suffix of the tag.
	 * @param callable $callback The callback to render the shortcode.
	 */
	protected function add_shortcode( string $tag, callable $callback ) {
		$prefix = SiteDefaults::SHORTCODE_PREFIX;
		add_shortcode(
			$prefix . $tag,
			$callback
		);
	}

	/**
	 * Log and return an error message depending on debug settings
	 *
	 * @param string $message The error message to show.
	 *
	 * @return string
	 */
	protected function return_error( string $message ): string {
		if (
			\defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			\defined( 'WP_DEBUG_LOG' ) &&
			WP_DEBUG_LOG
		) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is only in debug mode
			\error_log( $message );
		}

		if (
			\defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			\defined( 'WP_DEBUG_DISPLAY' ) &&
			WP_DEBUG_DISPLAY
		) {
			return '<span style="color: red;">' . $message . '</span>';
		}

		return '';
	}
}
