<?php
/**
 * Display Icon Templates in Header of Meetings
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\Security\Library\SecurityRoomHelpers;
use MyVideoRoomPlugin\Module\Security\Security;

/**
 * Class Dependencies
 * Manages Shared Info across Certain Modules where cross module calls should be done to Core instead.
 */
class Dependencies {

	const MODULE_BUDDYPRESS_ID       = 434;
	const MODULE_SITE_VIDEO_ID       = 1095;
	const MODULE_PERSONAL_MEETING_ID = 1065;
	const ROOM_NAME_PERSONAL_MEETING = 'personal-video-room';
	const MODULE_SECURITY_ENTITY_ID  = 1029;
	const MODULE_SECURITY_ID         = 10;
	const MULTI_ROOM_HOST_SUFFIX     = '-hostsetting';

	/**
	 * Is Buddypress Active - checks if BuddyPress is enabled.
	 *
	 * @return bool
	 */
	public function is_buddypress_active(): bool {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		return is_plugin_active( 'buddypress/bp-loader.php' );
	}

	/**
	 * Is Security Module Active - checks if Security Module is enabled.
	 *
	 * @return bool
	 */
	public function is_security_active(): bool {
		$module_slug = Security::MODULE_SECURITY_NAME;
		return Factory::get_instance( Module::class )->is_module_active( $module_slug );
	}

	/**
	 * Render Security Disabled Admin Settings Page
	 *
	 * @param  array $input - the inbound menu.
	 * @return array - outbound menu.
	 */
	public function render_security_disabled_settings_page( $input = array() ): array {
		if ( $this->is_security_active() ) {
			return $input;
		}
		$admin_tab = new MenuTabDisplay(
			esc_html__( 'Security and Permissions', 'my-video-room' ),
			'securityperms',
			fn() => Factory::get_instance( SecurityRoomHelpers::class )->get_security_header_page()
		);
		array_push( $input, $admin_tab );
		return $input;
	}

}
