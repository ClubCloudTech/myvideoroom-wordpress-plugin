<?php
/**
 * Manages Ajax in the admin pages
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Admin;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;

/**
 * Class AdminAjax
 */
class AdminAjax {

	/**
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function init() {

		// Enqueue Script Ajax Handling.
		\wp_enqueue_script(
			'mvr-admin-ajax-js',
			\plugins_url( '/js/mvradminajax.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version() . \wp_rand( 40, 30000 ),
			true
		);
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

		if ( isset( $_POST['action_taken'] ) ) {
			$action_taken = sanitize_text_field( wp_unslash( $_POST['action_taken'] ) );
		}
		if ( isset( $_POST['state'] ) ) {
			$action_state = sanitize_text_field( wp_unslash( $_POST['state'] ) );
		}
		if ( isset( $_POST['module'] ) ) {
			$module = sanitize_text_field( wp_unslash( $_POST['module'] ) );
		}

		/*
		* Activate/Deactivate Module.
		*
		*/
		if ( 'update_module' === $action_taken ) {

			$button = Factory::get_instance( ModuleConfig::class )->module_activation_button( intval( $module ), $action_state );

				$response['button'] = $button;

			return \wp_send_json( $response );
		}

		/*
		* Update User Tab Name.
		*
		*/
		if ( 'update_user_tab_name' === $action_taken ) {
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
		}

		/*
		* Update Group Tab Name.
		*
		*/
		if ( 'update_group_tab_name' === $action_taken ) {
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
		}

		/*
		* Check Slug is Available. (Used by Room URL Change Functions)
		*
		*/
		if ( 'check_slug' === $action_taken ) {
			if ( isset( $_POST['slug'] ) ) {
				$slug = sanitize_text_field( wp_unslash( $_POST['slug'] ) );
			}

			$path_object = \get_page_by_path( $slug );

			if ( $path_object ) {
				$response['available'] = false;
				$response['input'] = $slug;
			} else {
				$response['available'] = true;
				$response['input'] = $slug;
			}
			return \wp_send_json( $response );
		}

		/*
		* Update Room Slug.
		*
		*/
		if ( 'update_slug' === $action_taken ) {
			if ( isset( $_POST['slug'] ) ) {
				$slug_pre_san = sanitize_text_field( wp_unslash( $_POST['slug'] ) );
				$slug         = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );
			}
			if ( isset( $_POST['post_id'] ) ) {
				$post_id = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
			}
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
		}

		/*
		* Refresh Room Tables - Used after module activation button changes module states and availability.
		*
		*/
		if ( 'refresh_tables' === $action_taken ) {
			$response['conference'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRSiteVideo::ROOM_NAME_SITE_VIDEO );
			$response['maintable']  = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
			if ( Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME ) ) {
				$response['personalmeeting'] = Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
			}
			return \wp_send_json( $response );
		}

		/*
		* Add a New Room - Used To add Site Conference Rooms.
		*
		*/
		if ( 'add_new_room' === $action_taken ) {
			if ( isset( $_POST['slug'] ) ) {
				$slug_pre_san = sanitize_text_field( wp_unslash( $_POST['slug'] ) );
				$room_slug    = strtolower( str_replace( ' ', '-', trim( $slug_pre_san ) ) );
			}
			if ( isset( $_POST['display_title'] ) ) {
				$display_title = sanitize_text_field( wp_unslash( $_POST['display_title'] ) );
			}
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
		}

		die();
	}
}
