<?php
/**
 * Menu Tab Object Class
 * Shows Menu Objects that can be rendered by tabs in Elemental and MyVideoRoom.
 *
 * @package MyVideoRoomPlugin\Entity
 */

namespace MyVideoRoomPlugin\Entity;

/**
 * Menu Tab Object Class
 * Shows Menu Objects that can be rendered by tabs in Elemental and MyVideoRoom.
 */
class MenuTabDisplay {

	/**
	 * Tab Display Name
	 *
	 * @var string $tab_display_name
	 */
	private string $tab_display_name;

	/**
	 * Tab slug
	 *
	 * @var string $tab_slug
	 */
	private string $tab_slug;

	/**
	 * CallBack Content
	 *
	 * @var callable $function_callback
	 */
	private $function_callback;

	/**
	 * CallBack Content
	 *
	 * @var string $element_id - the ID to use for the element
	 */
	private ?string $element_id = null;

	/**
	 * MenuTabDisplay constructor.
	 *
	 * @param string   $tab_display_name  Description of Tab.
	 * @param string   $tab_slug          Identifier of Tab for navigation.
	 * @param callable $function_callback Function to display content.
	 * @param ?string  $element_id - the ID to use for the element.
	 */
	public function __construct(
		string $tab_display_name,
		string $tab_slug,
		callable $function_callback,
		?string $element_id = null
	) {
		$this->tab_display_name  = $tab_display_name;
		$this->tab_slug          = $tab_slug;
		$this->function_callback = $function_callback;
		$this->element_id        = $element_id;
	}

	/**
	 * Gets Tab Display Name.
	 *
	 * @return string
	 */
	public function get_tab_display_name(): string {
		return $this->tab_display_name;
	}

	/**
	 * Gets Tab Slug.
	 *
	 * @return string
	 */
	public function get_tab_slug(): string {
		return $this->tab_slug;
	}

	/**
	 * Gets Function Callback.
	 *
	 * @return string
	 */
	public function get_function_callback(): string {
		return ( $this->function_callback )();
	}

	/**
	 * Gets Element ID.
	 *
	 * @return string
	 */
	public function get_element_id(): ?string {
		return $this->element_id;
	}
}
