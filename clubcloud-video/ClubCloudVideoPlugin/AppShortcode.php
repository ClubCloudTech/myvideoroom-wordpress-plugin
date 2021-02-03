<?php

class ClubCloudVideoPlugin_AppShortcode extends ClubCloudVideoPlugin_Shortcode  {
	const SHORTCODE_TAGS = [
		'clubcloud_app',
		'clubvideo'
	];

	private string $privateKey;
	private ClubCloudVideoPlugin_Endpoints $endpoints;

	public function __construct( string $privateKey ) {
		$this->privateKey = $privateKey;
		$this->endpoints = new ClubCloudVideoPlugin_Endpoints();

		foreach(self::SHORTCODE_TAGS as $shortcodeTag) {
			add_shortcode( $shortcodeTag, [ $this, 'createShortcode' ] );
		}

		add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script( 'jquery' ) );


		$pluginData = get_plugin_data( __DIR__ . '/../index.php' );
		$pluginVersion = $pluginData['Version'];

		add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script(
			'clubcloudvideo-app-js',
			plugins_url( '/../js/app.js', __FILE__ ),
			['jquery'],
			$pluginVersion
		));

		add_action( 'wp_head', function () {
			echo '<script>var clubCloudAppEndpoint = "' . $this->endpoints->getAppEndpoint() . '"</script>';
		} );
	}

	/**
	 * @deprecated
	 * @param array $params
	 *
	 * @return array
	 */
	private function processLegacy( $params )
	{
		if (!$params['name'] && $params['cc_event_id']) {
			$params['name'] = $params['cc_event_id'];
		}

		if (!$params['reception'] && $params['cc_is_reception']) {
			$params['reception'] = !!$params['cc_is_reception'];
		}

		if (!$params['map'] && $params['cc_plan_id']) {
			$params['map'] = $params['cc_plan_id'];
		}

		if (!$params['lobby'] && $params['cc_enable_lobby']) {
			$params['lobby'] = !!$params['cc_enable_lobby'];
		}

		if (!$params['admin'] && $params['auth']) {
			$params['admin'] = !!$params['auth'];
		}

		// process legacy rooms

		if ($params['map'] === 'Innovateoffice') {
			$params['map'] = 'innovate-office';
		}

		if ($params['map'] === 'boardroom1') {
			$params['map'] = 'boardroom';
		}

		return $params;
	}

	public function createShortcode( $params ) {
		$params = $params ?: [];
		$params = $this->processLegacy($params);

		$roomName    = $params['name'];
		$mapId       = $params['map'];
		$receptionId = $params['reception-id'];
		$receptionVideo = $params['reception-video'];
		$enableLobby = ! ! ( $params['lobby']);
		$enableReception = ! ! ($params['reception']);
		$admin = ! ! ( $params['admin']);
		$enableFloorplan = ! ! ( $params['floorplan']);
		$loadingText = $params['text-loading'] ?: "Loading...";

		$videoServerEndpoint = $this->endpoints->getVideoEndpoint();
		$stateServer = $this->endpoints->getStateEndpoint();
		$roomsEndpoint = $this->endpoints->getRoomsEndpoint();

		$roomHash = md5( json_encode( [
			'type'                => 'roomHash',
			'roomName'            => $roomName,
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
			'admin'               => $admin,
			'enableFloorplan'     => $enableFloorplan
		] );

		if (!openssl_sign($message, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
			throw new Exception("Unable to sign data.");
		}

		$securityToken = urlencode(base64_encode($signature));

		$jwtEndpoint = get_site_url() . '/wp-json/clubcloud/jwt';

		$currentUser = wp_get_current_user();
		$userName    = $currentUser ? $currentUser->display_name : null;
		$avatarUrl   = $this->getAvatar( $currentUser );
		$hasSubscription = $videoServerEndpoint === $this->endpoints->getSubscribedVideoEndpoint();

		return <<<EOT
            <div
                class="clubcloud-video-app"
                data-embedded="true"
                data-room-name="${roomName}"
                data-map-id="${mapId}"
                data-video-server-endpoint="${videoServerEndpoint}"
                data-jwt-endpoint="${jwtEndpoint}"
                data-server-endpoint="${stateServer}"
                data-admin="${admin}"
                data-enable-lobby="${enableLobby}"
                data-enable-reception="${enableReception}"
                data-reception-id="${receptionId}"
                data-reception-video="${receptionVideo}"
                data-enable-floorplan="${enableFloorplan}"
                data-room-hash="${roomHash}"
                data-password="${password}"
                data-security-token="${securityToken}"
                data-name="${userName}"
                data-avatar="${avatarUrl}"
                data-rooms-endpoint="${roomsEndpoint}"
                data-has-subscription="${hasSubscription}"
            >${loadingText}</div>
        EOT;
	}

	private function getAvatar( $user ) {
		return $user ? get_avatar_url( $user ) : null;
	}
}
