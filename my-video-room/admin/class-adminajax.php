<?php
/**
 * Manages Ajax in the admin pages
 *
 * @package my-video-room/admin/class-adminajax.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Admin;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\Maintenance;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoControllers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class AdminAjax
 */
class AdminAjax {

	const DELETE_APPROVED = 'delete-approved';

	/**
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function init() {

		// Enqueue Script Ajax Handling.
		\wp_register_script(
			'mvr-admin-ajax-js',
			\plugins_url( '/js/mvradminajax.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version() . \wp_rand( 40, 30000 ),
			true
		);
		\wp_enqueue_script( 'mvr-admin-ajax-js' );
		// Localize script Ajax Upload.
		$script_data_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'mvr_admin_ajax' ),

		);

		wp_localize_script(
			'mvr-admin-ajax-js',
			'myvideoroom_admin_ajax',
			$script_data_array
		);
		wp_enqueue_script( 'myvideoroom-protect-input' );

	}

	/** MyVideoRoom Admin Ajax Support.
	 * Handles ajax calls from backend wp-admin pages
	 *
	 * @return mixed
	 */
	public function myvideoroom_admin_ajax_handler() {
		$response            = array();
		$response['message'] = 'No Change';

		// Security Checks.
		check_ajax_referer( 'mvr_admin_ajax', 'security', false );

		$action_taken = Factory::get_instance( Ajax::class )->get_string_parameter( 'action_taken' );
		$action_state = Factory::get_instance( Ajax::class )->get_string_parameter( 'state' );
		$module       = Factory::get_instance( Ajax::class )->get_string_parameter( 'module' );
		$input_type   = Factory::get_instance( Ajax::class )->get_string_parameter( 'inputType' );

		switch ( $action_taken ) {
			/*
			* Core View Display.
			*
			*/
			case 'core':
				$room_id = (int) Factory::get_instance( Ajax::class )->get_integer_parameter( 'roomId' );
				// Case Room Render for Reception Shortcode.
				if ( MVRSiteVideo::RECEPTION_ROOM_FLAG === $input_type ) {
					$response['mainvideo'] = Factory::get_instance( MVRSiteVideoControllers::class )->site_videoroom_host_function( $room_id, true );

				} elseif ( SiteDefaults::USER_ID_SITE_DEFAULTS === \intval( $room_id ) && MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $input_type ) {
					$response['mainvideo'] = ( require __DIR__ . '/../module/sitevideo/views/admin/view-settings-conference-center-default.php' )();

				} else {
					$room_object           = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
					$response['mainvideo'] = ( require __DIR__ . '/../module/sitevideo/views/admin/view-management-rooms.php' )( $room_object, $input_type );
				}
				return \wp_send_json( $response );

			/*
			* Activate/Deactivate Module.
			*
			*/
			case 'update_module':
				$button = Factory::get_instance( ModuleConfig::class )->module_activation_button( intval( $module ), $action_state );

				$response['button'] = $button;

				return \wp_send_json( $response );

			/*
			* Update User Tab Name.
			*
			*/
			case 'update_user_tab_name':
				if ( isset( $_POST['user_tab_name'] ) ) {
					$user_tab_name = sanitize_text_field( wp_unslash( $_POST['user_tab_name'] ) );
				}
				if ( strlen( $user_tab_name ) >= 5 ) {
					\update_option( 'myvideoroom-buddypress-user-tab', $user_tab_name );
					$response['feedback'] = \esc_html__( 'Setting Saved', 'myvideoroom' );
				} else {
					$response['feedback'] = \esc_html__( 'Tab Name Invalid', 'myvideoroom' );
				}

				return \wp_send_json( $response );

			/*
			* Update Group Tab Name.
			*
			*/
			case 'update_group_tab_name':
				if ( isset( $_POST['group_tab_name'] ) ) {
					$group_tab_name = sanitize_text_field( wp_unslash( $_POST['group_tab_name'] ) );
				}
				if ( strlen( $group_tab_name ) >= 5 ) {
					\update_option( 'myvideoroom-buddypress-group-tab', $group_tab_name );
					$response['feedback'] = \esc_html__( 'Setting Saved', 'myvideoroom' );
				} else {
					$response['feedback'] = \esc_html__( 'Tab Name Invalid', 'myvideoroom' );
				}

				return \wp_send_json( $response );

			/*
			* Check Slug is Available. (Used by Room URL Change Functions)
			*
			*/
			case 'check_slug':
				$slug_pre_san = Factory::get_instance( Ajax::class )->get_string_parameter( 'slug' );
				$slug         = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );

				$path_object = \get_page_by_path( $slug );

				if ( $path_object ) {
					$response['available'] = false;
					$response['input']     = $slug;
				} else {
					$response['available'] = true;
					$response['input']     = $slug;
				}
				return \wp_send_json( $response );

			/*
			* Update Room Slug.
			*
			*/
			case 'update_slug':
				$slug_pre_san = Factory::get_instance( Ajax::class )->get_string_parameter( 'slug' );
				$slug         = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );
				$post_id      = Factory::get_instance( Ajax::class )->get_string_parameter( 'post_id' );

				if ( strlen( $slug ) >= 3 ) {
					$update_array = array(
						'ID'        => intval( $post_id ),
						'post_name' => $slug,
					);
					\wp_update_post( $update_array );
					$response['maintable'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
					if ( Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME ) ) {
						$response['personalmeeting'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
					}
					if ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
						$response['conference'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
					}

					$response['feedback'] = \esc_html__( 'Saved', 'myvideoroom' );
				} else {
					$response['feedback'] = \esc_html__( 'Slug Update Failed', 'myvideoroom' );
				}

				return \wp_send_json( $response );

			/*
			* Update Room Name.
			*
			*/
			case 'update_name':
				$room_name = Factory::get_instance( Ajax::class )->get_string_parameter( 'room_name' );
				$post_id   = intval( Factory::get_instance( Ajax::class )->get_string_parameter( 'post_id' ) );

				if ( strlen( $room_name ) >= 3 ) {
					$update_array = array(
						'ID'         => $post_id,
						'post_title' => $room_name,
					);
					\wp_update_post( $update_array, true );
					Factory::get_instance( RoomMap::class )->update_room_display_name( $room_name, $post_id );
					$response['maintable'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
					if ( Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME ) ) {
						$response['personalmeeting'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
					}
					if ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
						$response['conference'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
					}

					$response['feedback'] = \esc_html__( 'Saved', 'myvideoroom' );
				} else {
					$response['feedback'] = \esc_html__( 'Name Update Failed', 'myvideoroom' );
				}

				return \wp_send_json( $response );

			/*
			* Refresh Room Tables - Used after module activation button changes module states and availability.
			*
			*/
			case 'refresh_tables':
				$response['conference'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
				$response['maintable']  = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
				if ( Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME ) ) {
					$response['personalmeeting'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
				}
				return \wp_send_json( $response );

			/*
			* Add a New Room - Used To add Site Conference Rooms.
			*
			*/
			case 'add_new_room':
				$slug_pre_san  = Factory::get_instance( Ajax::class )->get_string_parameter( 'slug' );
				$room_slug     = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );
				$display_title = Factory::get_instance( Ajax::class )->get_string_parameter( 'display_title' );

				if ( \strlen( $room_slug ) < 3 || \strlen( $display_title ) < 3 ) {
					$response['feedback'] = esc_html__( 'Input is too short ', 'myvideoroom' );
					return \wp_send_json( $response );
				}

				Factory::get_instance( RoomAdmin::class )->create_and_check_sitevideo_page(
					strtolower( str_replace( ' ', '-', trim( $display_title ) ) ),
					$display_title,
					$room_slug,
					MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
					MVRSiteVideo::SHORTCODE_SITE_VIDEO
				);

				$response['maintable'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
				if ( Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME ) ) {
					$response['personalmeeting'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
				}
				if ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
					$response['conference'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
				}
				return \wp_send_json( $response );

			/*
			* Delete Pre Confirmation.
			*
			*/
			case 'delete_room':
				$room_id   = (int) Factory::get_instance( Ajax::class )->get_integer_parameter( 'roomId' );
				$room_name = Factory::get_instance( Ajax::class )->get_string_parameter( 'roomName' );
				$nonce     = Factory::get_instance( Ajax::class )->get_string_parameter( 'nonce' );
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
				$confirmation_approved = Factory::get_instance( MVRSiteVideoViews::class )->basket_nav_bar_button( self::DELETE_APPROVED, esc_html__( 'Delete ', 'myvideoroom' ) . $room_name, null, $approved_nonce, null, null, strval( $room_id ) );
				$response['mainvideo'] = Factory::get_instance( MVRSiteVideoViews::class )->shortcode_confirmation( $message, $confirmation_approved );
				return \wp_send_json( $response );

			/*
			* Delete Post Confirmation.
			*
			*/
			case 'delete_approved':
				$nonce   = Factory::get_instance( Ajax::class )->get_string_parameter( 'nonce' );
				$room_id = (int) Factory::get_instance( Ajax::class )->get_integer_parameter( 'roomId' );
				$verify  = \wp_verify_nonce( $nonce, $room_id . self::DELETE_APPROVED );

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

				$response['conference'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
				$response['maintable']  = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
				if ( Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME ) ) {
					$response['personalmeeting'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
				}
				return \wp_send_json( $response );

			/*
			* Update Maintenance Settings.
			*
			*/
			case 'save_maintenance_settings':
				$template_update = Factory::get_instance( Ajax::class )->get_string_parameter( 'template_update' );

				if ( $template_update ) {
					Factory::get_instance( AvailableScenes::class )->update_templates();
					$response['updated'] = \esc_html__( 'Last Updated: ', 'myvideoroom' ) . gmdate( 'Y-m-d H:i:s', intval( get_option( Maintenance::OPTION_LAST_TEMPLATE_SYNCC ) ) );
					return \wp_send_json( $response );
				}
				// Listeners Hook into this filter to pick up Ajax post and process in own module.
				$response = \apply_filters( 'myvideoroom_maintenance_result_listener', $response );
				return \wp_send_json( $response );

		}
		die();
	}
}
