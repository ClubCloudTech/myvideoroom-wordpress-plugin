<?php
/**
 * @TODO
 *
 * @package MyVideoRoomPlugin/Module/RoomBuilder/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder\Settings;

use MyVideoRoomPlugin\Library\AdminNavigation;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

/**
 * Class Reference
 */
class RoomPermissionsOption {

	private string $key;

	private bool $is_checked;

	private string $label;

	private string $description;

	/**
	 * RoomPermissionsOption constructor.
	 *
	 * @param string $key
	 * @param bool $is_checked
	 * @param string $label
	 * @param string $description
	 */
	public function __construct( string $key, bool $is_checked, string $label, string $description ) {
		$this->key         = $key;
		$this->is_checked  = $is_checked;
		$this->label       = $label;
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * @return bool
	 */
	public function is_checked(): bool {
		return $this->is_checked;
	}

	public function set_checked(bool $is_checked): self {
		$this->is_checked = $is_checked;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}
}
