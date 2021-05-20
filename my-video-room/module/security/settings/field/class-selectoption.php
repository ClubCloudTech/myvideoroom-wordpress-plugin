<?php
/**
 * A HTML checkbox input field
 *
 * @package MyVideoRoomPlugin/Module/Security/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Security\Settings\Field;

use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Module\Security\Settings\Field;

/**
 * Class Field
 */
class SelectOption {

	private string $value;
	private string $name;

	/**
	 * SelectOption constructor.
	 *
	 * @param string $value
	 * @param string $name
	 */
	public function __construct( string $value, string $name ) {
		$this->value = $value;
		$this->name  = $name;
	}

	/**
	 * @return string
	 */
	public function get_value(): string {
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}



}
