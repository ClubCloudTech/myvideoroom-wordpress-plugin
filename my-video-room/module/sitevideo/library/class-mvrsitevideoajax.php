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
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoAjax {

	/**
	 * Site Video Support for Ajax Settings.
	 *
	 * @return void
	 */
	public function get_ajax_page_settings() {

		$room_id    = (int) Factory::get_instance( Ajax::class )->get_text_parameter( 'roomId' );
		$input_type = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );

		// Case Room Render for Reception Shortcode.

		if ( MVRSiteVideo::RECEPTION_ROOM_FLAG === $input_type ) {
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped.
			echo Factory::get_instance( MVRSiteVideoControllers::class )->site_videoroom_host_function( $room_id );

		} elseif ( SiteDefaults::USER_ID_SITE_DEFAULTS === \intval( $room_id ) && MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $input_type ) {
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( require __DIR__ . '/views/view-settings-conference-center-default.php' )();

		} else {
			$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( require __DIR__ . '/views/view-management-rooms.php' )( $room_object, $input_type );
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
			$response['message'] = \esc_html('To begin Soundcheck click on any open seat', 'myvideoroom');
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
				return null;
			}
			$session = 'tmp-' . $user_session . '.png';

			// Delete Existing File in Uploads directory if exists.
			$delete = $this->get_current_directory() . '/' . $session;
			\wp_delete_file( $delete );

			// Process Upload.
			if ( $temp_name ) {
				//phpcs:ignore -- WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				$upload = \wp_upload_bits( $session, null, file_get_contents( $_FILES['upimage']['tmp_name'] ) );

				$return = Factory::get_instance( RoomAdmin::class )->room_picture_name_update( $room_name, $user_session, $upload['file'], $upload['url'] );
				if ( $return ) {
					$response['message'] = 'Picture Update Success';
				} else {
					$response['message'] = 'Picture Update Failed';
				}
			}
			return \wp_send_json( $response );
		}

		if ( $display_name ) {
			$display_updated = Factory::get_instance( RoomAdmin::class )->room_picture_name_update( $room_name, $user_session, null, null, $display_name );
		}

		// Start Meeting Section.
		if ( 'start_meeting' === $action_taken && $display_updated ) {
			$session_id    = Factory::get_instance( RoomAdmin::class )->get_user_session();
			$room_record   = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $session_id, $room_name );
			$room_owner_id = $room_record->get_owner_id();
			$room_object   = Factory::get_instance( UserVideoPreference::class )->get_by_id( $room_owner_id, $room_name );

			$response['mainvideo'] = Factory::get_instance( RoomAdmin::class )->update_main_video_window( $room_object, $original_room );

			return \wp_send_json( $response );
		}
		die();
	}
	/** File Directory Support Aseembly.
	 * Returns current file name of upload directory.
	 *
	 * @return string
	 */
	private function get_current_directory() {
		$current_upload_object = wp_get_upload_dir();
		return $current_upload_object['basedir'] . $current_upload_object['subdir'];
	}
}
