<?php
/**
 * Abstract class for all shortcodes
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

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

		return $plugin_data['Version'] . time();
	}

	/**
	 * Get the current host this instance is on, and strip the scheme
	 *
	 * @return string
	 */
	protected function get_host(): string {
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$host = preg_replace( '#^https?://#', '', esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) ) );
		} else {
			$host = '';
		}

		return $host;
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
			defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			defined( 'WP_DEBUG_LOG' ) &&
			WP_DEBUG_LOG
		) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is only in debug mode
			error_log( $message );
		}

		if (
			defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			defined( 'WP_DEBUG_DISPLAY' ) &&
			WP_DEBUG_DISPLAY
		) {
			return '<span style="color: red;">' . $message . '</span>';
		}

		return '';
	}
}
