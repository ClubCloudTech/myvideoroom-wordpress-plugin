<?php
include("./models/ClubCloudVideoPlugin.php");
include("./models/ClubCloudVideoPlugin_Admin.php");
include("./models/ClubCloudVideoPlugin_JWT.php");
include("./models/ClubCloudVideoPlugin_Shortcode.php");

/**
 * Plugin Name:     ClubCloud Video
 * Plugin URI:      https://clubcloud.tech
 * Description:     Allows embedding of the ClubCloud Video App into Wordpress
 * Version:         0.2-alpha
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
	add_action( 'plugins_loaded', [ 'ClubCloudVideoPlugin', 'init' ] );
	// ---
}
