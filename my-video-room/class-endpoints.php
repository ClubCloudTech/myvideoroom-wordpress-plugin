<?php
/**
 * Manages endpoints for external services
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

/**
 * Class Endpoints
 */
class Endpoints {
	/**
	 * The endpoint for the video controller.
	 *
	 * @var string
	 */
	private string $video_endpoint;

	/**
	 * The endpoint for the front end app.
	 *
	 * @var string
	 */
	private string $app_endpoint;

	/**
	 * The endpoint for the state management server.
	 *
	 * @var string
	 */
	private string $state_endpoint;

	/**
	 * The endpoint for the rooms server
	 *
	 * @var string
	 */
	private string $rooms_endpoint;

	/**
	 * The endpoint for the licence server
	 *
	 * @var string
	 */
	private string $licence_endpoint;

	/**
	 * Endpoints constructor.
	 */
	public function __construct() {

		if ( defined( 'MYVIDEOROOM_CUSTOM_ENDPOINTS' ) ) {
			$custom_endpoints = MYVIDEOROOM_CUSTOM_ENDPOINTS;
		} else {
			$custom_endpoints = array();
		}

		$video_server = $this->get_server_endpoint();

		$this->video_endpoint   = $custom_endpoints['video'] ?? 'meet.' . $video_server;
		$this->app_endpoint     = $custom_endpoints['app'] ?? 'https://app.' . $video_server;
		$this->state_endpoint   = $custom_endpoints['state'] ?? 'https://state.' . $video_server;
		$this->rooms_endpoint   = $custom_endpoints['rooms'] ?? 'https://rooms.' . $video_server;
		$this->licence_endpoint = $custom_endpoints['licence'] ?? 'https://licence.' . $video_server;
	}

	/**
	 * Get the server endpoint
	 *
	 * @return string
	 */
	public function get_server_endpoint(): string {
		if ( esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) ) ) {
			return esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) );
		} else {
			return 'clubcloud.tech';
		}
	}

	/**
	 * Get endpoint for the video controller.
	 *
	 * @return string
	 */
	public function get_video_endpoint(): string {
		return $this->video_endpoint;
	}

	/**
	 * Get the endpoint for the front end app.
	 *
	 * @return string
	 */
	public function get_app_endpoint(): string {
		return $this->app_endpoint;
	}

	/**
	 * Get the endpoint for the state management server.
	 *
	 * @return string
	 */
	public function get_state_endpoint(): string {
		return $this->state_endpoint;
	}

	/**
	 * Get the endpoint for the rooms server
	 *
	 * @return string
	 */
	public function get_rooms_endpoint(): string {
		return $this->rooms_endpoint;
	}

	/**
	 * Get the endpoint for the licence server
	 *
	 * @return string
	 */
	public function get_licence_endpoint(): string {
		return $this->licence_endpoint;
	}

}
