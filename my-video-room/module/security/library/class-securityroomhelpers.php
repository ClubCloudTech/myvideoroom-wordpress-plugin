<?php
/**
 * Addon functionality for Filtering Users from Accessing Rooms
 *
 * @package Class SecurityRoomHelpers.
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Library\UserRoles;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;

/**
 * PageFilters - Security Filter Defaults for Renderblock Function.
 */
class SecurityRoomHelpers  {

	/**
	 * Render Site Video Admin Settings Page
	 *
	 * @param  array $input - the inbound menu.
	 * @return array - outbound menu.
	 */
	public function render_security_admin_settings_page( $input = array() ): array {

		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Security and Permissions', 'my-video-room' ),
			'securityperms',
			fn() => Factory::get_instance( self::class )->get_security_admin_page()
		);
		array_push( $input, $admin_tab );
		return $input;
	}

	/**
	 * Get_sitevideo_admin_page - returns admin page
	 *
	 * @return string
	 */
	private function get_security_admin_page() {
		$page = require __DIR__ . '/../views/view-settings-security.php';
		return $page();
	}

}
