<?php
/**
 * A HTML checkbox input field
 *
 * @package MyVideoRoomPlugin/Module/Security/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Security\Settings\Field;

/**
 * Class Field
 */
class SelectOption {

	/**
	 * The Value
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * The Name
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * SelectOption constructor.
	 *
	 * @param string $value - value.
	 * @param string $name  - name.
	 */
	public function __construct( string $value, string $name ) {
		$this->value = $value;
		$this->name  = $name;
	}

	/**
	 * Get Value.
	 *
	 * @return string
	 */
	public function get_value(): string {
		return $this->value;
	}

	/**
	 * Get Name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

}
