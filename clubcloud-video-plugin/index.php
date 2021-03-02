<?php
/**
 * ClubCloud Video Plugin Initializer. Will be auto-called by WordPress
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

/**
 * Plugin Name:         ClubCloud Video
 * Plugin URI:          https://clubcloud.tech
 * Description:         Allows embedding of the ClubCloud Video App into WordPress
 * Version:             0.9.0-beta
 * Requires PHP:        7.4
 * Requires at least:   5.6
 * Author:              Alec Sammon, Craig Jones
 * Author URI:          https://clubcloud.tech/
 * License:             GPLv2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 */


namespace ClubCloudVideoPlugin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! class_exists( Plugin::class ) ) {
	/**
	 * Autoloader for classes in the ClubCloud Video Plugin
	 *
	 * @param string $class_name The name of the class to autoload.
	 *
	 * @throws \Exception When file is not found.
	 *
	 * @return boolean
	 */
	function autoloader( string $class_name ): bool {
		if ( strpos( $class_name, 'ClubCloudVideoPlugin' ) === 0 ) {
			$src_location = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;

			$file_name = str_replace( 'ClubCloudVideoPlugin\\', '', $class_name );
			$file_name = strtolower( $file_name );

			$file_name = str_replace( '\\', DIRECTORY_SEPARATOR, $file_name ) . '.php';

			$path     = ( pathinfo( $file_name ) );
			$location = realpath( $src_location . $path['dirname'] ) . '/class-' . $path['basename'];

			if ( ! file_exists( $location ) ) {
				return false;
			}

			return (bool) include_once $location;
		}

		return false;
	}

	spl_autoload_register( 'ClubCloudVideoPlugin\autoloader' );

	add_action( 'plugins_loaded', array( Plugin::class, 'init' ) );
}