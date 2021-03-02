<?php
/**
 * Short code for creating the video widget
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

use Exception;
use WP_User;

/**
 * Class AppShortcode
 */
class AppShortcode extends Shortcode {
	const SHORTCODE_TAGS = array(
		'clubcloud_app',
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
		foreach ( self::SHORTCODE_TAGS as $shortcode_tag ) {
			add_shortcode( $shortcode_tag, array( $this, 'output_shortcode' ) );
		}

		add_action( 'wp_enqueue_scripts', fn() => wp_enqueue_script( 'jquery' ) );

		add_action(
			'wp_enqueue_scripts',
			fn() => wp_enqueue_script(
				'clubcloudvideo-app-js',
				plugins_url( '/../js/app.js', __FILE__ ),
				array( 'jquery' ),
				$this->get_plugin_version(),
				true
			)
		);

		add_action(
			'wp_head',
			function () {
				echo '<script>var clubCloudAppEndpoint = "' . esc_url( $this->endpoints->get_app_endpoint() ) . '"</script>';
			}
		);
	}

	/**
	 * Convert legacy params to new format
	 *
	 * @param array $params List of params to convert to new format.
	 *
	 * @return array
	 * @deprecated
	 */
	private function process_legacy_params( array $params ): array {
		if ( ! $params['name'] && $params['cc_event_id'] ) {
			$params['name'] = $params['cc_event_id'];
		}

		if ( ! $params['reception'] && $params['cc_is_reception'] ) {
			$params['reception'] = ! ! $params['cc_is_reception'];
		}

		if ( ! $params['map'] && $params['cc_plan_id'] ) {
			$params['map'] = $params['cc_plan_id'];
		}

		if ( ! $params['lobby'] && $params['cc_enable_lobby'] ) {
			$params['lobby'] = ! ! $params['cc_enable_lobby'];
		}

		if ( ! $params['admin'] && $params['auth'] ) {
			$params['admin'] = ! ! $params['auth'];
		}

		// process legacy rooms.

		if ( 'Innovateoffice' === $params['map'] ) {
			$params['map'] = 'innovate-office';
		}

		if ( 'boardroom1' === $params['map'] ) {
			$params['map'] = 'boardroom';
		}

		return $params;
	}

	/**
	 * Create the video widget
	 *
	 * @param array $params List of params to pass to the shortcode.
	 *
	 * @return string
	 * @throws Exception When unable to sign the request.
	 */
	public function output_shortcode( $params = array() ) {
		if ( ! $params ) {
			$params = array();
		}

		$params = $this->process_legacy_params( $params );

		$room_name = $params['name'];
		$map_id    = $params['map'];

		$reception_id = 'office';
		if ( $params['reception-id'] ) {
			$reception_id = $params['reception-id'];
		}

		$reception_video  = $params['reception-video'];
		$enable_lobby     = ! ! ( $params['lobby'] );
		$enable_reception = ! ! ( $params['reception'] );
		$admin            = ! ! ( $params['admin'] );
		$enable_floorplan = ! ! ( $params['floorplan'] );

		$loading_text = 'Loading...';
		if ( $params['text-loading'] ) {
			$loading_text = $params['text-loading'];
		}

		$video_server_endpoint = $this->endpoints->get_video_endpoint();
		$state_server          = $this->endpoints->get_state_endpoint();
		$rooms_endpoint        = $this->endpoints->get_rooms_endpoint();

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

		$password = hash(
			'sha256',
			wp_json_encode(
				array(
					'type'                => 'password',
					'roomName'            => $room_name,
					'mapId'               => $map_id,
					'videoServerEndpoint' => $video_server_endpoint,

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Not required
					'host'                => $_SERVER['host'] ?? null,
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
			throw new Exception( 'Unable to sign data.' );
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for passing data to javascript
		$security_token = rawurlencode( base64_encode( $signature ) );

		$jwt_endpoint = get_site_url() . '/wp-json/clubcloud/jwt';

		$current_user     = wp_get_current_user();
		$user_name        = $current_user ? $current_user->display_name : null;
		$avatar_url       = $this->getAvatar( $current_user );
		$has_subscription = $video_server_endpoint === $this->endpoints->get_subscribed_video_endpoint();

		return <<<EOT
            <div
                class="clubcloud-video-app"
                data-embedded="true"
                data-room-name="${room_name}"
                data-map-id="${map_id}"
                data-video-server-endpoint="${video_server_endpoint}"
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
                data-has-subscription="${has_subscription}"
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
