<?php
/**
 * Gets details about a room
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\DAO\UserVideoPreference;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference as ShortcodeUserVideoPreference;


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
	 * Room Change Heartbeat - Returns The Room Configuration Object if Room Layout has changed.
	 *
	 * @param string $room_name The name of the room.
	 * @param string $cart_id The ID of the User Making the Request.
	 *
	 * @return ?UserVideoPreferenceEntity
	 */
	public function room_change_heartbeat( string $room_name, string $cart_id = null ) {

		if ( ! $cart_id ) {
			$cart_id = $this->get_user_session();
		}

		// Users Entered Room Timestamp and recorded Room Owner.
		$room_record = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $cart_id, $room_name );

		if ( $room_record ) {

			$my_timestamp  = $room_record->get_timestamp();
			$room_owner_id = $room_record->get_owner_id();
			if ( ! $room_owner_id || ! $my_timestamp ) {
				return null;
			}
		} else {
			return null;
		}

		// Rooms last updated timestamp, and object info.
		$room_object = Factory::get_instance( UserVideoPreference::class )->get_by_id( $room_owner_id, $room_name );

		if ( $room_object ) {
			$room_last_changed = $room_object->get_timestamp();
		} else {
			return null;
		}

		if ( $room_last_changed > $my_timestamp ) {
			return $room_object;
		} else {
			return null;
		}
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
	/**
	 * Room Change Heartbeat - Returns The Room Configuration Object if Room Layout has changed.
	 *
	 * @param UserVideoPreferenceEntity $room_object - the object class to re-assemble room from.
	 * @param string $room_name - the original room name rendered in the room.
	 *
	 * @return string
	 */
	public function update_main_video_window( UserVideoPreferenceEntity $room_object, string $original_room_name ) {

		//return serialize( $room_object );

		$user_id                 = $room_object->get_user_id();
		$room_name               = $room_object->get_room_name();
		$video_template          = $room_object->get_layout_id();
		$reception_id            = $room_object->get_reception_id();
		$reception_enabled       = $room_object->is_reception_enabled();
		$reception_video_enabled = $room_object->is_reception_video_enabled();
		$reception_video_url     = $room_object->get_reception_video_url_setting();
		$show_floorplan          = $room_object->is_floorplan_enabled();

		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( $original_room_name )
			->set_layout( $video_template );

		$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );

		if ( $host_status ) {
			$myvideoroom_app->set_as_host();

		} else {

		}
		return do_shortcode( $myvideoroom_app->output_shortcode_text() );

	}

	/**
	 * Update Security Settings - Returns The Room Security Settings Page on Ajax.
	 *
	 * @param UserVideoPreferenceEntity $room_object - the object class to re-assemble room from.
	 *
	 * @return string
	 */
	public function update_video_settings_window( UserVideoPreferenceEntity $room_object ) {

		$user_id   = $room_object->get_user_id();
		$room_name = $room_object->get_room_name();

		return \do_shortcode(
			Factory::get_instance( ShortcodeUserVideoPreference::class )->choose_settings(
				$user_id,
				$room_name
			)
		);

	}

	/**
	 * Update Security Settings - Returns The Room Security Settings Page on Ajax.
	 *
	 * @param UserVideoPreferenceEntity $room_object - the object class to re-assemble room from.
	 *
	 * @return string
	 */
	public function update_security_settings_window( UserVideoPreferenceEntity $room_object ) {

		$user_id   = $room_object->get_user_id();
		$room_name = $room_object->get_room_name();

		return \do_shortcode(
			Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
				$user_id,
				$room_name
			)
		);

	}

}
