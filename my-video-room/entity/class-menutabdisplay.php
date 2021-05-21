<?php
/**
 * User Video Preference Entity Object
 *
 * @package MyVideoRoomPlugin\Entity
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
	 * MenuTabDisplay constructor.
	 *
	 * @param string   $tab_display_name   Description of Tab.
	 * @param string   $tab_slug           Identifier of Tab for navigation.
	 * @param callable $function_callback  Function to display content.
	 */
	public function __construct(
		string $tab_display_name,
		string $tab_slug,
		$function_callback
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
}
