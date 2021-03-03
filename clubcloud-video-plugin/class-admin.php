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
			'ClubCloud Video',
			'ClubCloud Video',
			'manage_options',
			'clubcloud-video',
			array( $this, 'create_admin_page' ),
			'dashicons-format-chat'
		);
	}

	/**
	 * Create the admin page contents.
	 */
	public function create_admin_page() {
		$messages = array();

		$activation_key        = get_option( Plugin::SETTING_ACTIVATION_KEY );
		$private_key           = get_option( Plugin::SETTING_PRIVATE_KEY );
		$video_server_endpoint = get_option( Plugin::SETTING_VIDEO_SERVER );

		if ( $activation_key ) {
			$opts = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'Authorization: Bearer ' . $activation_key,
				),
			);

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Not required
			$host = $_SERVER['HTTP_HOST'] ?? null;
			$url  = 'https://licence.' . $video_server_endpoint . '/' . $host;

			$licence = wp_remote_get( $url, $opts );

			$private_key = null;
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
					'message' => 'ClubCloud video has been activated.',
				);
			} else {
				$messages[] = array(
					'type'    => 'notice-error',
					'message' => 'Failed to activate ClubCloud video, please check your activation key and try again.',
				);
			}
		} elseif ( $private_key ) {
			$messages[] = array(
				'type'    => 'notice-info',
				'message' => 'ClubCloud video is currently activate.',
			);
		} else {
			$messages[] = array(
				'type'    => 'notice-warning',
				'message' => 'ClubCloud video is not currently activated. Please enter your activation key to get started.',
			);
		}

		delete_option( Plugin::SETTING_ACTIVATION_KEY );

		require __DIR__ . '/partials/admin.php';
	}
}
