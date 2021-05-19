<?php
/**
 * Short code for creating the video widget
 *
 * @package MyVideoRoomPlugin/Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Shortcode;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\Endpoints;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\Shortcode;
use WP_User;

/**
 * Class App
 */
class App extends Shortcode {
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
		\add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );

		\add_action( 'wp_enqueue_scripts', fn() => $this->enqueue_scripts() );
		\add_action(
			'wp_head',
			function () {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, - Output already escaped.
				echo $this->get_app_endpoint_head_script();
			}
		);

		\add_action(
			'myvideoroom_admin_init',
			function () {
				\add_action( 'admin_enqueue_scripts', fn() => $this->enqueue_scripts() );

				\add_action(
					'admin_head',
					function () {
						//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, - Output already escaped.
						echo $this->get_app_endpoint_head_script();
					}
				);
			}
		);
	}

	/**
	 * Enqueue the required javascript libraries
	 */
	public function enqueue_scripts() {
		\wp_enqueue_script( 'jquery' );

		\wp_enqueue_script(
			'myvideoroom-app',
			\plugins_url( '/../js/app.js', __FILE__ ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version(),
			true
		);
	}

	/**
	 * Get script to insert into head for JavaScript to be able to fetch the correct endpoint
	 */
	public function get_app_endpoint_head_script(): string {
		return '<script>var myVideoRoomAppEndpoint = "' . \esc_url( $this->endpoints->get_app_endpoint() ) . '"</script>';
	}

	/**
	 * Create the video widget
	 *
	 * @param array|string $attr List of params to pass to the shortcode.
	 *
	 * @return string
	 */
	public function output_shortcode( $attr = array() ) {
		if ( ! $attr ) {
			$attr = array();
		}

		if ( ! $this->private_key ) {
			return $this->return_error(
				'<div>' . \esc_html__(
					'MyVideoRoom is currently unlicensed.',
					'myvideoroom'
				) . '</div>'
			);
		}

		$hostname = $this->get_host();

		if ( ! $hostname ) {
			return $this->return_error(
				'<div>' . \esc_html__(
					'MyVideoRoom cannot find the host that it is currently running on.',
					'myvideoroom'
				) . '</div>'
			);
		}

		$shortcode_constructor = new AppShortcodeConstructor();

		$shortcode_constructor->set_name( $attr['name'] ?? \get_bloginfo( 'name' ) );
		$shortcode_constructor->set_seed( $attr['seed'] ?? null );
		$shortcode_constructor->set_layout( $attr['layout'] ?? 'boardroom' );
		$shortcode_constructor->set_reception_id( $attr['reception-id'] ?? 'office' );

		if ( $attr['reception-video'] ?? null ) {
			$shortcode_constructor->set_reception_video_url( $attr['reception-video'] );
		}

		if ( 'true' === ( $attr['lobby'] ?? 'false' ) ) {
			$shortcode_constructor->enable_lobby();
		} else {
			$shortcode_constructor->disable_lobby();
		}

		if ( 'true' === ( $attr['reception'] ?? 'false' ) ) {
			$shortcode_constructor->enable_reception();
		} else {
			$shortcode_constructor->disable_reception();
		}

		// load legacy admin settings.
		if ( isset( $attr['admin'] ) && ! isset( $attr['host'] ) ) {
			$attr['host'] = $attr['admin'];
		}

		if ( ! isset( $attr['host'] ) ) {
			$host = \current_user_can( Plugin::CAP_GLOBAL_HOST );
		} else {
			$host = ( 'true' === $attr['host'] );
		}

		if ( true === $host ) {
			$shortcode_constructor->set_as_host();
		} elseif ( false === $host ) {
			$shortcode_constructor->set_as_guest();
		}

		if ( 'true' === ( $attr['floorplan'] ?? 'false' ) ) {
			$shortcode_constructor->enable_floorplan();
		} else {
			$shortcode_constructor->disable_floorplan();
		}

		/**
		 * Apply filter on the shortcode constructor to allow plugins to modify it's contents.
		 *
		 * @var AppShortcodeConstructor $shortcode_constructor
		 */
		$shortcode_constructor = \apply_filters( 'myvideoroom_shortcode_constructor', $shortcode_constructor, $attr );

		if ( $shortcode_constructor->get_error() ) {
			return '<div class="myvideoroom-error">' . $shortcode_constructor->get_error() . '</div>';
		}

		// --

		$loading_text = \esc_html__( 'Loading...', 'myvideoroom' );
		if ( $attr['text-loading'] ?? false ) {
			$loading_text = $attr['text-loading'];
		}

		$video_server_endpoint = $this->endpoints->get_video_endpoint();
		$state_server          = $this->endpoints->get_state_endpoint();
		$rooms_endpoint        = $this->endpoints->get_rooms_endpoint();
		$app_endpoint          = $this->endpoints->get_app_endpoint();
		$licence_endpoint      = $this->endpoints->get_licence_endpoint();

		$room_hash = \md5(
			\wp_json_encode(
				array(
					'type'                => 'roomHash',
					'roomName'            => $shortcode_constructor->get_name(),
					'videoServerEndpoint' => $video_server_endpoint,
					'host'                => $hostname,
					'seed'                => $shortcode_constructor->get_seed(),
				)
			)
		);

		$password = \hash(
			'sha256',
			\wp_json_encode(
				array(
					'type'                => 'password',
					'roomName'            => $shortcode_constructor->get_name(),
					'layoutId'            => $shortcode_constructor->get_layout(),
					'videoServerEndpoint' => $video_server_endpoint,
					'host'                => $hostname,
					'privateKey'          => $this->private_key,
				)
			)
		);

		$message = \wp_json_encode(
			array(
				'videoServerEndpoint' => $video_server_endpoint,
				'roomName'            => $shortcode_constructor->get_name(),
				'host'                => $shortcode_constructor->is_host(),
				'enableFloorplan'     => $shortcode_constructor->is_floorplan_enabled(),
			)
		);

		if ( ! \openssl_sign( $message, $signature, $this->private_key, OPENSSL_ALGO_SHA256 ) ) {
			return $this->return_error( \esc_html__( 'MyVideoRoom was unable to sign the data.', 'myvideoroom' ) );
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for passing data to javascript
		$security_token = \rawurlencode( \base64_encode( $signature ) );

		$jwt_endpoint = $licence_endpoint . '/' . $hostname . '.jwt?';

		$current_user = \wp_get_current_user();

		$user_name  = null;
		$avatar_url = null;

		if ( isset( $attr['user-name'] ) ) {
			$user_name = \esc_attr( $attr['user-name'] );
		} elseif ( $current_user ) {
			$user_name  = $current_user->display_name;
			$avatar_url = $this->getAvatar( $current_user );
		}

		$custom_jitsi_server = true;

		$room_name         = $shortcode_constructor->get_name();
		$layout_id         = $shortcode_constructor->get_layout();
		$lobby_enabled     = $shortcode_constructor->is_lobby_enabled();
		$reception_enabled = $shortcode_constructor->is_reception_enabled();
		$reception_id      = $shortcode_constructor->get_reception_id();
		$reception_video   = $shortcode_constructor->get_reception_video();
		$floorplan_enabled = $shortcode_constructor->is_floorplan_enabled();
		$host              = $shortcode_constructor->is_host();

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
                data-host="${host}"
                data-enable-lobby="${lobby_enabled}"
                data-enable-reception="${reception_enabled}"
                data-reception-id="${reception_id}"
                data-reception-video="${reception_video}"
                data-enable-floorplan="${floorplan_enabled}"
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
		if ( $user && \get_avatar_url( $user ) ) {
			return \get_avatar_url( $user );
		}

		return null;
	}
}
