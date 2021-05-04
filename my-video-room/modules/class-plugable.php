<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Modules;

/**
 * Interface Plugable
 */
interface Plugable {
	/**
	 * Get the plugin name
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Is the plugin available?
	 *
	 * @return bool
	 */
	public function is_available(): bool;

	/**
	 * Is the plugin installed?
	 *
	 * @return bool
	 */
	public function is_installed(): bool;

	/**
	 * Is the plugin active?
	 *
	 * @return bool
	 */
	public function is_active(): bool;

	/**
	 * Initialise the plugin
	 * - Add shortcodes
	 * - Add styles and scripts
	 * - ...
	 */
	public function init(): void;

	/**
	 * Activate the plugin
	 * - set up WordPress roles
	 * - create database tables
	 * - ...
	 *
	 * @return bool If the activation was successful
	 */
	public function activate(): bool;

	/**
	 * Deactivate the plugin
	 * - remove any new roles
	 * - delete database tables
	 *
	 * @return bool
	 */
	public function deactivate(): bool;


	/**
	 * Create the admin settings page
	 *
	 * @return string
	 */
	public function create_admin_settings(): string;
}
