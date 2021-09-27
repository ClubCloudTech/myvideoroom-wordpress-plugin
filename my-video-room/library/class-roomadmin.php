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
use MyVideoRoomPlugin\Entity\RoomSync;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as DAOSecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference as ShortcodeUserVideoPreference;
use MyVideoRoomPlugin\SiteDefaults;

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
	 * Room Change Heartbeat - Returns The Room Configuration Object if Room Layout has changed.
	 *
	 * @param string $room_name The name of the room.
	 * @param string $cart_id The ID of the User Making the Request.
	 *
	 * @return ?UserVideoPreferenceEntity
	 */
	public function security_change_heartbeat( string $room_name, string $cart_id = null ) {

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
		$room_object = Factory::get_instance( DAOSecurityVideoPreference::class )->get_by_id( $room_owner_id, $room_name );

		if ( $room_object ) {
			$room_last_changed = $room_object->get_timestamp();
		} else {
			return null;
		}

		if ( $room_last_changed > $my_timestamp ) {
			return Factory::get_instance( UserVideoPreference::class )->get_by_id( $room_owner_id, $room_name );
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
	 * Update Main Video Window - Redraws the Meeting via Ajax and returns a new room.
	 *
	 * @param UserVideoPreferenceEntity $room_object - the object class to re-assemble room from.
	 * @param string                    $original_room_name - the original room name rendered in the room.
	 *
	 * @return string
	 */
	public function update_main_video_window( UserVideoPreferenceEntity $room_object, string $original_room_name ) {

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
			->set_original_room_name( $room_name )
			->set_layout( $video_template );

		$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );

		if ( $host_status ) {
			$myvideoroom_app->set_as_host();

		}

		return do_shortcode( $myvideoroom_app->output_shortcode_text() );

	}

	/**
	 * Renders a Pre-Entry Room for Users to perform a soundcheck.
	 *
	 * $original_room_name - the original room name rendered in the room.
	 *
	 * @return string
	 */
	public function render_guest_soundcheck() {

		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( SiteDefaults::SOUNDCHECK_ROOM_NAME )
			->set_original_room_name( SiteDefaults::SOUNDCHECK_ROOM_NAME )
			->disable_reception()
			->disable_floorplan();

		return do_shortcode( $myvideoroom_app->output_shortcode_text() );

	}

	/**
	 * Update Video Settings - Returns The Updated Video Settings Page on Ajax.
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

	/**
	 * Room Picture and Name Update - changes Avatar Picture and sets User Meeting Display Name.
	 *
	 * @param string $cart_id The ID of the User Making the Request.
	 * @param string $file_path The Display Name the User wants to use.
	 * @param string $file_url The Display Name the User wants to use.
	 * @param string $display_name The Display Name the User wants to use.
	 *
	 * @return bool
	 */
	public function room_picture_name_update( string $cart_id = null, string $file_path = null, string $file_url = null, string $display_name = null ): bool {

		if ( ! $cart_id ) {
			$cart_id = $this->get_user_session();
		}
		$room_name      = MVRSiteVideo::USER_STATE_INFO;
		$current_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $cart_id, $room_name );
		if ( ! $current_object ) {
			$current_object = Factory::get_instance( RoomSyncDAO::class )->create_new_user_storage_record();
		}
		if ( $file_path && $file_url ) {

			$current_object->set_user_picture_url( $file_url );
			$current_object->set_user_picture_path( $file_path );
		}

		if ( $display_name ) {
			$current_object->set_user_display_name( $display_name );
		}

		$return = Factory::get_instance( RoomSyncDAO::class )->update( $current_object );
		if ( $return ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Room User Settings Return - provides an object with User Name and pictures.
	 *
	 * @param string $avatar_url Avatar URL of the User (if any).
	 * @param string $display_name The Display Name the User wants to use.
	 *
	 * @return bool
	 */
	public function room_user_settings( string $avatar_url = null, string $display_name = null ) {
		// Setup Data.
		$cart_id                = $this->get_user_session();
		$room_name              = MVRSiteVideo::USER_STATE_INFO;
		$user_preference_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $cart_id, $room_name );
		$output_array           = array();

		if ( $user_preference_object ) {
			$picture_path      = $user_preference_object->get_user_picture_path();
			$picture_url       = $user_preference_object->get_user_picture_url();
			$user_display_name = $user_preference_object->get_user_display_name();
		}
		// Data Clean Up (In case Image has been deleted from Uploads Folder by other plugin or user).

		if ( isset( $picture_path ) && ! file_exists( $user_preference_object->get_user_picture_path() ) ) {
			$user_preference_object->set_user_picture_path( null );
			$user_preference_object->set_user_picture_url( null );
			Factory::get_instance( RoomSyncDAO::class )->update( $user_preference_object );
			return 'file doesnt exist';
		}
		// return $user_preference_object;
		// Logged Out.
		if ( ! \is_user_logged_in() ) {
			$output_array['display-name'] = $user_display_name;
			$output_array['picture-url']  = $picture_url;

			// Logged in Picture.
		} else {
			if ( isset( $picture_url ) ) {
				// Try Stored User Image First.
				$output_array['picture-url'] = $picture_url;

				// Try User Profile URL Second.
			} elseif ( isset( $avatar_url ) ) {
				$output_array['picture-url'] = $avatar_url;

				// No Picture.
			} else {
				$output_array['picture-url'] = null;
			}

			if ( isset( $user_display_name ) ) {
				$output_array['display-name'] = $user_display_name;

				// Try Logged in User Name Second.
			} elseif ( isset( $display_name ) ) {
				$output_array['display-name'] = $display_name;
			}
		}

		return $output_array;
	}


}
