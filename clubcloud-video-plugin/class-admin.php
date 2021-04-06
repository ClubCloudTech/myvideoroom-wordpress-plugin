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
class Admin extends Shortcode {

	/**
	 * Initialise the menu item.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		add_action(
			'admin_enqueue_scripts',
			fn() => wp_enqueue_style(
				'clubcloud-video-admin-css',
				plugins_url( '/css/admin.css', __FILE__ ),
				false,
				$this->get_plugin_version(),
			)
		);
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
		$server_endpoint = get_option( Plugin::SETTING_SERVER_DOMAIN );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Not required
		$host = $_SERVER['HTTP_HOST'] ?? null;

		if ( $activation_key ) {

			$url = 'https://licence.' . $server_endpoint;

			$opts = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $activation_key,
					'content-type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'host' => $host,
					)
				),
			);

			$licence_data = wp_remote_post( $url, $opts );

			$json    = null;
			$licence = null;

			if ( $licence_data ) {
				$licence = wp_remote_retrieve_body( $licence_data );
			}

			if ( $licence ) {
				$json = json_decode( $licence, true );
			}

			if ( $json['privateKey'] ?? false && $json['password'] ?? false ) {
				update_option( Plugin::SETTING_PRIVATE_KEY, $json['privateKey'] );
				update_option( Plugin::SETTING_ACCESS_TOKEN, $json['accessToken'] );

				if ( $json['maxConcurrentUsers'] ) {
					$max_concurrent_users = $json['maxConcurrentUsers'];
				} else {
					$max_concurrent_users = 'unlimited';
				}

				if ( $json['maxConcurrentRooms'] ) {
					$max_concurrent_rooms = $json['maxConcurrentRooms'];
				} else {
					$max_concurrent_rooms = 'unlimited';
				}

				$messages[] = array(
					'type'    => 'notice-success',
					'message' => "ClubCloud has been activated. Your current licence allows for a maximum of ${max_concurrent_users} concurrent users and ${max_concurrent_rooms} concurrent rooms",
				);

			} else {
				$messages[] = array(
					'type'    => 'notice-error',
					'message' => 'Failed to activate ClubCloud licence, please check your activation key and try again.',
				);
			}
		} elseif ( get_option( Plugin::SETTING_PRIVATE_KEY ) && get_option( Plugin::SETTING_ACCESS_TOKEN ) ) {

			$access_token = get_option( Plugin::SETTING_ACCESS_TOKEN );

			$url = 'https://licence.' . $server_endpoint . '/' . $host;

			$opts = array(
				'headers' => array(
					//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Authorization' => 'Basic ' . base64_encode( $host . ':' . $access_token ),
					'content-type'  => 'application/json',
				),
			);

			$licence_data = wp_remote_get( $url, $opts );

			$json    = null;
			$licence = null;

			if ( $licence_data ) {
				$licence = wp_remote_retrieve_body( $licence_data );
			}

			if ( $licence ) {
				$json = json_decode( $licence, true );
			}

			if ( $json ) {
				if ( null !== $json['maxConcurrentUsers'] ) {
					$max_concurrent_users = (int) $json['maxConcurrentUsers'];
				} else {
					$max_concurrent_users = 'unlimited';
				}

				if ( null !== $json['maxConcurrentRooms'] ) {
					$max_concurrent_rooms = (int) $json['maxConcurrentRooms'];
				} else {
					$max_concurrent_rooms = 'unlimited';
				}

				if ( 0 === $max_concurrent_users || 0 === $max_concurrent_rooms ) {
					$messages[] = array(
						'type'    => 'notice-warning',
						'message' => 'ClubCloud is currently unlicensed.',
					);
				} else {
					$messages[] = array(
						'type'    => 'notice-success',
						'message' => "ClubCloud is currently active. Your current licence allows for a maximum of ${max_concurrent_users} concurrent users and ${max_concurrent_rooms} concurrent rooms.",
					);
				}
			} else {
				$messages[] = array(
					'type'    => 'notice-error',
					'message' => 'Failed to validate your ClubCloud licence, please check try reloading this page, if this message remains please re-activate your subscription.',
				);
			}
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
