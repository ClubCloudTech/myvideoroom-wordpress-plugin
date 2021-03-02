<?php
/**
 * The entry point for the plugin
 *
 * @package ClubCloudVideoPlugin
 */

declare(strict_types=1);

namespace ClubCloudVideoPlugin;

/**
 * Class Plugin
 */
class Plugin {

	public const PLUGIN_NAMESPACE = 'cc';

	public const SETTING_VIDEO_SERVER = self::PLUGIN_NAMESPACE . '_video_server_url';
	public const SETTING_PRIVATE_KEY  = self::PLUGIN_NAMESPACE . '_private_key';

	public const SETTINGS = array(
		self::SETTING_VIDEO_SERVER,
		self::SETTING_PRIVATE_KEY,
	);

	/**
	 * Plugin constructor.
	 */
	public function __construct() {

		$private_key = get_option( self::SETTING_PRIVATE_KEY );

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		Factory::get_instance( Admin::class )->init();
		Factory::get_instance( TextOptionShortcode::class )->install();
		Factory::get_instance( AppShortcode::class, array( $private_key ) )->install();
		Factory::get_instance( MonitorShortcode::class, array( $private_key ) )->install();
		Factory::get_instance( JWT::class, array( $private_key ) )->install();
	}

	/**
	 * Initializer function, returns a instance of the plugin
	 *
	 * @return object
	 */
	public static function init() {
		return Factory::get_instance( self::class );
	}

	/**
	 * Register all settings with WordPress.
	 */
	public function register_settings() {
		foreach ( self::SETTINGS as $setting ) {
			register_setting( self::PLUGIN_NAMESPACE, $setting );
		}
	}
}
