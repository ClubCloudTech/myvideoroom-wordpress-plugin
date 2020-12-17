<?php

/**
 * Plugin Name:     ClubCloud Video
 * Plugin URI:      https://clubcloud.tech
 * Description:     Allows embedding of the ClubCloud Video App into Wordpress
 * Version:         0.2-alpha
 * Requires         PHP: 7.4
 * Author:          Alec Sammon, Craig Jones
 * Author URI:      https://clubcloud.tech/
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'ClubCloudVideo_Plugin' ) ) {

	class ClubCloudVideoPlugin {
		public const PLUGIN_NAMESPACE = 'cc';

		public const SETTING_VIDEO_SERVER_URL = self::PLUGIN_NAMESPACE . '_video_server_url';
		public const SETTING_ROOM_SERVER_URL = self::PLUGIN_NAMESPACE . '_room_server_url';
		public const SETTING_APP_SERVER_URL = self::PLUGIN_NAMESPACE . '_app_url';

		public const SETTING_WEB_TOKEN_KEY = self::PLUGIN_NAMESPACE . '_web_token_key';
		public const SETTING_SHARED_SECRET = self::PLUGIN_NAMESPACE . 'clubcloud_shared_secret';

		public const SETTINGS = [
			self::SETTING_VIDEO_SERVER_URL,
			self::SETTING_ROOM_SERVER_URL,
			self::SETTING_APP_SERVER_URL,
			self::SETTING_WEB_TOKEN_KEY,
			self::SETTING_SHARED_SECRET,

		];

		public static function init() {
			return new self();
		}

		// ---

		public function __construct() {
			$webTokenKey  = getenv( 'CLUBCLOUD_WEB_TOKEN_KEY' ) ?: get_option( self::SETTING_WEB_TOKEN_KEY );
			$sharedSecret = getenv( 'CLUBCLOUD_SHARED_SECRET' ) ?: get_option( self::SETTING_SHARED_SECRET );

			add_action( 'admin_init', [ $this, 'registerSettings' ] );

			new ClubCloudVideoPlugin_Admin();
			new ClubCloudVideoPlugin_Shortcode( $sharedSecret );
			new ClubCloudVideoPlugin_JWT( $sharedSecret, $webTokenKey );
		}

		public function registerSettings() {
			foreach ( self::SETTINGS as $setting ) {
				register_setting( self::PLUGIN_NAMESPACE, $setting );
			}
		}

	}

	add_action( 'plugins_loaded', [ 'ClubCloudVideoPlugin', 'init' ] );

	// ---

	class ClubCloudVideoPlugin_Admin {
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'addAdminMenu' ] );
		}

		public function addAdminMenu() {
			add_menu_page(
				'ClubCloud Video Settings',
				'ClubCloud Video Settings',
				'manage_options',
				'clubcloud-video-settings',
				[ $this, 'createAdminPage' ],
				'dashicons-format-chat'
			);
		}

		public function createAdminPage() {
			require( __DIR__ . '/admin/page.php' );
		}
	}

	class ClubCloudVideoPlugin_Shortcode {
		const SHORTCODE_TAG = 'clubvideo';

		private string $sharedSecret;

		public function __construct( string $sharedSecret ) {
			$this->sharedSecret = $sharedSecret;

			add_shortcode( self::SHORTCODE_TAG, [ $this, 'createShortcode' ] );

			add_filter( 'query_vars', fn( $queryVars ) => array_merge( $queryVars, [ 'dev' ] ) );
			add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script( 'jquery' ) );
		}

		public function createShortcode( array $params = [] ) {
			$devMode = ( get_query_var( 'dev', false ) === 'true' );

			$roomName    = $params['name'] ?: $params['cc_event_id'];
			$mapId       = $params['map'] ?: $params['cc_plan_id'];
			$enableLobby = ! ! ( $params['lobby'] ?: $params['cc_enable_lobby'] );

			$admin = ! ! ( $params['admin'] ?: $params['auth'] );

			$videoServerEndpoint = ( parse_url( get_option( 'cc_video_server_url' ), PHP_URL_HOST ) );

			if ( $devMode ) {
				$server      = 'http://localhost:4001';
				$appEndpoint = 'http://localhost:3000';
			} else {
				$server      = get_option( 'cc_room_server_url' );
				$appEndpoint = get_option( 'cc_app_url' );
			}

			$roomHash = md5( json_encode( [
				'type'                => 'roomHash',
				'roomName'            => $roomName,
				'mapId'               => $mapId,
				'videoServerEndpoint' => $videoServerEndpoint,
				'sharedSecret'        => $this->sharedSecret
			] ) );

			$password = hash( 'sha256', json_encode( [
				'type'                => 'password',
				'roomName'            => $roomName,
				'mapId'               => $mapId,
				'videoServerEndpoint' => $videoServerEndpoint,
				'sharedSecret'        => $this->sharedSecret
			] ) );

			$securityToken = hash( 'sha256', json_encode( [
				'videoServerEndpoint' => $videoServerEndpoint,
				'roomName'            => $roomName,
				'admin'               => $admin,
				'sharedSecret'        => $this->sharedSecret
			] ) );

			$jwtEndpoint = get_site_url() . '/wp-json/clubcloud/jwt';

			$currentUser = wp_get_current_user();
			$userName    = $currentUser ? $currentUser->display_name : null;
			$avatarUrl   = $this->getAvatar( $currentUser );

			return <<<EOT
        <script>
            var $ = jQuery.noConflict();
            $.get("${appEndpoint}/asset-manifest.json").then(function (data) {
                data.entrypoints.map(function (entrypoint) {
                    if (entrypoint.endsWith(".js")) {
                        $.getScript("${appEndpoint}/"+ entrypoint);
                    } else {
                        $('<link rel="stylesheet" href="${appEndpoint}/' + entrypoint + '" type="text/css" />').appendTo('head');
                    }
                });
            })
        </script>
        
        <div
                style="width: 100%; border: 1px solid black"
                id="clubcloud-video-react-app"
                data-embedded="true"
                data-room-name="${roomName}"
                data-map-id="${mapId}"
                data-video-server-endpoint="${videoServerEndpoint}"
                data-jwt-endpoint="${jwtEndpoint}"
                data-server-endpoint="${server}"
                data-admin="${admin}"
                data-enable-lobby="${enableLobby}"
                data-room-hash="${roomHash}"
                data-password="${password}"
                data-security-token="${securityToken}"
                data-name="${userName}"
                data-avatar="${avatarUrl}"
                data-rooms-endpoint="${appEndpoint}"
        ></div>
EOT;
		}

		private function getAvatar( $user ) {
			return $user ? get_avatar_url( $user ) : null;
		}
	}

	class ClubCloudVideoPlugin_JWT {
		private string $sharedSecret;
		private string $webTokenKey;

		public function __construct( string $sharedSecret, string $webTokenKey ) {
			$this->sharedSecret = $sharedSecret;
			$this->webTokenKey  = $webTokenKey;

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

					$securityToken = hash( 'sha256', json_encode( [
						'videoServerEndpoint' => $videoServerEndpoint,
						'roomName'            => $roomName,
						'admin'               => true,
						'sharedSecret'        => $this->sharedSecret
					] ) );

					if ( $securityToken !== $token ) {
						return wp_send_json_error( 'Incorrect token', 403 );
					}

					return rest_ensure_response( [
						'jwt' => $this->createJWT( $roomId )
					] );
				},
			) );
		}

		private function createJWT( string $roomId ) {
			$domain       = ( parse_url( get_option( 'cc_video_server_url' ), PHP_URL_HOST ) );
			$client_token = $this->webTokenKey;                          // this collects the toke string in the admin settings used for all encryption and decryption

			$header             = json_encode( [ 'typ' => 'JWT', 'alg' => 'HS256', 'kid' => $domain ] );
			$payload            = json_encode( [
				'iss'  => $domain,
				'iat'  => idate( 'U' ),
				'exp'  => idate( 'U' ) + 3000,
				'aud'  => $domain,
				'sub'  => $domain,
				'room' => strtolower($roomId)
			] );
			$base64UrlHeader    = str_replace( [ '+', '/', '=' ], ['-','_',''], base64_encode( $header ) );  // above is the setup to generate the jwt this sets the heder information
			$base64UrlPayload   = str_replace( [ '+', '/', '=' ], ['-','_',''], base64_encode( $payload ) );       // sets the payload for the video platform
			$signature          = hash_hmac( 'sha256', $base64UrlHeader . "." . $base64UrlPayload, $client_token, true );      // base 64 hashes the payload and then encrypts the header and payload with the client key stored in the admin settings
			$base64UrlSignature = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $signature ) );
			$jwt                = "{$base64UrlHeader}.{$base64UrlPayload}.{$base64UrlSignature}";  // concatenates the encrypted string and returns the jwt

			return $jwt;
		}
	}
}
