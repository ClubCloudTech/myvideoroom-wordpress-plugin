<?php
/**
 * @TODO
 *
 * @package MyVideoRoomPlugin/Module/RoomBuilder/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder\Settings;

/**
 * Interface Action
 */
interface Action {
	/**
	 * Get the output of the action as HTML
	 *
	 * @return string
	 */
	public function get_html(): string;
}
