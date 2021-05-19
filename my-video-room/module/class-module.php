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
	 * The id/slug of the module
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * The name of the module
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * An array of the description paragraphs
	 *
	 * @var array
	 */
	private array $description_array;

	/**
	 * The instantiation hook
	 *
	 * @var ?callable
	 */
	private $instantiation_hook;

	/**
	 * The compatibility hook
	 *
	 * @var ?callable
	 */
	private $compatibility_hook = null;

	/**
	 * The compatibility hook
	 *
	 * @var ?callable
	 */
	private $admin_page_hook = null;

	/**
	 * The activation hook
	 *
	 * @var ?callable
	 */
	private $activation_hook = null;

	/**
	 * The deactivation hook
	 *
	 * @var ?callable
	 */
	private $deactivation_hook = null;

	/**
	 * Is the module active
	 *
	 * @var bool
	 */
	private bool $active = false;

	/**
	 * Is the module hidden
	 *
	 * @var bool
	 */
	private bool $hidden = false;

	/**
	 * Module constructor.
	 *
	 * @param string    $slug               The id/slug of the module.
	 * @param string    $name               The name of the module.
	 * @param array     $description_array  The description of the module.
	 * @param ?callable $instantiation_hook The instantiation callback.
	 */
	public function __construct( string $slug, string $name, array $description_array, callable $instantiation_hook = null ) {
		$this->slug               = $slug;
		$this->name               = $name;
		$this->description_array  = $description_array;
		$this->instantiation_hook = $instantiation_hook;
	}

	/**
	 * Instantiate the modules
	 */
	public function instantiate() {
		( $this->instantiation_hook )();
	}

	/**
	 * Is the module compatible with this WordPress instance
	 *
	 * @return bool
	 */
	public function is_compatible(): bool {
		return ! $this->compatibility_hook || ( $this->compatibility_hook )();
	}

	/**
	 * Add a compatibility hook
	 *
	 * @param callable $hook A callback to check if the module is compatible with the WordPress install.
	 *
	 * @return $this
	 */
	public function add_compatibility_hook( callable $hook ): self {
		$this->compatibility_hook = $hook;

		return $this;
	}

	/**
	 * Get the admin page of the module
	 *
	 * @return string
	 */
	public function get_admin_page(): string {
		if ( $this->has_admin_page() ) {
			return ( $this->admin_page_hook )();
		}

		return '';
	}

	/**
	 * Does the module have it's own admin page
	 *
	 * @return bool
	 */
	public function has_admin_page(): bool {
		return ! ! $this->admin_page_hook;
	}

	/**
	 * Add an admin page hook
	 *
	 * @param callable $hook A callback that will render an admin page.
	 *
	 * @return $this
	 */
	public function add_admin_page_hook( callable $hook ): self {
		$this->admin_page_hook = $hook;

		return $this;
	}

	/**
	 * Get the key of the module
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
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
	 * Get the list of description paragraphs
	 *
	 * @return array
	 */
	public function get_description_array(): array {
		return $this->description_array;
	}

	/**
	 * Mark the module as active, and call the module's activation hook
	 *
	 * @return bool
	 */
	public function activate(): bool {
		if ( $this->activation_hook ) {
			$status = ( $this->activation_hook )();

			if ( false === $status ) {
				return false;
			}
		}

		$this->set_as_active();

		return true;
	}

	/**
	 * Mark the module as active
	 *
	 * @return $this
	 */
	public function set_as_active(): self {
		$this->active = true;

		return $this;
	}

	/**
	 * Add an activation hook
	 *
	 * @param callable $activation_hook Add a callback to call when the module is activated.
	 *
	 * @return $this
	 */
	public function add_activation_hook( callable $activation_hook ): self {
		$this->activation_hook = $activation_hook;

		return $this;
	}

	/**
	 * Call the module's deactivation hook, and mark as inactive
	 *
	 * @return bool
	 */
	public function deactivate(): bool {
		if ( $this->deactivation_hook ) {
			$status = ( $this->deactivation_hook )();

			if ( false === $status ) {
				return false;
			}
		}

		$this->set_as_inactive();

		return true;
	}

	/**
	 * Mark the module as inactive
	 *
	 * @return $this
	 */
	public function set_as_inactive(): self {
		$this->active = false;
		return $this;
	}

	/**
	 * Mark the module as hidden
	 * If the module is active then it will still be active, but not shown on the modules page.
	 * If the module is inactive then it will be shown on the modules page to allow the user to activate it.
	 *
	 * @return $this
	 */
	public function set_as_hidden(): self {
		$this->hidden = true;
		return $this;
	}

	/**
	 * Is the module hidden?
	 * Modules that are marked as hidden, and are active will not be shown in the modules page.
	 *
	 * @return bool
	 */
	public function is_hidden(): bool {
		return $this->is_active() && $this->hidden;
	}

	/**
	 * Add an deactivation hook
	 *
	 * @param callable $deactivation_hook Add a callback to call when the module is deactivated.
	 *
	 * @return $this
	 */
	public function add_deactivation_hook( callable $deactivation_hook ): self {
		$this->deactivation_hook = $deactivation_hook;

		return $this;
	}

	/**
	 * Is the module published
	 *
	 * @return bool
	 */
	public function is_published(): bool {
		return ! ! $this->instantiation_hook;
	}

	/**
	 * Is the module active?
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return $this->active;
	}

}
