<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\DAO\RoomMap;
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
	 * @return string
	 */
	public function site_videoroom_host_template( int $room_id ): string {
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
	 * @return string
	 */
	public function site_videoroom_guest_template( int $host_id ): string {
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
					return MVRSiteVideo::ROOM_NAME_TABLE;
				}
		}

		return $room_type;
	}

	/**
	 * Generate Room Table
	 *
	 * @param string $room_type ? all rooms.
	 * @return string table.
	 */
	public function generate_room_table( string $room_type = null ): string {
		// Returns all rooms with null roomtype, or a specific room with Room Type.
		$rooms = Factory::get_instance( MVRSiteVideoRoomHelpers::class )->get_rooms( $room_type );
		return ( require __DIR__ . '/../views/table-output.php' )( $rooms );
	}
}
