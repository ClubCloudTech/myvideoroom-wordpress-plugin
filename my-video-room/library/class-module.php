<?php
/**
 * Get details about the modules installed into the plugin
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use DirectoryIterator;
use MyVideoRoomPlugin\Module\Module as ModuleInstance;
use MyVideoRoomPlugin\Plugin;

/**
 * Class Modules
 */
class Module {

	/**
	 * List of registered modules
	 *
	 * @var ModuleInstance[]
	 */
	private static array $modules = array();

	// --

	/**
	 * Register a module
	 * Should be called by a module in an action attached to `myvideoroom_init`
	 *
	 * @param string    $slug               The modules slug.
	 * @param string    $name               The modules translated name.
	 * @param array     $description_array  An array of paragraphs to show as a description.
	 * @param ?callable $instantiation_hook The callback to instantiate the module.
	 *
	 * @return ModuleInstance
	 */
	public static function register(
		string $slug,
		string $name,
		array $description_array,
		callable $instantiation_hook = null
	): ModuleInstance {
		$module = new ModuleInstance(
			$slug,
			$name,
			$description_array,
			$instantiation_hook
		);

		self::$modules[ $slug ] = $module;

		\ksort( self::$modules );

		return $module;
	}

	/**
	 * Load all the built in modules
	 */
	public static function load_built_in_modules() {
		$modules_dir = new DirectoryIterator( __DIR__ . '/../module' );

		foreach ( $modules_dir as $module ) {
			$path = __DIR__ . '/../module/' . $module->getFilename() . '/index.php';

			if (
				$module->isDir() &&
				! $module->isDot() &&
				\file_exists( $path )
			) {
				require_once $path;
			}
		}
	}

	/**
	 * Get a module by it's slug
	 *
	 * @param string $slug The module's slug.
	 *
	 * @return ?ModuleInstance
	 */
	public function get_module( string $slug ): ?ModuleInstance {
		$modules = $this->get_all_modules();

		return $modules[ $slug ] ?? null;
	}

	/**
	 * Get all available MyVideoRoom modules
	 *
	 * @return ModuleInstance[]
	 */
	public function get_all_modules(): array {
		if ( \get_option( Plugin::SETTING_ACTIVATED_MODULES ) ) {
			$activated_modules = \json_decode( \get_option( Plugin::SETTING_ACTIVATED_MODULES ), true );
		} else {
			$activated_modules = array();
		}

		foreach ( self::$modules as $module ) {
			if ( $module->is_published() && \in_array( $module->get_slug(), $activated_modules, true ) ) {
				$module->set_as_active();
			} else {
				$module->set_as_inactive();
			}
		}

		return self::$modules;
	}

	/**
	 * Is a module currently active
	 *
	 * @param string $module_slug The module slug to check for activation status.
	 *
	 * @return bool
	 */
	public function is_module_active( string $module_slug ): bool {
		$active_modules = $this->get_active_modules();

		return \in_array(
			$module_slug,
			\array_map( fn( ModuleInstance $module ) => $module->get_slug(), $active_modules ),
			true
		);
	}

	/**
	 * Get the list of active modules
	 *
	 * @return ModuleInstance[]
	 */
	public function get_active_modules(): array {
		if ( \get_option( Plugin::SETTING_ACTIVATED_MODULES ) ) {
			$activated_modules = \json_decode( \get_option( Plugin::SETTING_ACTIVATED_MODULES ), true );
		} else {
			$activated_modules = array();
		}

		return \array_filter(
			self::$modules,
			function ( ModuleInstance $module, $key ) use ( $activated_modules ) {
				return $module->is_published() && \in_array( $key, $activated_modules, true );
			},
			ARRAY_FILTER_USE_BOTH
		);
	}
}
