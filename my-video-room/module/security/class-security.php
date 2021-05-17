<?php
/**
 * Class Security- Provides the Render Block Host Function for Security.
 *
 * @package file class-security.php.
 */

namespace MyVideoRoomPlugin\Module\Security;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\Dao\ModuleConfig;
use MyVideoRoomPlugin\Core\SiteDefaults;
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
	const MULTI_ROOM_HOST_SUFFIX          = '-hostsetting';
	const MODULE_SECURITY_ID              = SiteDefaults::MODULE_SECURITY_ID; // Proxied to Main Core so Activation state can be queried by Core Modules.
	const MODULE_SECURITY_ENTITY_ID       = 1029;
	const MODULE_SECURITY_ADMIN_PAGE      = 'view-admin-settings-security';
	const MODULE_SECURITY_DISPLAY         = 'Room Permissions';
	const MODULE_SECURITY_ADMIN_LOCATION  = '/modules/security/views/view-settings-security.php';
	const MODULE_SECURITY_ENTITY_LOCATION = '/modules/security/views/view-settings-security-entity.php';
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
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function runtime() {
		// Register Menu in Admin Page.
		$this->security_menu_setup();
		// Turn on Runtime Filters.
		Factory::get_instance( PageFilters::class )->runtime_filters();

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
}
