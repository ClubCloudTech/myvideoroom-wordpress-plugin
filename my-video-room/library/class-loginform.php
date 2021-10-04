<?php
/**
 * Gets details about a room
 *
 * @package MyVideoRoomPlugin\Library\LoginForm.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;


/**
 * Class LoginForm
 */
class LoginForm {

	/**
	 * An increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Get Login Settings Admin page
	 *
	 * @return string
	 */
	public function get_login_settings_page(): string {
		$this->check_for_update_request();
		$login_override  = get_option( 'myvideoroom-login-override' );
		$login_shortcode = get_option( 'myvideoroom-login-shortcode' );
		return ( require __DIR__ . '/../views/admin/view-settings-login.php' )( self::$id_index ++, \boolval( $login_override ), $login_shortcode );
	}

	/**
	 * Check for updating the Login Settings
	 *
	 * * @return void
	 */
	private function check_for_update_request(): void {
		$http_post_library = Factory::get_instance( HttpPost::class );

		if ( $http_post_library->is_post_request( 'login_setting' ) ) {
			if ( ! $http_post_library->is_nonce_valid( 'login_setting' ) ) {
				esc_html_e( 'Error, Security NONCE mismatch', 'myvideoroom' );
			}

			$login_override  = $http_post_library->get_checkbox_parameter( 'login_override' );
			$login_shortcode = $http_post_library->get_string_parameter( 'login_shortcode' );

			if ( $login_shortcode ) {

				\update_option( 'myvideoroom-login-shortcode', $login_shortcode );
			}
				\update_option( 'myvideoroom-login-override', $login_override );

		}
	}
}
