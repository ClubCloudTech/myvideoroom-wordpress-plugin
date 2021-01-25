<?php

class ClubCloudVideoPlugin_MonitorShortcode extends ClubCloudVideoPlugin_Shortcode {
	const SHORTCODE_TAGS = [
		'clubcloud_monitor',
		'clubcloud_reception_widget',
		'clubwatch'
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

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'clubcloud-socket-io-3.1.0',
				'https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.0/socket.io.js',
				[],
				null
			)
		);

		$pluginData = get_plugin_data( __DIR__ . '/../index.php' );
		$pluginVersion = $pluginData['Version'];

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'clubcloudvideo-watch-js',
				plugins_url( '/../js/watch.js', __FILE__ ),
				['jquery', 'clubcloud-socket-io-3.1.0'],
				$pluginVersion
			)
		);
	}

	public function createShortcode( array $params = null, $contents ): string {

		$params = $params ?: [];

		preg_match_all( '/\[clubcloud_text_option.*type="(?<type>.*)"](?<data>.*)\[\/clubcloud_text_option]/msU', $contents, $matches, PREG_SET_ORDER );

		foreach( $matches as $match ) {
			$params['text-' . $match['type']] = $match['data'];
		}

		$roomName = $params['name'];

		$textEmpty = $this->formatText($params['text-empty'] ?: null);
		$textSingle = $this->formatText($params['text-single'] ?: null);
		$textPlural = $this->formatText($params['text-plural'] ?: null);
		$loadingText = $params['text-loading'] ?: "Loading...";

		$type = $params['type'];

		$videoServerEndpoint = $this->endpoints->getVideoEndpoint();
		$stateServer = $this->endpoints->getStateEndpoint();

		$roomHash = md5( json_encode( [
			'type'                => 'roomHash',
			'roomName'            => $roomName,
			'videoServerEndpoint' => $videoServerEndpoint,
			'host'                => $_SERVER['host']
		] ) );

		$message = json_encode( [
			'videoServerEndpoint' => $videoServerEndpoint,
			'roomName'            => $roomName,
			'admin'               => true,
			'enableFloorplan'    => false
		] );

		if (!openssl_sign($message, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
			throw new Exception("Unable to sign data.");
		}

		$securityToken = urlencode(base64_encode($signature));

		return <<<EOT
            <div
                class="clubcloud-video-waiting"
                data-room-name="${roomName}"
                data-room-hash="${roomHash}"
                data-video-server-endpoint="${videoServerEndpoint}"
                data-server-endpoint="${stateServer}"
                data-security-token="${securityToken}"
                data-text-empty="${textEmpty}"
                data-text-single="${textSingle}"
                data-text-plural="${textPlural}"
                data-type="${type}"
            >${loadingText}</div>
        EOT;
	}

}
