<?php
/**
 * Manages $_GET requests
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class HttpGet
 */
class HttpGet {
	/**
	 * Get a string from the $_GET
	 *
	 * @param string $name    The name of the field.
	 * @param string $default The default value.
	 *
	 * @return string
	 */
	public function get_text_parameter( string $name, string $default = '' ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return \sanitize_text_field( \wp_unslash( $_GET[ $name ] ?? $default ) );
	}

	/**
	 * Get a string from the $_GET
	 *
	 * @param string   $name    The name of the field.
	 * @param ?integer $default The default value.
	 *
	 * @return ?integer
	 */
	public function get_integer_parameter( string $name, int $default = null ): ?int {
		$value = $this->get_text_parameter( $name );

		if ( $value !== '' ) {
			return (int) $value;
		}

		return $default;
	}

}
