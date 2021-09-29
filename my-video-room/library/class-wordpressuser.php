<?php
/**
 * Wrapper for WordPress User functions
 *
 * @package MyVideoRoomExtrasPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

/**
 * Class WordPressUser
 */
class WordPressUser {

	/**
	 * Get_logged_in_wordpress_user.
	 *
	 * @return ?\WP_User
	 */
	public function get_logged_in_wordpress_user(): ?\WP_User {
		return \wp_get_current_user();
	}

	/**
	 * Get a WordPress by user by id
	 *
	 * @param ?int $user_id The id of the user.
	 *
	 * @return \WP_User|null
	 */
	public function get_wordpress_user_by_id( int $user_id = null ): ?\WP_User {
		$user = \get_user_by( 'id', $user_id );

		if ( ! $user ) {
			$user = null;
		}

		return $user;
	}

	/**
	 * Get a WordPress by user by a string identifier, could be email, login name or slug
	 *
	 * @param string $identifier The identifier of the user.
	 *
	 * @return \WP_User|null
	 */
	public function get_wordpress_user_by_identifier_string( string $identifier ): ?\WP_User {
		$user_by_email = $this->get_wordpress_user_by_email( $identifier );

		if ( $user_by_email ) {
			return $user_by_email;
		}

		$user_by_login = $this->get_wordpress_user_by_login( $identifier );

		if ( $user_by_login ) {
			return $user_by_login;
		}

		$user_by_slug = $this->get_wordpress_user_by_slug( $identifier );

		if ( $user_by_slug ) {
			return $user_by_slug;
		}

		return null;
	}

	/**
	 * Get a WordPress by user by email address
	 *
	 * @param string $email_address The email address of the user.
	 *
	 * @return \WP_User|null
	 */
	private function get_wordpress_user_by_email( string $email_address ): ?\WP_User {
		$user = \get_user_by( 'email', $email_address );

		if ( ! $user ) {
			$user = null;
		}

		return $user;
	}

	// ---

	/**
	 * Get a WordPress by user by login name
	 *
	 * @param string $login_name The login name of the user.
	 *
	 * @return \WP_User|null
	 */
	private function get_wordpress_user_by_login( string $login_name ): ?\WP_User {
		$user = \get_user_by( 'login', $login_name );

		if ( ! $user ) {
			$user = null;
		}

		return $user;
	}

	/**
	 * Get a WordPress by user by slug
	 *
	 * @param string $slug The slug of the user.
	 *
	 * @return \WP_User|null
	 */
	public function get_wordpress_user_by_slug( string $slug ): ?\WP_User {
		$user = \get_user_by( 'slug', $slug );

		if ( ! $user ) {
			$user = null;
		}

		return $user;
	}
}
