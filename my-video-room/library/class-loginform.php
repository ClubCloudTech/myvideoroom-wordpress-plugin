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


	/**
	 * Render Default Settings Admin Page.
	 */
	public function render_shortcode_login_page() {

		$render = include __DIR__ . '/../views/view-login-form.php';
		return $render();

	}



}
