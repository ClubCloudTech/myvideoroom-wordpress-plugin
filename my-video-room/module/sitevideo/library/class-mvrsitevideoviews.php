<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoViews {

	// ---
	// Site Video Room Templates.

	/**
	 * Render Site Videoroom Host Template Controllers
	 * These functions prepare information for delivery to templates and views.
	 *
	 * @param int $room_id The room ID.
	 *
	 * @return array|string
	 */
	public function site_videoroom_host_template( int $room_id ) {
		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

		$display_name = '';
		$room_name    = '';

		if ( $room_object ) {
			$display_name = $room_object->display_name;
			$room_name    = $room_object->room_name;
		}

		$module_suffix = ' ' . MVRSiteVideo::MODULE_SITE_VIDEO_DESCRIPTION;
		$module_id     = $display_name;
		$render        = require __DIR__ . '/../../../views/header/view-roomheader.php';
		$name_output   = esc_html__( 'Hosting ', 'my-video-room' );
		$is_guest      = false;
		$meeting_link  = Factory::get_instance( RoomAdmin::class )->get_room_url( $room_name );

		return $render( $module_id, $name_output, $room_id, $room_name, $is_guest, $meeting_link, $module_suffix );

	}

	/**
	 * Render Site Video Guest Template
	 *
	 * @param int $host_id ID of Host for calculating Headers.
	 *
	 * @return array|string
	 */
	public function site_videoroom_guest_template( int $host_id ) {
		$room_object   = Factory::get_instance( RoomMap::class )->get_room_info( $host_id );
		$display_name  = $room_object->display_name;
		$room_name     = $room_object->room_name;
		$module_suffix = ' ' . MVRSiteVideo::MODULE_SITE_VIDEO_DESCRIPTION;
		$module_id     = $display_name;
		$render        = require __DIR__ . '/../../../views/header/view-roomheader.php';
		$name_output   = esc_html__( 'Visiting ', 'my-video-room' );
		$is_guest      = true;
		$meeting_link  = Factory::get_instance( RoomAdmin::class )->get_room_url( $room_name );

		return $render( $module_id, $name_output, $host_id, $room_name, $is_guest, $meeting_link, $module_suffix );

	}

	/**
	 * Room Type Friendly Name
	 *
	 * @param string $room_type .
	 *
	 * @return string name.
	 */
	public function conference_room_friendly_name( string $room_type ): string {
		switch ( $room_type ) {
			case MVRSiteVideo::ROOM_NAME_SITE_VIDEO:
				if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
					return esc_html__( 'Module Disabled', 'myvideoroom' );
				} else {
					return esc_html__( 'Conference Center Room', 'myvideoroom' );
				}
		}

		return $room_type;
	}

	/**
	 * Generate Room Table
	 *
	 * @param string $room_type ? all rooms.
	 * @param bool   $shortcode ? whether its a shortcode call.
	 *
	 *  @return string table.
	 */
	public function generate_room_table( string $room_type = null, bool $shortcode = false ): string {
		// Returns all rooms with null roomtype, or a specific room with Room Type.
		$rooms = Factory::get_instance( MVRSiteVideoRoomHelpers::class )->get_rooms( $room_type );
		return ( require __DIR__ . '/../views/table-output.php' )( $rooms, $room_type, $shortcode );
	}

	/**
	 * Generate Login Function.
	 *
	 * @param array $input       - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_login_tab_welcome( array $input ): array {

		$basket_menu = new MenuTabDisplay(
			\esc_html__( 'Login', 'myvideoroom' ),
			'templatelogin',
			fn() => $this->render_login_page()
		);

		array_push( $input, $basket_menu );
		return $input;

	}

	/**
	 * Generate Reception Function.
	 *
	 * @param array $input       - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_reception_tab_welcome( array $input, int $user_id = null, string $room_name = null, bool $host_status = null, array $header = null, bool $ajax_flag = null ): array {
		if ( $ajax_flag ) {
			return $input;
		}

		$basket_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_INFO_RECEPTION ),
			'receptioncenter',
			fn() => Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_site_conference_page( true )
		);

		array_push( $input, $basket_menu );
		return $input;

	}

	/**
	 * Render Login Page
	 *
	 * @param string $room_name The Room name.
	 * @return string - Login Page.
	 */
	public function render_login_page(): string {
		\wp_enqueue_script( 'myvideoroom-iframe-handler' );
		$login_override  = get_option( 'myvideoroom-login-override' );
		$login_shortcode = get_option( 'myvideoroom-login-shortcode' );
		$redirect_url    = Factory::get_instance( RoomAdmin::class )->get_room_url( MVRSiteVideo::ROOM_NAME_REDIRECT, true ) . '/';
		$render          = require __DIR__ . '/../views/login/view-login.php';
		return $render( \boolval( $login_override ), $login_shortcode, $redirect_url );

	}

	/**
	 * Render Picture Page
	 *
	 * @return string - Welcome Picture Page.
	 */
	public function render_picture_page(): string {

		wp_enqueue_script( 'myvideoroom-protect-username' );
		$user_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$room_name    = MVRSiteVideo::USER_STATE_INFO;
		$user_info    = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $user_session, $room_name );

		// Check for Blank Record of new user and create record.
		if ( ! $user_info ) {
				$user_info = Factory::get_instance( RoomSyncDAO::class )->create_new_user_storage_record();
		}

		// Check Logged in user Profile Picture or Display Name.
		if ( \is_user_logged_in() ) {
			$current_user = \wp_get_current_user();

			if ( $user_info && ! $user_info->get_user_picture_url() ) {
				$avatar = \get_avatar_url( $current_user );
				$user_info->set_user_picture_url( $avatar );
			}

			if ( $user_info && ! $user_info->get_user_display_name() ) {
				$user_display = $current_user->display_name;
				$user_info->set_user_display_name( $user_display );
			}
		}
			$render = require __DIR__ . '/../views/login/view-picture-register.php';

			return $render( $user_info );

	}

}
