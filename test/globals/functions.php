<?php
/**
 * This file defines any global functions from WordPress
 *
 * @package ClubCloudGamesPlugin/Test
 */

declare(strict_types=1);

define( 'WPINC', true );

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/plugin_dir_path/
 */
function plugin_dir_path() {
	return realpath( __DIR__ . '/../../my-video-room' );
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/get_plugin_data/
 */
function get_plugin_data() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
function add_action() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
function add_shortcode() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 */
function wp_enqueue_script() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 */
function wp_enqueue_style() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/plugins_url/
 */
function plugins_url() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/register_activation_hook/
 */
function register_activation_hook() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/register_deactivation_hook/
 */
function register_deactivation_hook() {
}

/**
 * Proxy for WordPress
 *
 * @see https://developer.wordpress.org/reference/functions/wp_rand/
 *
 * @param integer $min Lower limit for the generated number.
 * @param integer $max Upper limit for the generated number.
 *
 * @return int
 */
function wp_rand( int $min = 0, int $max = 0 ) {
	//phpcs:ignore WordPress.WP.AlternativeFunctions.rand_rand
	return rand( $min, $max );
}

