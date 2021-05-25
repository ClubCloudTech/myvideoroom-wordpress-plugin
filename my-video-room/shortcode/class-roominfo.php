<?php
/**
 * Short code for showing room info
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Shortcode;

use MyVideoRoomPlugin\DAO\RoomAdmin;
use MyVideoRoomPlugin\Factory;

/**
 * Class RoomInfo
 */
class RoomInfo {
	const SHORTCODE_TAG_INFO = App::SHORTCODE_TAG . '_info';

	/**
	 * Install the shortcode
	 */
	public function init() {
		\add_shortcode( self::SHORTCODE_TAG_INFO, array( $this, 'output_shortcode' ) );
	}

	/**
	 * Create video room info shortcode
	 *
	 * @param array|string $attributes List of params to pass to the shortcode.
	 *
	 * @return string
	 */
	public function output_shortcode( $attributes = array() ) {
		$room_name = $attributes['room'] ?? '';
		$room_type = $attributes['type'] ?? '';

		return Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, $room_type );
	}

}
