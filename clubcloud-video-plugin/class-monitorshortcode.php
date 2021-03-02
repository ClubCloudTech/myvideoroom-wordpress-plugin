<?php
/**
 * Short code for monitoring a room
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

use Exception;

/**
 * Class MonitorShortcode
 */
class MonitorShortcode extends Shortcode {

	const SHORTCODE_TAGS = array(
		'clubcloud_monitor',
	);

	/**
	 * The private key to authorise this install.
	 *
	 * @var string
	 */
	private string $private_key;

	/**
	 * The list of endpoints for services.
	 *
	 * @var Endpoints
	 */
	private Endpoints $endpoints;

	/**
	 * MonitorShortcode constructor.
	 *
	 * @param string $private_key The private key to authorise this install.
	 */
	public function __construct( string $private_key ) {
		$this->private_key = $private_key;
		$this->endpoints   = new Endpoints();
	}

	/**
	 * Install the shortcode
	 */
	public function init() {
		foreach ( self::SHORTCODE_TAGS as $shortcode_tag ) {
			add_shortcode( $shortcode_tag, array( $this, 'output_shortcode' ) );
		}

		add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script( 'jquery' ) );

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'clubcloud-socket-io-3.1.0',
				'https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.0/socket.io.js',
				array(),
				'3.1.0',
				true
			)
		);

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'clubcloudvideo-watch-js',
				plugins_url( '/../js/watch.js', __FILE__ ),
				array( 'jquery', 'clubcloud-socket-io-3.1.0' ),
				$this->get_plugin_version(),
				true
			)
		);
	}

	/**
	 * Output the widget
	 *
	 * @param array|null $params    Params passed from the shortcode to this function.
	 * @param string     $contents  The text content of the shortcode.
	 *
	 * @return string
	 * @throws Exception When unable to sign the request.
	 */
	public function output_shortcode( array $params = null, $contents = '' ): string {

		if ( ! $params ) {
			$params = array();
		}

		preg_match_all( '/\[clubcloud_text_option.*type="(?<type>.*)"](?<data>.*)\[\/clubcloud_text_option]/msU', $contents, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			$params[ 'text-' . $match['type'] ] = $match['data'];
		}

		$room_name = $params['name'];

		$text_empty = null;
		if ( $params['text-empty'] ) {
			$text_empty = $this->format_data_attribute_text( $params['text-empty'] );
		}

		$text_single = null;
		if ( $params['text-single'] ) {
			$text_single = $this->format_data_attribute_text( $params['text-single'] );
		}

		$text_plural = null;
		if ( $params['text-plural'] ) {
			$text_plural = $this->format_data_attribute_text( $params['text-plural'] );
		}

		$loading_text = 'Loading...';
		if ( $params['text-loading'] ) {
			$loading_text = $params['text-loading'];
		}

		$type = $params['type'];

		$video_server_endpoint = $this->endpoints->get_video_endpoint();
		$state_server_endpoint = $this->endpoints->get_state_endpoint();

		$room_hash = md5(
			wp_json_encode(
				array(
					'type'                => 'roomHash',
					'roomName'            => $room_name,
					'videoServerEndpoint' => $video_server_endpoint,

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Not required
					'host'                => $_SERVER['host'] ?? null,
				)
			)
		);

		$message = wp_json_encode(
			array(
				'videoServerEndpoint' => $video_server_endpoint,
				'roomName'            => $room_name,
				'admin'               => true,
				'enableFloorplan'     => false,
			)
		);

		if ( ! openssl_sign( $message, $signature, $this->private_key, OPENSSL_ALGO_SHA256 ) ) {
			throw new Exception( 'Unable to sign data.' );
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for passing data to javascript
		$security_token = rawurlencode( base64_encode( $signature ) );

		return <<<EOT
            <div
                class="clubcloud-video-waiting"
                data-room-name="${room_name}"
                data-room-hash="${room_hash}"
                data-video-server-endpoint="${video_server_endpoint}"
                data-server-endpoint="${state_server_endpoint}"
                data-security-token="${security_token}"
                data-text-empty="${text_empty}"
                data-text-single="${text_single}"
                data-text-plural="${text_plural}"
                data-type="${type}"
            >${loading_text}</div>
        EOT;
	}

}
