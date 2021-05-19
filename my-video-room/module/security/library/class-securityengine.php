<?php
/**
 * Class Security- Provides the Render Block Host Function for Security.
 *
 * @package file class-security.php.
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;
use MyVideoRoomPlugin\Module\Security\Library\PageFilters;
use MyVideoRoomPlugin\Module\Security\Security;

/**
 * Class Security- Provides the Render Block Host Function for Security.
 */
class SecurityEngine {

	/**
	 * This function is called by all video switches to determine if they can return the video room, or if a setting has blocked their rendering
	 * It is a constructor only with other functions doing the filtering
	 *
	 * @param  ?int    $host_id      Host ID to Check.user_id of host to send to upstream filters.
	 * @param  ?string $room_type    String of type of room to filter on.
	 * @param  ?int    $module_id    ID of Module in case any active blocking by module.
	 * @param  ?string $room_name    Name of room to filter.
	 *
	 * @return ?string  - null if nothing blocks, or template page if it does.
	 */
	public function render_block( ?int $host_id, string $room_type, int $module_id = null, string $room_name = null ): ?string {
		// Activation/module.
		if ( ! Factory::get_instance( ModuleConfig::class )->module_activation_status( Security::MODULE_SECURITY_ID ) ) {
			return null;
		}

		// Getting Master Site override state.
		$site_override = Factory::get_instance( SecurityVideoPreference::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

		$room_disabled = Factory::get_instance( SecurityVideoPreference::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'room_disabled' );

		if ( $site_override && $room_disabled ) {
			return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_site();
		}

		/*
		Setup Environment Room Name Transformations for Special Cases.
		* Room names need to be modified for special cases - like multi-user scenarios.
		*/
		// Case BuddyPress Groups = need to pass room name, and host IDs as their creator and group name.
		if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
			global $bp;
			if ( \MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS === $room_name ) {
				$host_id   = $bp->groups->current_group->creator_id;
				$room_name = $bp->groups->current_group->slug;
			}
		}

		// Trapping any Host filter to set host status.
		$host_status = false;
		if ( strpos( $room_type, 'host' ) !== false ) {
			$host_status = true;
		}

		// First - Check Room Active - User Disable/Enable check.
		$disabled_block = Factory::get_instance( PageFilters::class )->block_disabled_room_video_render( $host_id, $room_name, $host_status, $room_type );
		if ( $disabled_block ) {
			return $disabled_block;
		}

		// Second Check Meeting/Room Type Module is Active in Control Panel - Module Check.
		if ( $module_id ) {
			$class_block = Factory::get_instance( PageFilters::class )->block_disabled_module_video_render( $module_id );
			if ( $class_block ) {
				return $class_block;
			}
		}

		// Check Users Signed Out Global Filter - Anonymous Check.
		if ( ! is_user_logged_in() ) {
			$signed_out_block = Factory::get_instance( PageFilters::class )->block_anonymous_room_video_render( $host_id, $room_name );
			if ( $signed_out_block ) {
				return $signed_out_block;
			}
		}

		// Check Allowed_Roles and Blocked Roles.
		$allowed_roles_block = Factory::get_instance( PageFilters::class )->allowed_roles_room_video_render( $host_id, $room_name, $host_status, $room_type );

		if ( $allowed_roles_block ) {
			return $allowed_roles_block;
		}

		// Action Hook to Display additional Form Entries from other Modules.
		$render_block = do_action( 'myvideoroom_security_render_block', $host_id, $room_name, $host_status, $room_type );
		if ( $render_block ) {
			return $render_block;
		}

		return null;
	}
}
