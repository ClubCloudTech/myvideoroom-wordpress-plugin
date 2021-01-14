<?php

/**
 * Plugin Name:     ClubCloud Video
 * Plugin URI:      https://clubcloud.tech
 * Description:     Allows embedding of the ClubCloud Video App into Wordpress
 * Version:         0.3.1-alpha
 * Requires         PHP: 7.4
 * Author:          Alec Sammon, Craig Jones
 * Author URI:      https://clubcloud.tech/
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'ClubCloudVideo_Plugin' ) ) {

	function clubCloudVideo_autoloader( $className ) {
		if ( strpos( $className, 'ClubCloudVideo' ) === 0 ) {
			$srcLocation = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;

			$fileName = str_replace( '_', DIRECTORY_SEPARATOR, $className ) . '.php';
			require_once($srcLocation . $fileName);
		}
	}
	spl_autoload_register( 'clubCloudVideo_autoloader' );

	add_action( 'plugins_loaded', [ 'ClubCloudVideoPlugin', 'init' ] );
}