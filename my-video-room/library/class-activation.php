<?php
/**
 * Activates the plugin
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Admin;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Class Activation
 */
class Activation {
	/**
	 * Attempt to activate using an activation key
	 *
	 * @return ?Notice
	 */
	public function activate(): ?Notice {
		$activation_key = get_option( Plugin::SETTING_ACTIVATION_KEY );
		delete_option( Plugin::SETTING_ACTIVATION_KEY );

		if ( ! $activation_key ) {
			return null;
		}

		$host = Factory::get_instance( Host::class )->get_host();

		if ( ! $host ) {
			return null;
		}

		$endpoints = new Endpoints();
		$url       = $endpoints->get_licence_endpoint();

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

		if ( ( $json['privateKey'] ?? false ) && ( $json['accessToken'] ?? false ) ) {
			update_option( Plugin::SETTING_PRIVATE_KEY, $json['privateKey'] );
			update_option( Plugin::SETTING_ACCESS_TOKEN, $json['accessToken'] );

			return new Notice(
				Notice::TYPE_SUCCESS,
				esc_html__( 'Activation of MyVideoRoom was successful', 'myvideoroom' ),
			);

		} else {
			return new Notice(
				Notice::TYPE_ERROR,
				esc_html__( 'Failed to activate the MyVideoRoom licence, please check your activation key and try again.', 'myvideoroom' ),
			);
		}
	}

	/**
	 * Get the current activation status
	 *
	 * @return Notice
	 */
	public function get_activation_status(): Notice {
		if (
			! get_option( Plugin::SETTING_PRIVATE_KEY ) ||
			! get_option( Plugin::SETTING_ACCESS_TOKEN )
		) {
			return new Notice(
				Notice::TYPE_WARNING,
				esc_html__(
					'MyVideoRoom has not been activated. Please enter your activation key to get started.',
					'myvideoroom'
				),
			);
		}

		$host = Factory::get_instance( Host::class )->get_host();

		if ( ! $host ) {
			return new Notice(
				Notice::TYPE_ERROR,
				esc_html__(
					'MyVideoRoom cannot determine the host name of the website, as such it cannot be activated.',
					'myvideoroom'
				),
			);
		}

		$access_token = get_option( Plugin::SETTING_ACCESS_TOKEN );

		$endpoints = new Endpoints();
		$url       = $endpoints->get_licence_endpoint() . '/' . $host;

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

		if (
			$json &&
			array_key_exists( 'maxConcurrentUsers', $json ) &&
			array_key_exists( 'maxConcurrentRooms', $json )
		) {
			if ( 0 === $json['maxConcurrentUsers'] || 0 === $json['maxConcurrentRooms'] ) {
				return new Notice(
					Notice::TYPE_WARNING,
					esc_html__( 'MyVideoRoom is currently unlicensed.', 'myvideoroom' ),
				);
			} else {

				$concurrent_strings = $this->get_concurrent_strings( $json['maxConcurrentUsers'], $json['maxConcurrentRooms'] );

				return new Notice(
					Notice::TYPE_SUCCESS,
					sprintf(
					/* translators: First %s is text representing allowed number of users, second %s refers to the allowed number of rooms */
						esc_html__( 'MyVideoRoom is currently active. Your current licence allows for %1$s and %2$s', 'myvideoroom' ),
						$concurrent_strings['maxConcurrentUsers'],
						$concurrent_strings['maxConcurrentRooms'],
					),
				);
			}
		}

		return new Notice(
			Notice::TYPE_ERROR,
			esc_html__( 'Failed to validate your MyVideoRoom licence, please try reloading this page, if this message remains please re-activate your subscription.', 'myvideroom' ),
		);
	}

	/**
	 * Convert number of users and rooms to strings
	 *
	 * @param int|null $max_concurrent_users The maximum number of concurrent users - or null for unlimited.
	 * @param int|null $max_concurrent_rooms The maximum number of concurrent rooms - or null for unlimited.
	 *
	 * @return string[]
	 */
	private function get_concurrent_strings( int $max_concurrent_users = null, int $max_concurrent_rooms = null ): array {
		if ( $max_concurrent_users ) {
			$max_concurrent_users_text = sprintf(
				esc_html(
				/* translators: %d is an number representing the number allowed current users */
					_n(
						'a maximum of %d concurrent user',
						'a maximum of %d concurrent users',
						$max_concurrent_users,
						'myvideoroom'
					)
				),
				$max_concurrent_users
			);
		} else {
			$max_concurrent_users_text = 'unlimited concurrent users';
		}

		if ( $max_concurrent_rooms ) {
			$max_concurrent_rooms_text = sprintf(
				esc_html(
				/* translators: %d is an number representing the number allowed current rooms */
					_n(
						'a maximum of %d concurrent room',
						'a maximum of %d concurrent rooms',
						$max_concurrent_rooms,
						'myvideoroom'
					)
				),
				$max_concurrent_rooms
			);
		} else {
			$max_concurrent_rooms_text = 'unlimited concurrent rooms';
		}

		return array(
			'maxConcurrentUsers' => $max_concurrent_users_text,
			'maxConcurrentRooms' => $max_concurrent_rooms_text,
		);
	}
}
