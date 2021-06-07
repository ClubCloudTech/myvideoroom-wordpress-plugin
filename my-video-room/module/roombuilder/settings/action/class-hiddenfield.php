<?php
/**
 * An option for setting room permissions
 *
 * @package MyVideoRoomPlugin/Module/RoomBuilder/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action;

/**
 * Class HiddenField
 */
class HiddenField implements Action {

	/**
	 * The name of the field
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * The value of the field
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * HiddenField constructor.
	 *
	 * @param string $name  The name of the field.
	 * @param string $value The value of the field.
	 */
	public function __construct( string $name, string $value ) {
		$this->name  = $name;
		$this->value = $value;
	}

	/**
	 * Get the submit button
	 *
	 * @return string
	 */
	public function get_html(): string {
		return '<input type="hidden" value="' . $this->value . '" name="myvideoroom_' . $this->name . '" />';
	}
}
