<?php
/**
 * Manages getting AJAX requests
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class Ajax
 */
class Ajax {
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
		return \sanitize_text_field( \wp_unslash( $_REQUEST[ $name ] ?? $default ) );
	}
	/**
	 * Get a string from the $_POST
	 *
	 * @param string $name The name of the field.
	 *
	 * @return string
	 */
	public function get_string_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return \sanitize_text_field( \wp_unslash( $_POST[ $name ] ?? '' ) );
	}

	/**
	 * Get a integer from the $_POST
	 *
	 * @param string   $name    The name of the field.
	 * @param ?integer $default The default value.
	 *
	 * @return ?integer
	 */
	public function get_integer_parameter( string $name, int $default = null ): ?int {
		$value = $this->get_string_parameter( $name );

		if ( '' !== $value ) {
			return (int) $value;
		}

		return $default;
	}

	/**
	 * Get a value from a $_POST radio field
	 *
	 * @param string $name The name of the field.
	 *
	 * @return string
	 */
	public function get_radio_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return \sanitize_text_field( \wp_unslash( $_POST[ $name ] ?? '' ) );
	}

	/**
	 * Get an array from the $_POST
	 *
	 * @param string $name The name of the field.
	 *
	 * @return array
	 */
	public function get_string_list_parameter( string $name ): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$options = $_POST[ $name ] ?? array();

		$return = array();

		foreach ( $options as $option ) {
			$value = \trim( \sanitize_text_field( \wp_unslash( $option ) ) );
			if ( $value ) {
				$return[] = $value;
			}
		}

		return $return;
	}

	/**
	 * Get a boolean value from a $_POST checkbox
	 *
	 * @param string $name    The name of the field.
	 * @param bool   $default The default value.
	 *
	 * @return bool
	 */
	public function get_checkbox_parameter( string $name, bool $default = false ): bool {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ?? false ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
			return \sanitize_text_field( \wp_unslash( $_POST[ $name ] ?? '' ) ) === 'on';
		} else {
			return $default;
		}
	}

}
