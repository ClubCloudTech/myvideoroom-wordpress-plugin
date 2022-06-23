<?php
/**
 * Get the available scenes from the MyVideoRoom rooms server
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Factory;

/**
 * Class Available Scenes
 */
class AvailableScenes {
	const OPTION_MVR_SCENE_LAYOUTS     = 'myvideoroom-scene-layouts';
	const OPTION_MVR_RECEPTION_LAYOUTS = 'myvideoroom-reception-layouts';
	const OPTION_MVR_RECEPTION_URI     = 'receptions';
	const OPTION_MVR_SCENES_URI        = 'layouts';
	const CACHE_TIME_TOLERANCE         = 24 * 60 * 60; // Time is 24 hours for cache.

	/**
	 * Get a list of available layouts from MyVideoRoom
	 *
	 * @return array
	 */
	public function get_available_layouts(): array {
		return \apply_filters( 'myvideoroom_available_layouts', $this->get_available_layout_templates( self::OPTION_MVR_SCENE_LAYOUTS ) );
	}

	/**
	 * Get a list of available receptions from MyVideoRoom
	 *
	 * @return array
	 */
	public function get_available_receptions(): array {
		return \apply_filters( 'myvideoroom_available_receptions', $this->get_available_layout_templates( self::OPTION_MVR_RECEPTION_LAYOUTS ) );
	}

	/**
	 * Get a list of available Templates from MyVideoRoom
	 *
	 * @param string $template_type - the type of template to retrieve from MVR servers.
	 * @return array
	 */
	private function get_available_layout_templates( string $template_type ): array {

		$cache_is_recent = $this->is_cache_recent();
		$scenes          = \get_option( $template_type );

		if ( $scenes && $cache_is_recent ) {
			return $scenes;
		} else {
			$this->update_templates();
			$scenes = \get_option( $template_type );
		}

		if ( $scenes ) {
			return $scenes;
		} else {
			return array();
		}
	}

	/**
	 * Update Any Room Template from MyVideoRoom Servers and commit to Options Cache.
	 *
	 * @param string $uri - the path of the endpoint to check.
	 * @param string $option_name - the name of the option to update in local wp options cache.
	 * @return bool
	 */
	private function update_from_myvideoroom_servers( string $uri, string $option_name ): bool {

		$url  = Factory::get_instance( Endpoints::class )->get_rooms_endpoint() . '/' . $uri;
		$host = Factory::get_instance( Host::class )->get_host();

		if ( $host ) {
			$url = \add_query_arg( array( 'host' => $host ), $url );
		}

		$request = \wp_remote_get( $url );

		if ( \is_wp_error( $request ) ) {
			return false;
		}

		$body = \wp_remote_retrieve_body( $request );

		$scenes = \json_decode( $body );

		if ( $scenes ) {
			$timestamp = \current_time( 'timestamp' );
			\update_option( Maintenance::OPTION_LAST_TEMPLATE_SYNCC, $timestamp );
			\update_option( $option_name, $scenes );
			return true;
		}

		return false;

	}

	/**
	 * Get a list of available receptions from MyVideoRoom
	 *
	 * @return void
	 */
	public function update_templates():void {

		$this->update_from_myvideoroom_servers( self::OPTION_MVR_RECEPTION_URI, self::OPTION_MVR_RECEPTION_LAYOUTS );
		$this->update_from_myvideoroom_servers( self::OPTION_MVR_SCENES_URI, self::OPTION_MVR_SCENE_LAYOUTS );

		$timestamp = \current_time( 'timestamp' );
		\update_option( Maintenance::OPTION_LAST_TEMPLATE_SYNCC, $timestamp );
	}

	/**
	 * Determines whether layouts are recent in cache time (set as a constant in top of class).
	 *
	 * @return bool
	 */
	private function is_cache_recent(): bool {

		$current_time    = \current_time( 'timestamp' );
		$cache_tolerance = self::CACHE_TIME_TOLERANCE;
		$last_updated    = \get_option( Maintenance::OPTION_LAST_TEMPLATE_SYNCC );

		if ( $current_time < ( $last_updated + $cache_tolerance ) ) {
			return true;
		}
		return false;
	}
}
