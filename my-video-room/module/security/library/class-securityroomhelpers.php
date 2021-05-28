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
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Security;

/**
 * PageFilters - Security Filter Defaults for Renderblock Function.
 */
class SecurityRoomHelpers {

	/**
	 * Render Security Admin Settings Page
	 *
	 * @param array $input - the inbound menu.
	 *
	 * @return array - outbound menu.
	 */
	public function render_security_admin_settings_page( array $input ): array {

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
	private function get_security_admin_page(): string {
		return ( require __DIR__ . '/../views/view-settings-security.php' )();
	}

	/**
	 * Security Plugin Module Disable.
	 *
	 * @param int $module_id - the module ID of the Feature Database.
	 *
	 * @return bool
	 */
	public function security_disable_feature_module( int $module_id ): bool {
		if ( Security::MODULE_SECURITY_ID !== $module_id ) {
			return false;
		}
		$module_slug     = Security::MODULE_SECURITY_NAME;
		$security_module = Factory::get_instance( Module::class )->get_module( $module_slug );
		Factory::get_instance( Modules::class )->deactivate_module( $security_module );
		header( 'Refresh:0' );

		return true;
	}
	/**
	 * Security Update Post ID's for regeneration.
	 *
	 *  @param  int $post_id - the new post ID.
	 *  @param  int $old_post_id - the old post ID.
	 *  @return bool
	 */
	public function update_security_post_id( int $post_id, int $old_post_id ) {

		return Factory::get_instance( SecurityVideoPreference::class )->update_user_id( $post_id, $old_post_id );
	}

}
