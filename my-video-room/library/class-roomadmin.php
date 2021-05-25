<?php
/**
 * Gets details about a room
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Factory;

/**
 * Class RoomAdmin
 */
class RoomAdmin {

	/**
	 * Get the WordPress URL of a room
	 *
	 * @param string $room_name The name of the room.
	 *
	 * @return string
	 */
	public function get_room_url( string $room_name ): ?string {
		$post = $this->get_post( $room_name );

		// rooms which are no longer published should no longer have urls.
		if ( ! $post || 'publish' !== $post->post_status ) {
			return null;
		}

		return get_site_url() . '/' . $post->post_name . '/';
	}

	/**
	 * Get the room type by the room name
	 *
	 * @param string $room_name The name of the room.
	 *
	 * @return string
	 */
	public function get_room_type( string $room_name ): string {
		$room_post_id = Factory::get_instance( RoomMap::class )->get_post_id_by_room_name( $room_name );
		$room         = Factory::get_instance( RoomMap::class )->get_room_info( $room_post_id );
		return \apply_filters( 'myvideoroom_room_type_display_override', $room->room_type );
	}

	/**
	 * Get the WordPress post by the room name
	 *
	 * @param string $room_name The room name.
	 *
	 * @return ?\WP_Post
	 */
	public function get_post( string $room_name ): ?\WP_Post {
		$room_post_id = Factory::get_instance( RoomMap::class )->get_post_id_by_room_name( $room_name );
		return get_post( $room_post_id );
	}
}
