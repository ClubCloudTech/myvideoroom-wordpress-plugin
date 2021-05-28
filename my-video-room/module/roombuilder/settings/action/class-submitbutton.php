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
 * Class SubmitButton
 */
class SubmitButton implements Action {

	/**
	 * The action of the button
	 *
	 * @var string
	 */
	private string $action;

	/**
	 * Is submit text
	 *
	 * @var string
	 */
	private string $submit_text;

	/**
	 * The type of the button
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * SubmitOption constructor.
	 *
	 * @param string $action      The action of the button.
	 * @param string $submit_text The submit text.
	 * @param string $type        The type.
	 */
	public function __construct( string $action, string $submit_text, string $type = 'primary' ) {
		$this->action      = $action;
		$this->submit_text = $submit_text;
		$this->type        = $type;
	}

	/**
	 * Get the submit button
	 *
	 * @return string
	 */
	public function get_html(): string {
		return Factory::get_instance( HttpPost::class )->create_form_submit(
			$this->action,
			$this->submit_text,
			$this->type
		);
	}
}
