<?php
/**
 * Short code for monitoring a room
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

/**
 * Class MonitorShortcode
 */
class MonitorShortcode extends Shortcode {

	const SHORTCODE_TAG = 'myvideoroom_monitor';

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
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );

		add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script( 'jquery' ) );

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'socket-io-3.1.0',
				plugins_url( '/third-party/socket.io.js', __FILE__ ),
				array(),
				'3.1.0',
				true
			)
		);

		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_enqueue_script(
					'myvideoroom-monitor',
					plugins_url( '/js/monitor.js', __FILE__ ),
					array( 'jquery', 'socket-io-3.1.0' ),
					$this->get_plugin_version(),
					true
				);

				wp_localize_script(
					'myvideoroom-monitor',
					'myvideoroom_monitor_texts',
					array(
						'reception' => array(
							'textEmpty'  => esc_html__( 'Nobody is currently waiting', 'myvideoroom' ),
							'textSingle' => esc_html__( 'One person is waiting in reception', 'myvideoroom' ),
							'textPlural' => esc_html__( '{{count}} people are waiting in reception', 'myvideoroom' ),
						),
						'seated'    => array(
							'textEmpty'  => esc_html__( 'Nobody is currently seated', 'myvideoroom' ),
							'textSingle' => esc_html__( 'One person is seated', 'myvideoroom' ),
							'textPlural' => esc_html__( '{{count}} people are seated', 'myvideoroom' ),
						),
						'all'       => array(
							'textEmpty'  => esc_html__( 'Nobody is currently in this room', 'myvideoroom' ),
							'textSingle' => esc_html__( 'One person is currently in this room', 'myvideoroom' ),
							'textPlural' => esc_html__( '{{count}} people are currently in this room', 'myvideoroom' ),
						),
					)
				);

			}
		);

	}

	/**
	 * Output the widget
	 *
	 * @param array  $params    Params passed from the shortcode to this function.
	 * @param string $contents  The text content of the shortcode.
	 *
	 * @return string
	 */
	public function output_shortcode( $params = array(), $contents = '' ): string {
		if ( ! $this->private_key ) {
			if (
				defined( 'WP_DEBUG' ) &&
				WP_DEBUG
			) {
				return '<div>' . esc_html__( 'MyVideoRoom is currently unlicensed', 'myvideoroom' ) . '</div>';
			} else {
				return '';
			}
		}

		$host = $this->get_host();

		if ( ! $params ) {
			$params = array();
		}

		$room_name = sanitize_text_field( $params['name'] ?? get_bloginfo( 'name' ) );
		$type      = sanitize_key( $params['type'] ?? 'reception' );

		$video_server_endpoint = $this->endpoints->get_video_endpoint();
		$state_server_endpoint = $this->endpoints->get_state_endpoint();

		$room_hash = md5(
			wp_json_encode(
				array(
					'type'                => 'roomHash',
					'roomName'            => $room_name,
					'videoServerEndpoint' => $video_server_endpoint,
					'host'                => $host,
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
			return $this->return_error( esc_html__( 'MyVideoRoom was unable to sign the data.', 'myvideoroom' ) );
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for passing data to javascript
		$security_token = rawurlencode( base64_encode( $signature ) );

		// parse enclosing shortcode with text options.
		preg_match_all(
			'/\[myvideoroom_text_option.*type="(?<type>.*)"](?<data>.*)\[\/myvideoroom_text_option]/msU',
			$contents,
			$matches,
			PREG_SET_ORDER
		);

		foreach ( $matches as $match ) {
			$params[ 'text-' . $match['type'] ] = $this->sanitize_html(
				// strip <br> from start and end.
				preg_replace(
					'/^(<br\s*\/?>)*|(<br\s*\/?>)*$/i',
					'',
					$match['data']
				)
			);
		}

		$text_loading      = $this->sanitize_html( $params['text-loading'] ?? '' );
		$text_empty        = $this->sanitize_html( $params['text-empty'] ?? '' );
		$text_empty_plain  = $this->sanitize_html( $params['text-empty-plain'] ?? wp_strip_all_tags( $text_empty ) );
		$text_single       = $this->sanitize_html( $params['text-single'] ?? '' );
		$text_single_plain = $this->sanitize_html( $params['text-single-plain'] ?? wp_strip_all_tags( $text_single ) );
		$text_plural       = $this->sanitize_html( $params['text-plural'] ?? '' );
		$text_plural_plain = $this->sanitize_html( $params['text-plural-plain'] ?? wp_strip_all_tags( $text_plural ) );

		return <<<EOT
            <div
                class="myvideoroom-monitor"
                data-room-name="${room_name}"
                data-room-hash="${room_hash}"
                data-video-server-endpoint="${video_server_endpoint}"
                data-server-endpoint="${state_server_endpoint}"
                data-security-token="${security_token}"
                data-text-empty="${text_empty}"
                data-text-empty-plain="${text_empty_plain}"
                data-text-single="${text_single}"
                data-text-single-plain="${text_single_plain}"
                data-text-plural="${text_plural}"
                data-text-plural-plain="${text_plural_plain}"
                data-type="${type}"
            >${text_loading}</div>
        EOT;
	}

	/**
	 * Sanitize and escape an html param for passing into a data attribute
	 *
	 * @param string|null $html A block of text or html.
	 *
	 * @return ?string
	 */
	private function sanitize_html( string $html = null ): ? string {
		if ( $html ) {
			return esc_html( force_balance_tags( wp_kses_post( $html ) ) );
		}

		return null;
	}

}
