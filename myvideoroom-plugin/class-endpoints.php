<?php
/**
 * Manages endpoints for external services
 *
 * @package MyVideoRoomPlugin
 */

declare(strict_types=1);

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
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Simple check for dev mode, does not need extra security checks.
		$dev_node = ( $_GET['dev'] ?? false ) === 'true';

		$this->video_endpoint = 'meet.' . get_option( Plugin::SETTING_SERVER_DOMAIN );

		if ( $dev_node ) {
			$this->app_endpoint     = 'http://localhost:3000';
			$this->state_endpoint   = 'http://localhost:4001';
			$this->rooms_endpoint   = 'http://localhost:4002';
			$this->licence_endpoint = 'http://localhost:4003';
		} else {

			$this->app_endpoint     = 'https://app.' . get_option( Plugin::SETTING_SERVER_DOMAIN );
			$this->state_endpoint   = 'https://state.' . get_option( Plugin::SETTING_SERVER_DOMAIN );
			$this->rooms_endpoint   = 'https://rooms.' . get_option( Plugin::SETTING_SERVER_DOMAIN );
			$this->licence_endpoint = 'https://licence.' . get_option( Plugin::SETTING_SERVER_DOMAIN );
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
