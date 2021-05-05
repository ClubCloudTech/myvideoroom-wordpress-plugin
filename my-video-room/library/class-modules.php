<?php
/**
 * Get details about the modules installed into the plugin
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Module\BuddyPress\Plugin as BuddyPress;
use MyVideoRoomPlugin\Module\Module;
use MyVideoRoomPlugin\Module\Plugable;

/**
 * Class Modules
 */
class Modules {


	/**
	 * Get all available MyVideoRoom modules
	 *
	 * @return Module[]
	 */
	public function get_modules(): array {

		$modules_dir = new \DirectoryIterator( __DIR__ . '/../module' );

		$modules = array();

		foreach ( $modules_dir as $module ) {
			$path = __DIR__ . '/../module/' . $module->getFilename() . '/index.php';

			if (
				! $module->isDir() ||
				$module->isDot() ||
				! file_exists( $path )
			) {
				continue;
			}

			$modules[ $module->getFilename() ] = $this->get_module( realpath( $path ) );
		}

		ksort( $modules );

		return $modules;
	}

	/**
	 * Load a module
	 *
	 * @param string $path The path to the module.
	 *
	 * @return Module
	 */
	private function get_module( string $path ): Module {
		$module_data = \get_file_data(
			$path,
			array(
				'name'        => 'Module Name',
				'description' => 'Description',
				'published'   => 'Published',
			)
		);

		return new Module(
			$module_data['name'],
			$module_data['description'],
			( 'false' !== $module_data['published'] ),
			( require $path )
		);
	}
}
