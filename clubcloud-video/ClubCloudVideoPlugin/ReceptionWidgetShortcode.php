<?php

class ClubCloudVideoPlugin_ReceptionWidgetShortcode {
	const SHORTCODE_TAGS = [
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

		add_filter( 'query_vars', fn( $queryVars ) => array_merge( $queryVars, [ 'dev' ] ) );
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

	public function createShortcode( $params ): string {
		$params = $params ?: [];

		$roomName = $params['name'];
		$textEmpty = $params['text-empty'] ?: null;
		$textSingle = $params['text-single'] ?: null;
		$textPlural = $params['text-plural'] ?: null;
		$loadingText = $params['text-loading'] ?: "Loading...";

		$type = $params['type'];

		$videoServerEndpoint = $this->endpoints->getVideoEndpoint();
		$stateServer = $this->endpoints->getStateEndpoint();

		$message = json_encode( [
			'videoServerEndpoint' => $videoServerEndpoint,
			'roomName'            => $roomName,
			'admin'               => true
		] );

		if (!openssl_sign($message, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
			throw new Exception("Unable to sign data.");
		}

		$securityToken = urlencode(base64_encode($signature));

		return <<<EOT
            <div
                class="clubcloud-video-waiting"
                data-room-name="${roomName}"
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
