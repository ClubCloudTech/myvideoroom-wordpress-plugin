<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoViews extends Shortcode {

	// ---
	// Site Video Room Templates.

	/**
	 * Render Site Videoroom Host Template
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
		$render        = require WP_PLUGIN_DIR . '/my-video-room/core/views/header/view-roomheader.php';
		$name_output   = esc_html__( 'Hosting ', 'my-video-room' );
		$is_guest      = false;
		$meeting_link  = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, 'url' );

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
		$render        = require WP_PLUGIN_DIR . '/my-video-room/core/views/header/view-roomheader.php';
		$name_output   = esc_html__( 'Visiting ', 'my-video-room' );
		$is_guest      = true;
		$meeting_link  = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, 'url' );

		return $render( $module_id, $name_output, $host_id, $room_name, $is_guest, $meeting_link, $module_suffix );

	}

}
