<?php
/**
 * Ajax for Site Video Room.
 *
 * @package MyVideoRoomPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\DAO\UserVideoPreference;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin as SetupRoomAdmin;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoAjax {

	const DELETE_APPROVED = 'delete-approved';

	/**
	 * Site Video Support for Ajax Settings.
	 * This function handles the tabs in conference centre pages that render rooms and config settings inside conference center backend, and reception center shortcode Ajax.
	 *
	 * @return mixed
	 */
	public function get_ajax_page_settings() {
		check_ajax_referer( 'handle_picture_upload', 'security', false );
		$room_id      = (int) Factory::get_instance( Ajax::class )->get_integer_parameter( 'roomId' );
		$input_type   = Factory::get_instance( Ajax::class )->get_string_parameter( 'inputType' );
		$action_taken = Factory::get_instance( Ajax::class )->get_string_parameter( 'action_taken' );
		$room_name    = Factory::get_instance( Ajax::class )->get_string_parameter( 'roomName' );
		$nonce        = Factory::get_instance( Ajax::class )->get_string_parameter( 'nonce' );
		$response     = array();

		switch ( $action_taken ) {
			/*
			* Core View Display.
			*
			*/
			case 'core':
				// Case Room Render for Reception Shortcode.
				if ( MVRSiteVideo::RECEPTION_ROOM_FLAG === $input_type ) {
					$response['mainvideo'] = Factory::get_instance( MVRSiteVideoControllers::class )->site_videoroom_host_function( $room_id, true );

				} elseif ( SiteDefaults::USER_ID_SITE_DEFAULTS === \intval( $room_id ) && MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $input_type ) {
					$response['mainvideo'] = ( require __DIR__ . '/../views/admin/view-settings-conference-center-default.php' )();

				} else {
					$room_object           = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
					$response['mainvideo'] = ( require __DIR__ . '/../views/admin/view-management-rooms.php' )( $room_object, $input_type );
				}
				return \wp_send_json( $response );

			/*
			* Delete Pre Confirmation.
			*
			*/
			case 'delete_room':
				if ( ! \wp_verify_nonce( $nonce, 'delete_room_' . $room_id ) || ! current_user_can( 'administrator' ) ) {
					$response['mainvideo'] = \esc_html__( 'You do not have permission to complete this operation.', 'myvideoroom' );
					return \wp_send_json( $response );
				}
				$message = sprintf(
				/* translators: %s is the message variant translated above */
					\esc_html__(
						'delete %s ? This action can not be reversed.',
						'myvideoroom'
					),
					esc_html( $room_name )
				);

				$approved_nonce        = wp_create_nonce( $room_id . self::DELETE_APPROVED );
				$confirmation_approved = Factory::get_instance( MVRSiteVideoViews::class )->basket_nav_bar_button( self::DELETE_APPROVED, esc_html__( 'Delete ', 'myvideoroom' ) . $room_name, null, $approved_nonce, null, null, $room_id );
				$response['mainvideo'] = Factory::get_instance( MVRSiteVideoViews::class )->shortcode_confirmation( $message, $confirmation_approved );
				return \wp_send_json( $response );

			/*
			* Delete Post Confirmation.
			*
			*/
			case 'delete_approved':
				$verify = \wp_verify_nonce( $nonce, $room_id . self::DELETE_APPROVED );
				if ( ! $verify || ! current_user_can( 'administrator' ) ) {
					$response['mainvideo'] = \esc_html__( 'You do not have permission to complete this operation.', 'myvideoroom' );
					return \wp_send_json( $response );
				}

				$room_check = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
				if ( ! $room_check ) {
					$response['mainvideo'] = \esc_html__( 'This Room does not Exist - please contact support', 'myvideoroom' );
				} else {
					Factory::get_instance( MVRSiteVideoRoomHelpers::class )->delete_room_and_post( $room_check );
				}

				$response['mainvideo'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO, true );
				return \wp_send_json( $response );

			/*
			* Check Slug is Available. (Used by Room URL Change Functions)
			*
			*/
			case 'check_slug':
				$slug_pre_san   = Factory::get_instance( Ajax::class )->get_string_parameter( 'slug' );
				$sanitized_slug = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );
				$path_object    = \get_page_by_path( $sanitized_slug );

				if ( $path_object ) {
					$response['available'] = false;
					$response['input']     = $sanitized_slug;
				} else {
					$response['available'] = true;
					$response['input']     = $sanitized_slug;
				}
				return \wp_send_json( $response );

				/*
				* Add a New Room From Frontend- Used To add Site Conference Rooms.
				*
				*/
			case 'add_new_room_shortcode':
				$slug_pre_san  = Factory::get_instance( Ajax::class )->get_string_parameter( 'slug' );
				$room_slug     = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );
				$display_title = Factory::get_instance( Ajax::class )->get_string_parameter( 'display_title' );

				$response['feedback'] = esc_html__( 'Saved', 'myvideoroom' );
				if ( \strlen( $room_slug ) < 3 || \strlen( $display_title ) < 3 ) {
					$response['feedback'] = esc_html__( 'Input is too short ', 'myvideoroom' );
					return \wp_send_json( $response );
				}

				Factory::get_instance( SetupRoomAdmin::class )->create_and_check_sitevideo_page(
					strtolower( str_replace( ' ', '-', trim( $display_title ) ) ),
					$display_title,
					$room_slug,
					MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
					MVRSiteVideo::SHORTCODE_SITE_VIDEO
				);
				$response['maintable'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO, true );

				return \wp_send_json( $response );

		}
		die();
	}

	/** File Upload Ajax Support.
	 * Handles Uploads from Welcome Area, sends them to storage and updates the database.
	 *
	 * @return mixed
	 */
	public function file_upload_handler() {
		$temp_name           = null;
		$response            = array();
		$response['message'] = 'No Change';

		// Security Checks.
		check_ajax_referer( 'handle_picture_upload', 'security', false );
		$user_session = Factory::get_instance( RoomAdmin::class )->get_user_session();

		if ( isset( $_POST['room_name'] ) ) {
			$room_name = sanitize_text_field( wp_unslash( $_POST['room_name'] ) );
		}
		if ( isset( $_POST['action_taken'] ) ) {
			$action_taken = sanitize_text_field( wp_unslash( $_POST['action_taken'] ) );
		}
		if ( isset( $_POST['display_name'] ) ) {
			$display_name = sanitize_text_field( wp_unslash( $_POST['display_name'] ) );
		}
		if ( isset( $_POST['original_room'] ) ) {
			$original_room = sanitize_text_field( wp_unslash( $_POST['original_room'] ) );
		}

		/*
		* Check Sound Section.
		*
		*/
		if ( 'check_sound' === $action_taken ) {
			$response['mainvideo'] = Factory::get_instance( RoomAdmin::class )->render_guest_soundcheck();
			$response['message']   = '<strong>' . \esc_html__( 'To begin Soundcheck click on any open seat', 'myvideoroom' ) . '</strong>';
			$response['message']  .= '<input id="stop-chk-sound" type="button" value="Stop Check" class="myvideoroom-welcome-buttons myvideoroom-button-override" />';
			return \wp_send_json( $response );
		}

		/*
		* Refresh Page Section.
		*
		*/
		if ( 'refresh_page' === $action_taken ) {
			$response['welcome'] = Factory::get_instance( MVRSiteVideoViews::class )->render_picture_page();

			return \wp_send_json( $response );
		}

		/*
		* Delete Me Section.
		*
		*/
		if ( 'delete_me' === $action_taken ) {
			// Process Delete.
			$room_name   = MVRSiteVideo::USER_STATE_INFO;
			$response    = array();
			$room_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $user_session, $room_name );
			if ( ! $room_object ) {
				return null;
			}
			$delete = Factory::get_instance( RoomSyncDAO::class )->delete( $room_object );
			if ( $delete ) {
				$response['feedback'] = esc_html__( 'Record Deleted', 'myvideoroom' );
			} else {
				$response['feedback'] = esc_html__( 'Record Delete Failed', 'myvideoroom' );
			}

			return \wp_send_json( $response );
		}

		/*
		* Update Picture Section.
		*
		*/
		if ( 'update_picture' === $action_taken ) {

			// Image Upload Section.
			if ( isset( $_FILES['upimage']['type'] ) && isset( $_FILES['upimage']['tmp_name'] ) ) {
				$temp_name = sanitize_file_name( wp_unslash( $_FILES['upimage']['tmp_name'] ) );
			}

			$arr_img_ext = array( 'image/png', 'image/jpeg', 'image/jpg', 'image/gif' );

			if ( isset( $_FILES['upimage']['type'] ) && ! in_array( $_FILES['upimage']['type'], $arr_img_ext, true ) ) {
				$response['feedback'] = esc_html__( 'Incorrect Attachment Type Sent', 'myvideoroom' );
				return \wp_send_json( $response );
			}
			$session = 'tmp-' . $user_session . wp_rand( 200, 20000 ) . '.png';

			// Delete Existing File in Uploads directory if exists.
			$delete_path = $this->get_current_picture_path( $user_session );

			if ( $delete_path ) {
				$delete = \wp_delete_file( $delete_path );
			}

			if ( $temp_name ) {
				//phpcs:ignore -- WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				$upload = \wp_upload_bits( $session, null, file_get_contents( $_FILES['upimage']['tmp_name'] ) );
				$return = Factory::get_instance( RoomAdmin::class )->room_picture_name_update( $upload['file'], $upload['url'] );
				if ( $return ) {
					$response['feedback'] = esc_html__( 'Picture Update Success', 'myvideoroom' );
				} else {
					$response['feedback'] = esc_html__( 'Picture Update Failed', 'myvideoroom' );
				}
			}
			return \wp_send_json( $response );
		}

		/*
		* Update Display Name section.
		*
		*/
		if ( 'update_display_name' === $action_taken ) {
			if ( $display_name && $room_name ) {
				$display_updated = Factory::get_instance( RoomAdmin::class )->room_picture_name_update( null, null, $display_name );
			}

			if ( true === $display_updated ) {
				$response['feedback'] = \esc_html__( 'Display Name Update Updated', 'myvideoroom' );
			} else {
				$response['feedback'] = \esc_html__( 'Display Name Update Failed', 'myvideoroom' );
			}
			return \wp_send_json( $response );
		}

		/*
		* Check Login.
		*
		*/
		if ( 'check_login' === $action_taken ) {

			if ( \is_user_logged_in() ) {
				$response['login'] = true;
			} else {
				$response['login'] = false;
			}
			return \wp_send_json( $response );
		}

		/*
		* Start Meeting Section..
		*
		*/
		if ( 'start_meeting' === $action_taken ) {
			$checksum  = Factory::get_instance( Ajax::class )->get_string_parameter( 'checksum' );
			$room_type = Factory::get_instance( Ajax::class )->get_string_parameter( 'roomType' );

			$session_id  = Factory::get_instance( RoomAdmin::class )->get_user_session();
			$room_record = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $session_id, $room_name );
			if ( ! $room_record ) {
				$room_object = null;
			} else {
				$room_owner_id = $room_record->get_owner_id();
				$room_object   = Factory::get_instance( UserVideoPreference::class )->get_by_id( $room_owner_id, $room_name );
			}
			$response['feedback'] = \esc_html__( 'Refreshing Video Room', 'myvideoroom' );

			if ( ! $room_object ) {
				// For Transient Rooms and SiteVideo Unset Rooms.
				$room_rebuild          = Factory::get_instance( RoomAdmin::class )->rebuild_room_record( $room_owner_id, $room_name, $room_type, $checksum, $original_room );
				$response['mainvideo'] = $room_rebuild;
			} else {
				// For SiteVideo and Rooms with set preferences in DB.
				$response['mainvideo'] = Factory::get_instance( RoomAdmin::class )->update_main_video_window( $room_object, $original_room );
			}

			return \wp_send_json( $response );
		}
		die();
	}

	/** Current Picture Path
	 * Returns current file name of upload directory.
	 *
	 * @param string $session_id the cart hash of the user.
	 * @return ?string
	 */
	private function get_current_picture_path( string $session_id ) {
		$room_name   = MVRSiteVideo::USER_STATE_INFO;
		$user_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $session_id, $room_name );
		if ( $user_object && $user_object->get_user_picture_path() ) {
			return $user_object->get_user_picture_path();
		} else {
			return null;
		}
	}
}
