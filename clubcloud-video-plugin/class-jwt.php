<?php
/**
 * Creates JSON Web Tokens for authentication with Jitsi.
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

use Exception;

/**
 * Class JWT
 */
class JWT {

	/**
	 * The private key to authorise this install.
	 *
	 * @var string
	 */
	private string $private_key;

	/**
	 * JWT constructor.
	 *
	 * @param string $private_key The private key to authorise this install.
	 */
	public function __construct( string $private_key ) {
		$this->private_key = $private_key;

	}

	/**
	 * Initialise this
	 */
	public function init() {
		add_action(
			'init',
			function () {
				header( 'Access-Control-Allow-Origin: *' );
			}
		);

		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
	}

	/**
	 * Register the rest route with WordPress
	 */
	public function register_rest_route() {
		register_rest_route(
			'clubcloud',
			'/jwt',
			array(
				// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
				'methods'             => \WP_REST_Server::READABLE,
				// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
				'callback'            => function ( $data ) {
					$room_name            = $data->get_param( 'room' );
					$room_id              = $data->get_param( 'rid' );
					$enable_floorplan     = ! ! $data->get_param( 'fp' );
					$token               = $data->get_param( 'token' );
					$video_server_endpoint = 'meet.' . get_option( Plugin::SETTING_SERVER_DOMAIN );

					$message = wp_json_encode(
						array(
							'videoServerEndpoint' => $video_server_endpoint,
							'roomName'            => $room_name,
							'admin'               => true,
							'enableFloorplan'     => $enable_floorplan,
						)
					);

					if ( ! openssl_sign( $message, $signature, $this->private_key, OPENSSL_ALGO_SHA256 ) ) {
						throw new Exception( 'Unable to sign data.' );
					}

					if ( $this->base_64_encode( $signature ) !== $token ) {
						\wp_send_json_error( 'Incorrect signature', 403 );
						return false;
					}

					return rest_ensure_response(
						array(
							'jwt' => $this->create_jwt( $room_id ),
						)
					);
				},
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Create the JWT token
	 *
	 * @param string $room_id The id of the room.
	 *
	 * @return string
	 * @throws Exception When unable to sign.
	 */
	private function create_jwt( string $room_id ): string {
		$domain = 'meet.' . get_option( Plugin::SETTING_SERVER_DOMAIN );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validation not required.
		$host   = $_SERVER['HTTP_HOST'] ?? null;
		$header = wp_json_encode(
			array(
				'typ' => 'JWT',
				'alg' => 'RS256',
				'kid' => $host,
			)
		);

		$payload = wp_json_encode(
			array(
				'iss'  => $domain,
				'iat'  => idate( 'U' ),
				'exp'  => idate( 'U' ) + 3000,
				'aud'  => $domain,
				'sub'  => $domain,
				'room' => strtolower( $room_id ),
			)
		);

		$base_64_url_header = str_replace(
			array( '+', '/', '=' ),
			array(
				'-',
				'_',
				'',
			),
			$this->base_64_encode( $header )
		);

		$base_64_url_body = str_replace(
			array( '+', '/', '=' ),
			array(
				'-',
				'_',
				'',
			),
			$this->base_64_encode( $payload )
		);

		if ( ! openssl_sign( $base_64_url_header . '.' . $base_64_url_body, $signature, $this->private_key, OPENSSL_ALGO_SHA256 ) ) {
			throw new Exception( 'Unable to sign data.' );
		}

		$base_64_url_signature = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), $this->base_64_encode( $signature ) );

		// concatenates the encrypted string and returns the jwt.
		return "{$base_64_url_header}.{$base_64_url_body}.{$base_64_url_signature}";
	}

	/**
	 * Base 64 Encode a value
	 *
	 * @param string $value The value to be encoded.
	 *
	 * @return string
	 */
	private function base_64_encode( string $value ) : string {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- JWT rely on base64 encoding
		return base64_encode( $value );
	}
}
