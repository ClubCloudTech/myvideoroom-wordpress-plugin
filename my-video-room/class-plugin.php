<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser;

/**
 * Class Plugin
 */
class Plugin {

	public const PLUGIN_NAMESPACE   = 'myvideoroom';
	public const SETTINGS_NAMESPACE = 'settings';

	public const SETTING_SERVER_DOMAIN     = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_server_domain';
	public const SETTING_ACTIVATION_KEY    = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_activation_key';
	public const SETTING_ACTIVATED_MODULES = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_activated_modules';

	public const SETTING_ACCESS_TOKEN = self::PLUGIN_NAMESPACE . '_access_token';
	public const SETTING_PRIVATE_KEY  = self::PLUGIN_NAMESPACE . '_private_key';

	public const CAP_GLOBAL_ADMIN = 'myvideoroom-global-admin';

	public const SETTINGS = array(
		self::SETTING_SERVER_DOMAIN,
		self::SETTING_ACTIVATION_KEY,
		self::SETTING_ACTIVATED_MODULES,
	);

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		$private_key = get_option( self::SETTING_PRIVATE_KEY );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __DIR__ . '/index.php' ), array( $this, 'add_action_links' ) );

		Factory::get_instance( Admin::class )->init();
		Factory::get_instance( TextOptionShortcode::class )->init();
		Factory::get_instance( AppShortcode::class, array( $private_key ) )->init();
		Factory::get_instance( MonitorShortcode::class, array( $private_key ) )->init();
		Factory::get_instance( ShortcodeRoomVisualiser::class )->init();
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
		register_setting( self::PLUGIN_NAMESPACE, self::SETTING_ACCESS_TOKEN );
	}

	/**
	 * Add custom action links to the plugin page
	 *
	 * @param array $actions The array of plugin action links.
	 *
	 * @return array
	 */
	public function add_action_links( array $actions ): array {
		$links = array(
			'Settings'  => admin_url( 'advanced.php?page=my-video-room-global' ),
			'Reference' => admin_url( 'advanced.php?page=my-video-room' ),
			'Support'   => 'https://clubcloud.tech',
		);

		foreach ( $links as $link_name => $link_address ) {
			$actions[] = "<a href=\"{$link_address}\">{$link_name}</a>";
		}

		return $actions;
	}
}
