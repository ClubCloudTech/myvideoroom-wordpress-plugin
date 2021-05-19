<?php
/**
 * Manages the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder;

use function do_shortcode;
use function do_action;

/**
 * Class Admin
 */
class Admin {

	/**
	 * Create the room builder admin page
	 *
	 * @return string
	 */
	public function create_room_builder_page(): string {
		return \do_shortcode( '[' . Module::SHORTCODE_TAG . ' initial_preview=false]' );
	}

}
