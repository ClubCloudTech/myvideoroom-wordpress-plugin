<?php
/**
 * Managed $_POST requests
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class HttpPost
 */
class HttpPost {


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
			return \sanitize_text_field( \wp_unslash( $_POST[ 'myvideoroom_' . $name ] ?? '' ) ) === 'on';
		} else {
			return $default;
		}
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
		$options = $_POST[ 'myvideoroom_' . $name ] ?? array();

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
	 * Get a value from a $_POST radio field
	 *
	 * @param string $name The name of the field.
	 *
	 * @return string
	 */
	public function get_radio_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return \sanitize_text_field( \wp_unslash( $_POST[ 'myvideoroom_' . $name ] ?? '' ) );
	}

	/**
	 * Is the nonce valid
	 *
	 * @param string $action The action we were expecting to validate.
	 *
	 * @return bool
	 */
	public function is_nonce_valid( string $action ): bool {
		$nonce = $this->get_text_parameter( 'nonce' );

		return (bool) \wp_verify_nonce( $nonce, $action );
	}

	/**
	 * Get a string from the $_POST
	 *
	 * @param string $name The name of the field.
	 *
	 * @return string
	 */
	public function get_text_parameter( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing --Nonce is verified in parent function
		return \sanitize_text_field( \wp_unslash( $_POST[ 'myvideoroom_' . $name ] ?? '' ) );
	}

	/**
	 * Is the request a POST request from the admin page.
	 *
	 * @param string $action The action we were expecting to call.
	 *
	 * @return bool
	 */
	public function is_admin_post_request( string $action ): bool {
		if ( $this->is_post_request( $action ) ) {
			return (bool) \check_admin_referer( $action, 'myvideoroom_nonce' );
		}

		return false;
	}

	/**
	 * Is the request a POST?
	 * To be used on requests that are allowed to come from non-admin pages.
	 *
	 * @param string $action The action we were expecting to call.
	 *
	 * @return bool
	 */
	public function is_post_request( string $action ): bool {
		return ( 'POST' === $_SERVER['REQUEST_METHOD'] ?? false ) &&
			$this->get_text_parameter( 'action' ) === $action;
	}

	/**
	 * Add a nonce and action to a form
	 *
	 * @param string $action The action.
	 *
	 * @return string
	 */
	public function create_admin_form_submit( string $action ): string {
		$output  = \wp_nonce_field( $action, 'myvideoroom_nonce', true, false );
		$output .= '<input type="hidden" value="' . $action . '" name="myvideoroom_action" />';
		$output .= \get_submit_button();

		return $output;
	}

	/**
	 * Add a nonce and action to a form
	 *
	 * @param string $action      The action.
	 * @param string $submit_text The translated text for the submit button.
	 *
	 * @return string
	 */
	public function create_form_submit( string $action, string $submit_text ): string {
		\ob_start();
		?>

		<?php \wp_nonce_field( $action, 'myvideoroom_nonce' ); ?>
		<input type="hidden" value="<?php echo \esc_attr( $action ); ?>" name="myvideoroom_action" />

		<input type="submit"
			name="submit"
			id="submit"
			class="button button-primary"
			value="<?php echo \esc_html( $submit_text ); ?>"
		/>
		<?php

		return \ob_get_clean();
	}
}
