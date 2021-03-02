<?php
/**
 * Manages the configuration settings for the video plugin
 *
 * @package ClubCloudVideoPlugin\Admin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

/**
 * Class Admin
 */
class Admin {

	/**
	 * Initialise the menu item.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add the admin menu page.
	 */
	public function add_admin_menu() {
		add_menu_page(
			'ClubCloud Video Settings',
			'ClubCloud Video Settings',
			'manage_options',
			'clubcloud-video-settings',
			array( $this, 'create_admin_page' ),
			'dashicons-format-chat'
		);
	}

	/**
	 * Create the admin page contents.
	 */
	public function create_admin_page() {
		require __DIR__ . '/partials/admin.php';
	}
}
