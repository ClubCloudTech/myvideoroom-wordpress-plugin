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
		$room_type     = MVRSiteVideo::ROOM_NAME_SITE_VIDEO;

		return $render( $module_id, $name_output, $room_id, $room_name, $is_guest, $meeting_link, $module_suffix, $room_type );

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
		$room_type     = MVRSiteVideo::ROOM_NAME_SITE_VIDEO;

		return $render( $module_id, $name_output, $host_id, $room_name, $is_guest, $meeting_link, $module_suffix, $room_type );

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
		$rooms  = Factory::get_instance( MVRSiteVideoRoomHelpers::class )->get_rooms( $room_type );
		$offset = \wp_rand();
		return ( require __DIR__ . '/../views/shared/table-output.php' )( $rooms, $room_type, $shortcode, $offset );
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
	/**
	 * Render Confirmation Pages
	 *
	 * @param string $message - Message to Display.
	 * @param string $confirmation_button_approved - Button to Display for Approved.
	 * @return string
	 */
	public function shortcode_confirmation( string $message, string $confirmation_button_approved ):string {

		$cancel_button = $this->cancel_nav_bar_button( 'cancel', esc_html__( 'Cancel', 'my-video-room' ), null, 'mvr-main-button-cancel' );

		// Render Confirmation Page View.

		$render = require __DIR__ . '/../views/shortcode/confirmation-page.php';
		return $render( $message, $confirmation_button_approved, $cancel_button );

	}

	/**
	 * Render the Basket Nav Bar Button
	 *
	 * @param  string $button_type - Feedback for Ajax Post.
	 * @param  string $button_label - Label for Button.
	 * @param  string $style - Add a class for the button (optional).
	 * @param  string $target_id - Adds a class to the button to javascript take an action on.
	 * @param  string $target_window - adds a target window element used to switch destination windows.
	 *
	 * @return string
	 */
	public function cancel_nav_bar_button( string $button_type, string $button_label, string $style = null, string $target_id = null, string $target_window = null ): string {

		$id_text = null;

		if ( $target_window ) {
			$id_text = ' data-target="' . $target_window . '" ';
		}

		$style .= ' ' . $target_id;

		return '
		<button id="' . $target_id . '" class="mvr-confirmation-cancel ' . $style . '" data-target="' . $target_window . '">
		<a data-input-type="' . $button_type . '" ' . $id_text . ' class="mvr-confirmation-cancel ' . $style . ' ">' . $button_label . '</a>
		</button>
		';
	}
	/**
	 * Render the Basket Nav Bar Button
	 *
	 * @param  string $button_type - Feedback for Ajax Post.
	 * @param  string $button_label - Label for Button.
	 * @param string $room_name -  Name of Room.
	 * @param  string $nonce - Nonce for operation (if confirmation used).
	 * @param  string $product_or_id - Adds additional Data to Nonce for more security (optional).
	 * @param  string $style - Add a class for the button (optional).
	 * @param  string $target_id - Adds a class to the button to javascript take an action on.
	 * @param  string $href_class - Adds a class to the button to javascript take an action on.
	 * @param  string $target_window - adds a target window element used to switch destination windows.
	 *
	 * @return string
	 */
	public function basket_nav_bar_button( string $button_type, string $button_label, string $room_name = null, string $nonce = null, string $product_or_id = null, string $style = null, string $target_id = null, string $href_class = null, string $target_window = null ): string {

		$id_text = null;
		if ( $product_or_id ) {
			$id_text .= ' data-record-id="' . $product_or_id . '" ';
		}

		if ( $target_window ) {
			$id_text .= ' data-target="' . $target_window . '" ';
		}

		if ( ! $style ) {
			$style = 'mvr-main-button-enabled';
		}

		return '
		<button  class="mvr-confirmation-button ' . $style . '" data-nonce="' . $nonce . '" data-room-id="' . $target_id . '" data-input-type="' . $button_type . '">
		<a  data-room-id="' . $target_id . '" data-input-type="' . $button_type . '" data-nonce="' . $nonce . '" data-room-name="' . $room_name . '"' . $id_text . ' class="mvr-confirmation-button ' . $style . $href_class . '">' . $button_label . '</a>
		</button>
		';
	}

}
