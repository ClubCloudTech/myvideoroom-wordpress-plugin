<?php
/**
 * User Video Preference Entity Object
 *
 * @package MyVideoRoomPlugin\Core\Entity
 */

namespace MyVideoRoomPlugin\Entity;

/**
 * Class UserVideoPreference
 */
class MenuTabDisplay {

	/**
	 * Tab Display Name
	 *
	 * @var string $tab_display_name
	 */
	private ?string $tab_display_name;

	/**
	 * Tab slug
	 *
	 * @var ?string $tab_slug
	 */
	private ?string $tab_slug;

	/**
	 * CallBack Content
	 *
	 * @var ?string $function_callback
	 */
	private ?string $function_callback;

	/**
	 * MenuTabDisplay constructor.
	 *
	 * @param ?string $tab_display_name   Description of Tab.
	 * @param ?string $tab_slug           Identifier of Tab for navigation.
	 * @param ?string $function_callback  Function to display content.
	 */
	public function __construct(
		string $tab_display_name,
		string $tab_slug,
		string $function_callback
	) {
		$this->tab_display_name  = $tab_display_name;
		$this->tab_slug          = $tab_slug;
		$this->function_callback = $function_callback;
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
	 * Sets Tab Display Name.
	 *
	 * @param string|null $tab_display_name - Display Name of Tab.
	 *
	 * @return MenuTabDisplay
	 */
	public function set_tab_display_name( string $tab_display_name = null ): MenuTabDisplay {
		$this->tab_display_name = $tab_display_name;
		return $this;
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
	 * Sets Tab Slug.
	 *
	 * @param string|null $tab_slug - Slug for Display of Tab in HTML.
	 *
	 * @return MenuTabDisplay
	 */
	public function set_tab_slug( string $tab_slug = null ): MenuTabDisplay {
		$this->tab_slug = $tab_slug;
		return $this;
	}

	/**
	 * Gets Function Callback.
	 *
	 * @return string
	 */
	public function get_function_callback(): string {
		return $this->function_callback;
	}

	/**
	 * Sets Function Callback.
	 *
	 * @param string|null $function_callback - the function that generates content.
	 *
	 * @return MenuTabDisplay
	 */
	public function set_function_callback( string $function_callback = null ): MenuTabDisplay {
		$this->function_callback = $function_callback;
		return $this;
	}
}
