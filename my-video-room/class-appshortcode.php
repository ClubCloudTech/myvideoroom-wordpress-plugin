<?php
/**
 * Short code for creating the video widget
 *
 * @package MyVideoRoomPlugin
 */

declare(strict_types=1);

namespace MyVideoRoomPlugin;

use Exception;
use WP_User;

/**
 * Class AppShortcode
 */
class AppShortcode extends Shortcode {
	const SHORTCODE_TAG = 'myvideoroom';

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
	 * AppShortcode constructor.
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

		add_action( 'wp_enqueue_scripts', fn() => $this->enqueue_scripts() );
		add_action(
			'wp_head',
			function () {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, - Output already escaped.
				echo $this->get_app_endpoint_head_script();
			}
		);

		add_action( 'myvideoroom_enqueue_scripts', fn() => $this->enqueue_scripts() );
		add_action(
			'myvideoroom_head',
			function () {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, - Output already escaped.
				echo $this->get_app_endpoint_head_script();
			}
		);
	}

	/**
	 * Enqueue the required javascript libraries
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );

		wp_enqueue_script(
			'myvideoroom-app',
			plugins_url( '/js/app.js', __FILE__ ),
			array( 'jquery' ),
			$this->get_plugin_version(),
			true
		);
	}

	/**
	 * Get script to insert into head for JavaScript to be able to fetch the correct endpoint
	 */
	public function get_app_endpoint_head_script(): string {
		return '<script>var myVideoRoomAppEndpoint = "' . esc_url( $this->endpoints->get_app_endpoint() ) . '"</script>';
	}

	/**
	 * Create the video widget
	 *
	 * @param array $params List of params to pass to the shortcode.
	 *
	 * @return string
	 */
	public function output_shortcode( $params = array() ) {
		$host = $this->get_host();

		if ( ! $host ) {
			$this->return_error(
				'<div>' . esc_html__(
					'>My Video Room cannot find the host that it is currentlyrunning on.',
					'myvideoroom'
				) . '</div>'
			);
		}

		if ( ! $params ) {
			$params = array();
		}

		$room_name = $params['name'] ?? get_bloginfo( 'name' );
		$layout_id = $params['layout'] ?? 'boardroom';

		$reception_id = 'office';
		if ( $params['reception-id'] ?? false ) {
			$reception_id = $params['reception-id'];
		}

		$reception_video  = $params['reception-video'] ?? null;
		$enable_lobby     = 'true' === ( $params['lobby'] ?? 'false' );
		$enable_reception = 'true' === ( $params['reception'] ?? 'false' );

		if ( ! isset( $params['admin'] ) ) {
			$admin = current_user_can( Plugin::CAP_GLOBAL_ADMIN );
		} else {
			$admin = 'true' === $params['admin'];
		}

		$enable_floorplan = 'true' === ( $params['floorplan'] ?? 'false' );

		$loading_text = esc_html__( 'Loading...', 'myvideoroom' );
		if ( $params['text-loading'] ?? false ) {
			$loading_text = $params['text-loading'];
		}

		$video_server_endpoint = $this->endpoints->get_video_endpoint();
		$state_server          = $this->endpoints->get_state_endpoint();
		$rooms_endpoint        = $this->endpoints->get_rooms_endpoint();
		$app_endpoint          = $this->endpoints->get_app_endpoint();
		$licence_endpoint      = $this->endpoints->get_licence_endpoint();

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

		$password = hash(
			'sha256',
			wp_json_encode(
				array(
					'type'                => 'password',
					'roomName'            => $room_name,
					'layoutId'            => $layout_id,
					'videoServerEndpoint' => $video_server_endpoint,
					'host'                => $host,
					'privateKey'          => $this->private_key,
				)
			)
		);

		$message = wp_json_encode(
			array(
				'videoServerEndpoint' => $video_server_endpoint,
				'roomName'            => $room_name,
				'admin'               => $admin,
				'enableFloorplan'     => $enable_floorplan,
			)
		);

		if ( ! openssl_sign( $message, $signature, $this->private_key, OPENSSL_ALGO_SHA256 ) ) {
			return $this->return_error( esc_html__( 'My Video Room was unable to sign the data.', 'myvideoroom' ) );
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for passing data to javascript
		$security_token = rawurlencode( base64_encode( $signature ) );

		$jwt_endpoint = $licence_endpoint . '/' . $host . '.jwt?';

		$current_user = wp_get_current_user();

		$user_name  = null;
		$avatar_url = null;

		if ( isset( $params['user-name'] ) ) {
			$user_name = esc_attr( $params['user-name'] );
		} elseif ( $current_user ) {
			$user_name  = $current_user->display_name;
			$avatar_url = $this->getAvatar( $current_user );
		}

		$custom_jitsi_server = true;

		return <<<EOT
            <div
                class="myvideoroom-app"
                data-embedded="true"
                data-room-name="${room_name}"
                data-layout-id="${layout_id}"
                data-video-server-endpoint="${video_server_endpoint}"
                data-app-endpoint="${app_endpoint}"
                data-jwt-endpoint="${jwt_endpoint}"
                data-server-endpoint="${state_server}"
                data-admin="${admin}"
                data-enable-lobby="${enable_lobby}"
                data-enable-reception="${enable_reception}"
                data-reception-id="${reception_id}"
                data-reception-video="${reception_video}"
                data-enable-floorplan="${enable_floorplan}"
                data-room-hash="${room_hash}"
                data-password="${password}"
                data-security-token="${security_token}"
                data-name="${user_name}"
                data-avatar="${avatar_url}"
                data-rooms-endpoint="${rooms_endpoint}"
                data-has-subscription="${custom_jitsi_server}"
            >${loading_text}</div>
        EOT;
	}

	/**
	 * Get the avatar url for a user
	 *
	 * @param WP_User|null $user The current WordPress user.
	 *
	 * @return string|null
	 */
	private function getAvatar( WP_User $user = null ): ?string {
		if ( $user && get_avatar_url( $user ) ) {
			return get_avatar_url( $user );
		}

		return null;
	}
}
