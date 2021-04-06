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
		'clubwatch',
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
				'socket-io-3.1.0',
				'https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.0/socket.io.js',
				array(),
				'3.1.0',
				true
			)
		);

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'clubcloud-video-watch-js',
				plugins_url( '/js/watch.js', __FILE__ ),
				array( 'jquery', 'socket-io-3.1.0' ),
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
		if ( ! $this->private_key ) {
			if (
				defined( 'WP_DEBUG' ) &&
				WP_DEBUG
			) {
				return '<div>ClubCloud Video is not currently licenced</div>';
			} else {
				return '';
			}
		}

		if ( ! $params ) {
			$params = array();
		}

		preg_match_all( '/\[clubcloud_text_option.*type="(?<type>.*)"](?<data>.*)\[\/clubcloud_text_option]/msU', $contents, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			$params[ 'text-' . $match['type'] ] = $match['data'];
		}

		$room_name = $params['name'] ?? get_bloginfo( 'name' );

		$text_empty = null;
		if ( $params['text-empty'] ?? false ) {
			$text_empty = $this->format_data_attribute_text( $params['text-empty'] );
		}

		$text_empty_plain = null;
		if ( $params['text-empty-plain'] ?? false ) {
			$text_empty_plain = $this->format_data_attribute_text( $params['text-empty-plain'] );
		}

		$text_single = null;
		if ( $params['text-single'] ?? false ) {
			$text_single = $this->format_data_attribute_text( $params['text-single'] );
		}

		$text_single_plain = null;
		if ( $params['text-single-plain'] ?? false ) {
			$text_single_plain = $this->format_data_attribute_text( $params['text-single-plain'] );
		}

		$text_plural = null;
		if ( $params['text-plural'] ?? false ) {
			$text_plural = $this->format_data_attribute_text( $params['text-plural'] );
		}

		$text_plural_plain = null;
		if ( $params['text-plural-plain'] ?? false ) {
			$text_plural_plain = $this->format_data_attribute_text( $params['text-plural-plain'] );
		}

		$loading_text = 'Loading...';
		if ( $params['text-loading'] ?? false ) {
			$loading_text = $params['text-loading'];
		}

		$type = $params['type'] ?? 'reception';

		$video_server_endpoint = $this->endpoints->get_video_endpoint();
		$state_server_endpoint = $this->endpoints->get_state_endpoint();

		$room_hash = md5(
			wp_json_encode(
				array(
					'type'                => 'roomHash',
					'roomName'            => $room_name,
					'videoServerEndpoint' => $video_server_endpoint,

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Not required
					'host'                => $_SERVER['HTTP_HOST'] ?? null,
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
                data-text-empty-plain="${$text_empty_plain}"
                data-text-single="${text_single}"
                data-text-single-plain="${text_single_plain}"
                data-text-plural="${text_plural}"
                data-text-plural-plain="${text_plural_plain}"
                data-type="${type}"
            >${loading_text}</div>
        EOT;
	}

}
