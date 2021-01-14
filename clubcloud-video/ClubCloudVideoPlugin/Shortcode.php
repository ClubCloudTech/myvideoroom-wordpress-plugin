<?php

class ClubCloudVideoPlugin_Shortcode {
	const SHORTCODE_TAG = 'clubvideo';

	private string $privateKey;

	public function __construct( string $privateKey ) {
		$this->privateKey = $privateKey;

		add_shortcode( self::SHORTCODE_TAG, [ $this, 'createShortcode' ] );

		add_filter( 'query_vars', fn( $queryVars ) => array_merge( $queryVars, [ 'dev' ] ) );
		add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script( 'jquery' ) );
	}

	public function createShortcode( $params ) {
		$params = $params ?: [];

		$devMode = ( get_query_var( 'dev', false ) === 'true' );

		$roomName    = $params['name'] ?: $params['cc_event_id'];
		$mapId       = $params['map'] ?: $params['cc_plan_id'];
		$enableLobby = ! ! ( $params['lobby'] ?: $params['cc_enable_lobby'] );
		$enableReception = ! ! ($params['reception']);

		$admin = ! ! ( $params['admin'] ?: $params['auth'] );

		$videoServerEndpoint = 'meet.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );

		if ( $devMode ) {
			$stateServer      = 'http://localhost:4001';
			$appEndpoint = 'http://localhost:3000';
			$roomsEndpoint = 'http://localhost:4002';
		} else {
			$stateServer      = 'https://state.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );;
			$appEndpoint = 'https://app.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );;
			$roomsEndpoint = 'https://rooms.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );;
		}

		$roomHash = md5( json_encode( [
			'type'                => 'roomHash',
			'roomName'            => $roomName,
			'mapId'               => $mapId,
			'videoServerEndpoint' => $videoServerEndpoint,
			'host'                => $_SERVER['host']
		] ) );

		$password = hash( 'sha256', json_encode( [
			'type'                => 'password',
			'roomName'            => $roomName,
			'mapId'               => $mapId,
			'videoServerEndpoint' => $videoServerEndpoint,
			'host'                => $_SERVER['host'],
			'privateKey'          => $this->privateKey
		] ) );

		$message = json_encode( [
			'videoServerEndpoint' => $videoServerEndpoint,
			'roomName'            => $roomName,
			'admin'               => $admin
		] );

		if (!openssl_sign($message, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
			throw new Exception("Unable to sign data.");
		}

		$securityToken = urlencode(base64_encode($signature));

		$jwtEndpoint = get_site_url() . '/wp-json/clubcloud/jwt';

		$currentUser = wp_get_current_user();
		$userName    = $currentUser ? $currentUser->display_name : null;
		$avatarUrl   = $this->getAvatar( $currentUser );

		return <<<EOT
            <script>
                var ccJq = jQuery.noConflict();
                ccJq.get("${appEndpoint}/asset-manifest.json").then(function (data) {
                    Object.values(data.files).map(function (file) {
                        if (file.endsWith(".js")) {
                            ccJq.getScript("${appEndpoint}/"+ file);
                        } else if (file.endsWith(".css")) {
                            ccJq('<link rel="stylesheet" href="${appEndpoint}/' + file + '" type="text/css" />').appendTo('head');
                        }
                    });
                })
            </script>
            
            <div
                    style="width: 100%; border: 1px solid black"
                    class="clubcloud-video-react-app"
                    data-embedded="true"
                    data-room-name="${roomName}"
                    data-map-id="${mapId}"
                    data-video-server-endpoint="${videoServerEndpoint}"
                    data-jwt-endpoint="${jwtEndpoint}"
                    data-server-endpoint="${stateServer}"
                    data-admin="${admin}"
                    data-enable-lobby="${enableLobby}"
                    data-enable-reception="${enableReception}"
                    data-room-hash="${roomHash}"
                    data-password="${password}"
                    data-security-token="${securityToken}"
                    data-name="${userName}"
                    data-avatar="${avatarUrl}"
                    data-rooms-endpoint="${roomsEndpoint}"
            ></div>
    EOT;
	}

	private function getAvatar( $user ) {
		return $user ? get_avatar_url( $user ) : null;
	}
}
