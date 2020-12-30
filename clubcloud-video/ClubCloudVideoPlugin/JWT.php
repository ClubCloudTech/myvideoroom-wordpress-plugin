<?php

class ClubCloudVideoPlugin_JWT {
	private string $privateKey;

	public function __construct( string $privateKey ) {
		$this->privateKey   = $privateKey;

		add_action( 'init', function () {
			header( "Access-Control-Allow-Origin: *" );
		} );

		add_action( 'rest_api_init', [ $this, 'registerRestRoute' ] );
	}

	public function registerRestRoute() {

		// register_rest_route() handles more arguments but we are going to stick to the basics for now.
		register_rest_route( 'clubcloud', '/jwt', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => WP_REST_Server::READABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => function ( $data ) {
				$roomName            = $data->get_param( 'room' );
				$roomId              = $data->get_param( 'rid' );
				$token               = $data->get_param( 'token' );
				$videoServerEndpoint = ( parse_url( get_option( 'cc_video_server_url' ), PHP_URL_HOST ) );

				$message = json_encode( [
					'videoServerEndpoint' => $videoServerEndpoint,
					'roomName'            => $roomName,
					'admin'               => true
				] );

				if (!openssl_sign($message, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
					throw new Exception("Unable to sign data.");
				}

				if ( base64_encode($signature) !== $token ) {
					return wp_send_json_error( 'Incorrect signature', 403 );
				}

				return rest_ensure_response( [
					'jwt' => $this->createJWT( $roomId )
				] );
			},
		) );
	}

	private function createJWT( string $roomId )
	{
		$domain       = ( parse_url( get_option( 'cc_video_server_url' ), PHP_URL_HOST ) );

		$host = $_SERVER['HTTP_HOST'];
		$header             = json_encode( [ 'typ' => 'JWT', 'alg' => 'RS256', 'kid' => $host ] );
		$payload            = json_encode( [
			'iss'  => $domain,
			'iat'  => idate( 'U' ),
			'exp'  => idate( 'U' ) + 3000,
			'aud'  => $domain,
			'sub'  => $domain,
			'room' => strtolower( $roomId )
		] );
		$base64UrlHeader    = str_replace( [ '+', '/', '=' ], [
			'-',
			'_',
			''
		], base64_encode( $header ) );  // above is the setup to generate the jwt this sets the header information
		$base64UrlPayload   = str_replace( [ '+', '/', '=' ], [
			'-',
			'_',
			''
		], base64_encode( $payload ) );       // sets the payload for the video platform

		if (!openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
			throw new Exception("Unable to sign data.");
		}

		$base64UrlSignature = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $signature ) );
		$jwt                = "{$base64UrlHeader}.{$base64UrlPayload}.{$base64UrlSignature}";  // concatenates the encrypted string and returns the jwt

		return $jwt;
	}
}
