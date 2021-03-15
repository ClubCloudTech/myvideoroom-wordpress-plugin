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

	public const PLUGIN_NAMESPACE   = 'clubcloud_video';
	public const SETTINGS_NAMESPACE = 'settings';

	public const SETTING_SERVER_DOMAIN  = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_server_domain';
	public const SETTING_ACTIVATION_KEY = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_activation_key';

	public const SETTING_PRIVATE_KEY = self::PLUGIN_NAMESPACE . '_private_key';

	public const SETTINGS = array(
		self::SETTING_SERVER_DOMAIN,
		self::SETTING_ACTIVATION_KEY,
	);

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		$private_key = get_option( self::SETTING_PRIVATE_KEY );

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		Factory::get_instance( Admin::class )->init();
		Factory::get_instance( JWT::class, array( $private_key ) )->init();

		Factory::get_instance( TextOptionShortcode::class )->init();
		Factory::get_instance( AppShortcode::class, array( $private_key ) )->init();
		Factory::get_instance( MonitorShortcode::class, array( $private_key ) )->init();
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
			register_setting( self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE, $setting );
		}

		register_setting( self::PLUGIN_NAMESPACE, self::SETTING_PRIVATE_KEY );
	}
}
