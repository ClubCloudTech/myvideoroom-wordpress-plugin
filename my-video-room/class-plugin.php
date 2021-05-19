<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Shortcode\App;
use MyVideoRoomPlugin\Shortcode\RoomInfo;

/**
 * Class Plugin
 */
class Plugin {

	public const PLUGIN_NAMESPACE   = 'myvideoroom';
	public const SETTINGS_NAMESPACE = 'settings';

	public const SETTING_SERVER_DOMAIN     = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_server_domain';
	public const SETTING_ACTIVATION_KEY    = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_activation_key';
	public const SETTING_ACCESS_TOKEN      = self::PLUGIN_NAMESPACE . '_access_token';
	public const SETTING_PRIVATE_KEY       = self::PLUGIN_NAMESPACE . '_private_key';
	public const SETTING_ACTIVATED_MODULES = self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE . '_activated_modules';

	public const CAP_GLOBAL_HOST = 'myvideoroom-global-host';

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		$private_key = \get_option( self::SETTING_PRIVATE_KEY );
		\do_action( 'myvideoroom_init' );

		\add_action( 'admin_init', array( $this, 'register_settings' ) );
		\add_filter(
			'plugin_action_links_' . \plugin_basename( __DIR__ . '/index.php' ),
			array( $this, 'add_action_links' )
		);

		Factory::get_instance( Admin::class )->init();

		$active_modules = Factory::get_instance( Module::class )->get_active_modules();

		foreach ( $active_modules as $module ) {
			$module->instantiate();
		}

		Factory::get_instance( App::class, array( $private_key ) )->init();
		Factory::get_instance( RoomInfo::class )->init();
		Factory::get_instance( SiteDefaults::class )->init();
	}

	/**
	 * Initializer function, returns a instance of the plugin
	 *
	 * @return \MyVideoRoomPlugin\Plugin
	 */
	public static function init() {
		return Factory::get_instance( self::class );
	}

	/**
	 * Register all settings with WordPress.
	 */
	public function register_settings() {
		\register_setting( self::PLUGIN_NAMESPACE . '_' . self::SETTINGS_NAMESPACE, self::SETTING_ACTIVATION_KEY );
		\register_setting( self::PLUGIN_NAMESPACE, self::SETTING_PRIVATE_KEY );
		\register_setting( self::PLUGIN_NAMESPACE, self::SETTING_ACCESS_TOKEN );
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
			'Settings'  => \menu_page_url( PageList::PAGE_SLUG_GETTING_STARTED, false ),
			'Reference' => \menu_page_url( PageList::PAGE_SLUG_REFERENCE, false ),
			'Support'   => 'https://clubcloud.tech',
		);

		foreach ( $links as $link_name => $link_address ) {
			$actions[] = "<a href=\"{$link_address}\">{$link_name}</a>";
		}

		return $actions;
	}
}
