<?php
/**
 * Class Security- Provides the Render Block Host Function for Security.
 *
 * @package file class-security.php.
 */

namespace MyVideoRoomPlugin\Module\Security;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\Dao\ModuleConfig;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\DAO\DBSetup;
use MyVideoRoomPlugin\Module\Security\Library\PageFilters;

/**
 * Class Security- Provides the Render Block Host Function for Security.
 */
class Security {

	const TABLE_NAME_SECURITY_CONFIG      = 'myvideoroom_extras_security_config';
	const MODULE_SECURITY_NAME            = 'security-module';
	const MODULE_SECURITY_ENTITY          = 'security-entity';
	const HOST_TABLE_SUFFIX               = 'host-permission';
	const MULTI_ROOM_HOST_SUFFIX          = Dependencies::MULTI_ROOM_HOST_SUFFIX;
	const MODULE_SECURITY_ID              = Dependencies::MODULE_SECURITY_ID; // Proxied to Main Core so Activation state can be queried by Core Modules.
	const MODULE_SECURITY_ENTITY_ID       = Dependencies::MODULE_SECURITY_ENTITY_ID;
	const MODULE_SECURITY_ADMIN_PAGE      = 'view-admin-settings-security';
	const MODULE_SECURITY_DISPLAY         = ' Advanced Room Permissions';
	const MODULE_SECURITY_ADMIN_LOCATION  = '/module/security/views/view-settings-security.php';
	const MODULE_SECURITY_ENTITY_LOCATION = '/module/security/views/view-settings-security-entity.php';
	const PERMISSIONS_TABLE               = 'security-default-permissions';

	/**
	 * Initialise On Module Activation.
	 * Once off functions for activating Module.
	 */
	public function initialise_module() {
		// Install Room Security Config table.
		Factory::get_instance( DBSetup::class )->install_security_config_table();
		// Register and Activate Module In Module Table.
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_SECURITY_NAME, self::MODULE_SECURITY_ID, true, self::MODULE_SECURITY_ADMIN_LOCATION );
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_SECURITY_ENTITY, self::MODULE_SECURITY_ENTITY_ID, true, self::MODULE_SECURITY_ENTITY_LOCATION );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SECURITY_ID, true );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SECURITY_ENTITY_ID, true );

	}

	/**
	 * De-Initialise On Module De-activation.
	 * Once off functions for activating Module.
	 */
	public function de_initialise_module() {
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SECURITY_ID, false );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_SECURITY_ENTITY_ID, false );
	}



	/**
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function init() {

		// Turn on Runtime Filters.
		Factory::get_instance( PageFilters::class )->runtime_filters();
		Factory::get_instance( self::class )->security_menu_setup();

	}
	/**
	 * Setup of Module Menu
	 */
	public function security_menu_setup() {
		add_action( 'mvr_module_submenu_add', array( self::class, 'security_menu_button' ) );
	}
	/**
	 * Render Module Menu.
	 */
	public function security_menu_button() {
		$name = self::MODULE_SECURITY_DISPLAY;
		$slug = self::MODULE_SECURITY_NAME;
		//phpcs:ignore --WordPress.WP.I18n.NonSingularStringLiteralText - $name is a constant text literal already.
		$display = esc_html__( $name, 'myvideoroom' );
		echo '<a class="mvr-menu-header-item" href="?page=my-video-room-extras&tab=' . esc_html( $slug ) . '">' . esc_html( $display ) . '</a>';
	}
	/**
	 * Render Security Admin Page.
	 */
	public function render_security_admin_page() {
		$active_tab = self::MODULE_SECURITY_NAME;
		$path       = Factory::get_instance( ModuleConfig::class )->get_module_admin_path( $active_tab );
		$render     = require WP_PLUGIN_DIR . '/my-video-room/' . $path;
		$messages   = array();
		//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Items already Sanitised.
		echo $render( $messages );
		return 'Powered by MyVideoRoom';
	}
}
