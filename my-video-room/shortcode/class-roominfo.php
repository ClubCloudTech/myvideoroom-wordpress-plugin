<?php
/**
 * Short code for showing room info
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Shortcode;

use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;

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
		$data_type = $attributes['type'] ?? '';

		$room_admin_library = Factory::get_instance( RoomAdmin::class );

		switch ( $data_type ) {
			case 'url':
				$value = $room_admin_library->get_room_url( $room_name );
				break;
			case 'name':
			case 'slug':
				$post  = $room_admin_library->get_post( $room_name );
				$value = $post ? $post->post_name : '';
				break;
			case 'post_id':
				$value = Factory::get_instance( RoomMap::class )->get_post_id_by_room_name( $room_name );
				break;
			case 'type':
				$value = $room_admin_library->get_room_type( $room_name );
				break;
			case 'title':
				$post  = $room_admin_library->get_post( $room_name );
				$value = $post ? $post->post_title : '';
				break;
			default:
				$value = sprintf(
					/* translators: %s is the data type string */
					esc_html__( 'Unknown data type "%s"' ),
					$data_type
				);
		}

		return "<span>${value}</span>";
	}
}
