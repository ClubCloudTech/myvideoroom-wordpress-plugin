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

	/**
	 * Get a list of available layouts from MyVideoRoom
	 *
	 * @return array
	 */
	public function get_available_layouts(): array {
		return \apply_filters( 'myvideoroom_available_layouts', $this->get_available_scenes( 'layouts' ) );
	}

	/**
	 * Get a list of available scenes from MyVideoRoom
	 *
	 * @param string $uri The type of scene (layouts/receptions).
	 *
	 * @return array
	 */
	private function get_available_scenes( string $uri ): array {
		$url = Factory::get_instance( Endpoints::class )->get_rooms_endpoint() . '/' . $uri;

		$host = Factory::get_instance( Host::class )->get_host();

		if ( $host ) {
			$url = \add_query_arg( array( 'host' => $host ), $url );
		}

		$request = \wp_remote_get( $url );

		if ( \is_wp_error( $request ) ) {
			return array();
		}

		$body = \wp_remote_retrieve_body( $request );

		$scenes = \json_decode( $body );

		if ( ! $scenes ) {
			return array();
		}

		return $scenes;
	}

	/**
	 * Get a list of available receptions from MyVideoRoom
	 *
	 * @return array
	 */
	public function get_available_receptions(): array {
		return \apply_filters( 'myvideoroom_available_receptions', $this->get_available_scenes( 'receptions' ) );
	}
}
