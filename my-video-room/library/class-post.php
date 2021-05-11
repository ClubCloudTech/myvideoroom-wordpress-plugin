<?php
/**
 * Managed $_POST requests
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class Post
 */
class Post {


	/**
	 * Get a string from the $_POST
	 *
	 * @param string $name The name of the field.
	 *
	 * @return string
	 */
	public function get_text_post_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return sanitize_text_field( wp_unslash( $_POST[ 'myvideoroom_room_builder_' . $name ] ?? '' ) );
	}

	/**
	 * Get a boolean value from a $_POST checkbox
	 *
	 * @param string $name    The name of the field.
	 * @param bool   $default The default value.
	 *
	 * @return bool
	 */
	public function get_checkbox_post_parameter( string $name, bool $default = false ): bool {
		if ( $this->is_post_request() ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
			return sanitize_text_field( wp_unslash( $_POST[ 'myvideoroom_room_builder_' . $name ] ?? '' ) ) === 'on';
		} else {
			return $default;
		}
	}

	/**
	 * Get a string from the $_POST
	 *
	 * @param string $name The name of the field.
	 *
	 * @return array
	 */
	public function get_multi_post_parameter( string $name ): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$options = $_POST[ 'myvideoroom_room_builder_' . $name ] ?? array();

		$return = array();

		foreach ( $options as $option ) {
			$value = trim( sanitize_text_field( wp_unslash( $option ) ) );
			if ( $value ) {
				$return[] = $value;
			}
		}

		return $return;
	}

	/**
	 * Get a value from a $_POST radio field
	 *
	 * @param string $name    The name of the field.
	 *
	 * @return string
	 */
	public function get_radio_post_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return sanitize_text_field( wp_unslash( $_POST[ 'myvideoroom_room_builder_' . $name ] ?? '' ) );
	}

	/**
	 * Is the request a POST
	 *
	 * @return bool
	 */
	public function is_post_request(): bool {
		return ( 'POST' === $_SERVER['REQUEST_METHOD'] ?? false );
	}
}
