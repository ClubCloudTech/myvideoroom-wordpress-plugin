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
		global $admin_page_hooks;

		if ( empty( $admin_page_hooks['clubcloud-settings'] ) ) {
			add_menu_page(
				'ClubCloud Settings',
				'ClubCloud Settings',
				'manage_options',
				'clubcloud-settings',
				array( $this, 'create_admin_page' ),
				'dashicons-format-chat'
			);

			add_submenu_page(
				'clubcloud-settings',
				'ClubCloud Settings',
				'General Settings',
				'manage_options',
				'clubcloud-settings',
				array( $this, 'create_admin_page' )
			);
		}

		add_submenu_page(
			'clubcloud-settings',
			'Video Reference',
			'Video Reference',
			'manage_options',
			'clubcloud-video',
			array( $this, 'create_video_admin_page' )
		);
	}

	/**
	 * Create the admin page contents.
	 */
	public function create_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$messages = array();

		$activation_key  = get_option( Plugin::SETTING_ACTIVATION_KEY );
		$private_key     = get_option( Plugin::SETTING_PRIVATE_KEY );
		$server_endpoint = get_option( Plugin::SETTING_SERVER_DOMAIN );

		if ( $activation_key ) {
			$opts = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $activation_key,
				),
			);

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Not required
			$host = $_SERVER['HTTP_HOST'] ?? null;
			$url  = 'https://licence.' . $server_endpoint . '/' . $host;

			$licence_data = wp_remote_get( $url, $opts );

			$private_key = null;
			$licence     = null;

			if ( $licence_data ) {
				$licence = wp_remote_retrieve_body( $licence_data );
			}

			if ( $licence ) {
				$json = json_decode( $licence, true );

				if ( $json && $json['privateKey'] ) {
					$private_key = $json['privateKey'];
				}
			}

			if ( $private_key ) {
				update_option( Plugin::SETTING_PRIVATE_KEY, $private_key );

				$messages[] = array(
					'type'    => 'notice-success',
					'message' => 'ClubCloud has been activated.',
				);
			} else {
				$messages[] = array(
					'type'    => 'notice-error',
					'message' => 'Failed to activate ClubCloud licence, please check your activation key and try again.',
				);
			}
		} elseif ( $private_key ) {
			$messages[] = array(
				'type'    => 'notice-info',
				'message' => 'ClubCloud is currently active.',
			);
		} else {
			$messages[] = array(
				'type'    => 'notice-warning',
				'message' => 'ClubCloud is not currently activated. Please enter your activation key to get started.',
			);
		}

		delete_option( Plugin::SETTING_ACTIVATION_KEY );

		require __DIR__ . '/partials/admin.php';
	}

	/**
	 * Create the admin page contents.
	 */
	public function create_video_admin_page() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- Not required
		$tab = $_GET['tab'] ?? null;

		switch ( $tab ) {
			case 'settings':
				$this->create_settings_admin_page();
				break;
			default:
				require __DIR__ . '/partials/admin-reference.php';
		}
	}

	/**
	 * Create the settings page for the video
	 */
	private function create_settings_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$messages = array();

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'update_caps', 'nonce' );

			global $wp_roles;
			$all_roles = $wp_roles->roles;
			foreach ( $all_roles as $key => $single_role ) {
				$role_object = get_role( $key );

				if ( isset( $_POST[ 'role_' . $key ] ) ) {
					$role_object->add_cap( Plugin::CAP_GLOBAL_ADMIN );
				} else {
					$role_object->remove_cap( Plugin::CAP_GLOBAL_ADMIN );
				}
			}

			$messages[] = array(
				'type'    => 'notice-success',
				'message' => 'Roles updated.',
			);
		}

		require __DIR__ . '/partials/admin-settings.php';
	}
}
