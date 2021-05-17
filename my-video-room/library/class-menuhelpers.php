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

	/**
	 * Remove_admin_bar - removes Admin Bar from WordPress for iframe.
	 *
	 * @return void
	 */
	public function remove_admin_bar() {
		wp_enqueue_style( 'mvr-template' );
		wp_enqueue_style( 'mvr-menutab-header' );
		show_admin_bar( false );
		add_filter( 'show_admin_bar', '__return_false' );
		add_action( 'get_header', array( $this, 'remove_admin_bar_action' ) );

	}

	/**
	 * Remove_admin_bar_action Function.
	 *
	 * @return void
	 */
	public function remove_admin_bar_action() {
		\show_admin_bar( false );
		remove_action( 'wp_head', '_admin_bar_bump_cb' );
	}

}

