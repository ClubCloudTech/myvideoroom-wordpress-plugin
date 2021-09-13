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
use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;

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
	 * Room Change Heartbeat - Returns if Room Layout has changed.
	 *
	 * @param string $room_name The name of the room.
	 *
	 * @return UserVideoPreferenceEntity
	 */
	public function room_change_heartbeat( string $room_name ): ?UserVideoPreferenceEntity {
		/*
		$current_time     = \current_time( 'timestamp' );
		$tolerance        = SiteDefaults::ROOM_REFRESH_TOLERANCE;
		$room_preference  =
		$room_last_update = */
		return null;
	}

}
