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

	/**
	 * Register Room Presence
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status - If User is Host.
	 * @return void
	 */
	public function register_room_presence( string $room_name, bool $host_status ):void {
		// Setup.
		$cart_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$timestamp    = \current_time( 'timestamp' );

		$current_record = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $cart_session, $room_name, $host_status );

		if ( $current_record ) {
			$current_record->set_timestamp( $timestamp );
			$current_record->set_room_host( $host_status );

		} else {
			$current_record = new RoomSyncEntity(
				$cart_session,
				$room_name,
				$timestamp,
				$timestamp,
				$host_status,
				null,
				null,
				$host_status,
				null
			);
			// Set Last Notification Timestamp for new room.
			Factory::get_instance( HostManagement::class )->notify_user( $room_name );
		}
		Factory::get_instance( RoomSyncDAO::class )->create( $current_record );

		// Check and Clean Master Status.
		Factory::get_instance( HostManagement::class )->initialise_master_status( $room_name, $host_status );
	}

	/**
	 * Get Session ID for Cart Synchronisation.
	 *
	 * @param ?int $user_id The user id. (optional).
	 *
	 * @return string the session ID of the user.
	 */
	public function get_user_session( int $user_id = null ): string {

		if ( $user_id ) {
			return wp_hash( $user_id );

		} elseif ( is_user_logged_in() ) {

			return wp_hash( get_current_user_id() );
		} else {

			// Get php session hash.
			if ( ! session_id() ) {
				session_start();
			}
			return session_id();
		}
	}
}
