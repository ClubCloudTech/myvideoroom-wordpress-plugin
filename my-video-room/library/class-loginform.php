<?php
/**
 * Gets details about a room
 *
 * @package MyVideoRoomPlugin\Library\LoginForm.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Factory;

/**
 * Class LoginForm
 */
class LoginForm {
	const SETTING_LOGIN_DISPLAY   = 'myvideoroom-login-display';
	const SETTING_LOGIN_OVERRIDE  = 'myvideoroom-login-override';
	const SETTING_LOGIN_SHORTCODE = 'myvideoroom-login-shortcode';
	const SETTING_LOGIN_IFRAME    = 'myvideoroom-login-iframe';

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
		$login_display   = get_option( self::SETTING_LOGIN_DISPLAY );
		$login_override  = get_option( self::SETTING_LOGIN_OVERRIDE );
		$login_shortcode = get_option( self::SETTING_LOGIN_SHORTCODE );
		$login_iframe    = get_option( self::SETTING_LOGIN_IFRAME );
		return ( require __DIR__ . '/../views/admin/view-settings-login.php' )( self::$id_index ++, \boolval( $login_display ), \boolval( $login_override ), \boolval( $login_iframe ), $login_shortcode );
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
			$login_display   = $http_post_library->get_checkbox_parameter( self::SETTING_LOGIN_DISPLAY );
			$login_override  = $http_post_library->get_checkbox_parameter( self::SETTING_LOGIN_OVERRIDE );
			$login_shortcode = $http_post_library->get_string_parameter( self::SETTING_LOGIN_SHORTCODE );
			$login_iframe    = $http_post_library->get_string_parameter( self::SETTING_LOGIN_IFRAME );

			if ( $login_shortcode ) {
				\update_option( self::SETTING_LOGIN_SHORTCODE, $login_shortcode );
			}
				\update_option( self::SETTING_LOGIN_OVERRIDE, $login_override );
				\update_option( self::SETTING_LOGIN_DISPLAY, $login_display );
				\update_option( self::SETTING_LOGIN_IFRAME, $login_iframe );
		}
	}
}
