<?php
/**
 * Addon functionality for Filtering Users from Accessing Rooms
 *
 * @package Class SecurityRoomHelpers.
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Admin\Modules;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\Security\Security;

/**
 * PageFilters - Security Filter Defaults for Renderblock Function.
 */
class SecurityRoomHelpers {

	/**
	 * Render Security Admin Settings Page
	 *
	 * @param  array $input - the inbound menu.
	 * @return array - outbound menu.
	 */
	public function render_security_admin_settings_page( $input = array() ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Security and Permissions', 'my-video-room' ),
			'securityperms',
			fn() => $this->get_security_admin_page()
		);
		array_push( $input, $admin_tab );
		return $input;
	}

	/**
	 * Get Security - returns admin page
	 *
	 * @return string
	 */
	private function get_security_admin_page() {
		$page = require __DIR__ . '/../views/view-settings-security.php';
		return $page();
	}

	/**
	 * Get Security Header- returns Security Header page
	 *
	 * @return string
	 */
	public function get_security_header_page() {
		$page = require __DIR__ . '/../views/view-settings-securityheader.php';
		return $page();
	}

	/**
	 * Security Plugin Module Disable.
	 *
	 *  @param  int $module_id - the module ID of the Feature Database.
	 *  @return bool
	 */
	public function security_disable_feature_module( int $module_id ) {
		if ( Security::MODULE_SECURITY_ID !== $module_id ) {
			return false;
		}
		$module_slug     = Security::MODULE_SECURITY_NAME;
		$security_module = Factory::get_instance( Module::class )->get_module( $module_slug );
		Factory::get_instance( Modules::class )->deactivate_module( $security_module );
		header( 'Refresh:0' );
		return true;
	}

}
