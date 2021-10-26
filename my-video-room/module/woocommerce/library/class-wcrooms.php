<?php
/**
 * Class WCRooms - Functions to assist WooCommerce Room Management
 *
 * @package my-video-room/module/woocommerce/library/class-wcrooms.php
 */

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\UserVideoPreference;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class WCRooms - Functions to assist WooCommerce Room Management
 */
class WCRooms {

	const ROOM_TITLE_WCROOM     = ' Video Storefront';
	const ROOM_SLUG_WCROOM      = 'storevideo';
	const ROOM_SHORTCODE_WCROOM = 'myvideoroom_wcstore';

	/**
	 * Room Type Friendly Name
	 *
	 * @param string $room_type .
	 * @return string name.
	 */
	public function conference_room_friendly_name( string $room_type ): string {
		switch ( $room_type ) {
			case MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME:
				if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID ) ) {
					return esc_html__( 'Module Disabled', 'myvideoroom' );
				} else {
					return MVRPersonalMeeting::MODULE_PERSONAL_MEETING_TABLE_DISPLAY;
				}
		}
		return $room_type;
	}

	/**
	 * Room Shortcode Transform
	 *
	 * @param ?string $input .
	 * @param ?string $room_type .
	 * @param int     $room_id - the room id.
	 * @param Object  $room_object .
	 * @return string name.
	 */
	public function conference_change_shortcode( ?string $input = null, ?string $room_type, int $room_id = null, $room_object ): ?string {
		if ( ! $room_type ) {
			return $input;
		}
		switch ( $room_type ) {
			case WooCommerce::MODULE_WOOCOMMERCE_ROOM:
				if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID ) ) {
					return esc_html__( 'Module Disabled', 'myvideoroom' );
				} else {
					$shortcode = '[' . preg_replace( '/[^a-zA-Z0-9\s_]/', '', MVRPersonalMeeting::ROOM_SHORTCODE_PERSONAL_MEETING ) . ']';
					return $shortcode;
				}
			case MVRSiteVideo::ROOM_NAME_REDIRECT:
				$shortcode = '[' . preg_replace( '/[^a-zA-Z0-9\s_]/', '', MVRSiteVideo::SHORTCODE_REDIRECT ) . ']';
				return $shortcode;

		}

		return $input;
	}

	/**
	 * Regenerate Room Helper for WC Store
	 *
	 * @param ?string $input .
	 * @param int     $room_id - the room id.
	 * @param object  $room_object . Object with preferences.
	 * @return string CallBack.
	 */
	public function regenerate_wcstore( ?string $input = null, int $room_id, object $room_object ): ?string {
		if ( WooCommerce::MODULE_WOOCOMMERCE_ROOM === $room_object->room_type ) {
			Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_object->room_name );
			$this->create_wcstore_page();
		}
		return $input;
	}

	/**
	 * Regenerate or create a Personal Meeting Centre page
	 *
	 * @param ?int       $original_room_id The original room id.
	 * @param ?\stdClass $room_object      The original room object.
	 *
	 * @return int
	 */
	public function create_wcstore_page( int $original_room_id = null, \stdClass $room_object = null ): int {
		if ( ! $room_object || WooCommerce::MODULE_WOOCOMMERCE_ROOM === $room_object->room_name ) {
			$new_id = $this->create_and_check_wcstore_page(
				WooCommerce::MODULE_WOOCOMMERCE_ROOM,
				get_bloginfo( 'name' ) . ' ' . self::ROOM_TITLE_WCROOM,
				self::ROOM_SLUG_WCROOM,
				WooCommerce::MODULE_WOOCOMMERCE_ROOM,
				self::ROOM_SHORTCODE_WCROOM,
				$original_room_id,
			);
		} else {
			$new_id = $this->create_and_check_wcstore_page(
				$room_object->room_name,
				$room_object->display_name,
				$room_object->slug,
				WooCommerce::MODULE_WOOCOMMERCE_ROOM,
				self::ROOM_SHORTCODE_WCROOM,
				$original_room_id
			);
		}

		return $new_id;
	}
	/**
	 * Create a WC Store page into the WordPress environment, register in page table, and ensure its enabled.
	 *
	 * @param string  $room_name      Name of room to build.
	 * @param string  $display_title  Title of Page.
	 * @param string  $slug           WordPress Slug to assign page.
	 * @param string  $room_type      Type of Room in DB.
	 * @param ?string $shortcode      Shortcode to store for room.
	 * @param ?string $old_post_id    Type Old Room in DB to update the value to the new post..
	 *
	 * @return integer
	 */
	private function create_and_check_wcstore_page( string $room_name, string $display_title, string $slug, string $room_type, string $shortcode = null, string $old_post_id = null ): ?int {
		// Check Page Doesn't already Exist in Database and hasn't been deleted if it does.
		$check_page_exists = Factory::get_instance( RoomMap::class )->check_page_exists( $room_name );

		// Check_page_exists has three states, Yes, No, Or Orphan - if yes - exit function, if no create the room, if orphan delete room mapping in database and create room again.
		if ( RoomMap::PAGE_STATUS_EXISTS === $check_page_exists ) {
			return Factory::get_instance( RoomMap::class )->get_post_id_by_room_name( $room_name );
		}

		// Create Page in DB as Page doesn't exist.
		$post_id = wp_insert_post(
			array(
				'post_author'  => 1,
				'post_title'   => $display_title,
				'post_name'    => strtolower( str_replace( ' ', '-', trim( $slug ) ) ),
				'post_status'  => 'publish',
				'post_content' => '[' . $shortcode . ']',
				'post_type'    => 'page',
			)
		);

		if ( $old_post_id ) {
			// Update Database References to New Post IDs to ensure Room Permissions and Settings stay intact with New Pages.
			Factory::get_instance( UserVideoPreference::class )->update_user_id( $post_id, $old_post_id );

			// Handle case if other modules have tables to update.
			\do_action( 'myvideoroom_page_delete_post_number_refresh', $post_id, $old_post_id );
		}

		// Insert into DB as Page Didn't Exist.
		if ( RoomMap::PAGE_STATUS_NOT_EXISTS === $check_page_exists ) {
			Factory::get_instance( RoomMap::class )->register_room_in_db( $room_name, $post_id, $room_type, $display_title, $slug, $shortcode );
		} elseif ( RoomMap::PAGE_STATUS_ORPHANED === $check_page_exists ) {
			// Update the DB if Orphan.
			Factory::get_instance( RoomMap::class )->update_room_post_id( $post_id, $room_name );
		}
		return $post_id;
	}



	/**
	 * Render Personal Rooms Admin Page.
	 */
	public function render_personalvideo_admin_page() {
		$module_active = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID );
		if ( $module_active ) {
			$settings_style = '';
			$info_style     = 'display: none;';
		} else {
			$settings_style = 'display: none;';
			$info_style     = '';
		}
		return ( require __DIR__ . '/../views/view-settings-personalvideo.php' )( $settings_style, $info_style );
	}

}
