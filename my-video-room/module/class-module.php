<?php
/**
 * Wraps a module to make it easier to access
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module;

/**
 * Class Module
 */
class Module {

	/**
	 * The name of the module
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * The description of the module
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * If the module is published (or coming soon)
	 *
	 * @var bool
	 */
	private bool $published;

	/**
	 * The factory to create the module
	 *
	 * @var callable
	 */
	private $module_factory;

	/**
	 * Module constructor.
	 *
	 * @param string   $name            The name of the module.
	 * @param string   $description     The description of the module.
	 * @param bool     $published       If the module is published (or coming soon).
	 * @param callable $module_factory  The factory to create the module.
	 */
	public function __construct( string $name, string $description, bool $published, callable $module_factory ) {
		$this->name           = $name;
		$this->description    = $description;
		$this->published      = $published;
		$this->module_factory = $module_factory;
	}

	/**
	 * Get the name of the module
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the description of the module
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Is the module published
	 *
	 * @return bool
	 */
	public function is_published(): bool {
		return $this->published;
	}

	/**
	 * Get an instance of the module
	 *
	 * @return Plugable
	 */
	public function get_instance(): Plugable {
		return ( $this->module_factory )();
	}


}
