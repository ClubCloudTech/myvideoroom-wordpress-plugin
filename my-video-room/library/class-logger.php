<?php
/**
 * Log Error Details.
 *
 * @package MyVideoRoomPlugin\Library\Logger
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class Logger
 */
abstract class Logger {

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
