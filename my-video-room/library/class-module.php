<?php
/**
 * Get details about the modules installed into the plugin
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

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
	 * @param string    $slug The modules slug.
	 * @param string    $name The modules translated name.
	 * @param array     $description_array An array of paragraphs to show as a description.
	 * @param ?callable $instantiation_hook The callback to instantiate the module.
	 *
	 * @return ModuleInstance
	 */
	public function register(
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

		ksort( self::$modules );

		return $module;
	}

	/**
	 * Get all available MyVideoRoom modules
	 *
	 * @return ModuleInstance[]
	 */
	public function get_all_modules(): array {
		if ( get_option( Plugin::SETTING_ACTIVATED_MODULES ) ) {
			$activated_modules = json_decode( get_option( Plugin::SETTING_ACTIVATED_MODULES ), true );
		} else {
			$activated_modules = array();
		}

		foreach ( self::$modules as $module ) {
			if ( $module->is_published() && in_array( $module->get_slug(), $activated_modules, true ) ) {
				$module->set_as_active();
			} else {
				$module->set_as_inactive();
			}
		}

		return self::$modules;
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
	 * Get the list of active modules
	 *
	 * @return ModuleInstance[]
	 */
	public function get_active_modules(): array {
		if ( get_option( Plugin::SETTING_ACTIVATED_MODULES ) ) {
			$activated_modules = json_decode( get_option( Plugin::SETTING_ACTIVATED_MODULES ), true );
		} else {
			$activated_modules = array();
		}

		return array_filter(
			self::$modules,
			function ( ModuleInstance $module, $key ) use ( $activated_modules ) {
				return $module->is_published() && in_array( $key, $activated_modules, true );
			},
			ARRAY_FILTER_USE_BOTH
		);
	}

	/**
	 * Load all the built in modules
	 */
	public static function load_built_in_modules() {
		$modules_dir = new \DirectoryIterator( __DIR__ . '/../module' );

		foreach ( $modules_dir as $module ) {
			$path = __DIR__ . '/../module/' . $module->getFilename() . '/index.php';

			if (
				$module->isDir() &&
				! $module->isDot() &&
				file_exists( $path )
			) {
				require_once $path;
			}
		}
	}

	/**
	 * Activate a module
	 *
	 * @param ModuleInstance $module The module to activate.
	 *
	 * @return boolean
	 */
	public function activate_module( ModuleInstance $module ): bool {
		$all_modules = $this->get_all_modules();

		$activation_status = $module->activate();

		if ( ! $activation_status ) {
			return false;
		}

		$module->set_as_active();

		$activated_modules = array_keys( array_filter( $all_modules, fn( $module ) => $module->is_active() ) );

		update_option( Plugin::SETTING_ACTIVATED_MODULES, wp_json_encode( $activated_modules ) );

		$module->instantiate();

		return true;
	}

	/**
	 * Deactivate a module
	 *
	 * @param ModuleInstance $module The module to deactivate.
	 *
	 * @return boolean
	 */
	public function deactivate_module( ModuleInstance $module ): bool {
		$all_modules = $this->get_all_modules();

		$deactivation_status = $module->deactivate();

		if ( ! $deactivation_status ) {
			return false;
		}

		$module->set_as_inactive();

		$activated_modules = array_keys( array_filter( $all_modules, fn( $module ) => $module->is_active() ) );

		update_option( Plugin::SETTING_ACTIVATED_MODULES, wp_json_encode( $activated_modules ) );

		return true;
	}
}
