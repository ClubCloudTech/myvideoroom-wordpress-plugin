<?php
/**
 * An option for setting room permissions
 *
 * @package MyVideoRoomPlugin/Module/RoomBuilder/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder\Settings;

/**
 * Class Reference
 */
class RoomPermissionsOption {

	/**
	 * The key of the permissions option
	 *
	 * @var string
	 */
	private string $key;

	/**
	 * Is this option selected
	 *
	 * @var bool
	 */
	private bool $is_selected;

	/**
	 * The label of the option
	 *
	 * @var string
	 */
	private string $label;

	/**
	 * The description of how the option works
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * RoomPermissionsOption constructor.
	 *
	 * @param string $key         The key of the permissions option.
	 * @param bool   $is_selected Is this option selected.
	 * @param string $label       The label of the option.
	 * @param string $description The description of how the option works.
	 */
	public function __construct( string $key, bool $is_selected, string $label, string $description ) {
		$this->key         = $key;
		$this->is_selected = $is_selected;
		$this->label       = $label;
		$this->description = $description;
	}

	/**
	 * Get the key of the permissions option.
	 *
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Is this option selected.
	 *
	 * @return bool
	 */
	public function is_selected(): bool {
		return $this->is_selected;
	}

	/**
	 * Set if the option is selected
	 *
	 * @param bool $is_selected If the option should be selected.
	 *
	 * @return $this
	 */
	public function set_as_selected( bool $is_selected ): self {
		$this->is_selected = $is_selected;
		return $this;
	}

	/**
	 * Get the label of the option.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Get the description of how the option works.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}
}
