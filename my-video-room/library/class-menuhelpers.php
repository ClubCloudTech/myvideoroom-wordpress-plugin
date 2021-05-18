<?php
/**
 * Shortcodes for menus
 *
 * @package MyVideoRoomExtrasPlugin\Core
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\WordPressUser as LibraryWordPressUser;
use MyVideoRoomPlugin\Shortcode;

/**
 * Class MenuHelpers
 * Provides Supporting Functions around Menus and frames.
 */
class MenuHelpers extends Shortcode {

	/**
	 * Provide Runtime
	 */
	public function init() {
		return null;
	}


	/**
	 * A Function to Return the User Nicename for menus
	 *
	 * @param integer $user_id - user ID.
	 *
	 * @return string
	 */
	public function nice_name( int $user_id = null ): string {

		if ( $user_id ) {
			$user = Factory::get_instance( LibraryWordPressUser::class )->get_wordpress_user_by_id( $user_id );
		} else {
			$user = \wp_get_current_user();
		}

		return $user->user_nicename;
	}

}