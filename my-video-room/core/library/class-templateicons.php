<?php
/**
 * Display Icon Templates in Header of Meetings
 *
 * @package MyVideoRoomPlugin\Core\Library
 */

namespace MyVideoRoomPlugin\Core\Library;

use MyVideoRoomExtrasPlugin\Modules\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\Dao\UserVideoPreference as UserVideoPreferenceDAO;

/**
 * Class TemplateIcons
 * Provides Iconography for Header Display Information in Front end.
 */
class TemplateIcons {

	/**
	 * Takes UserID and Room Name from Template pages and returns formatted room information icons.
	 *
	 * @param  int    $user_id - User ID to check.
	 * @param  string $room_name - Room Name to check.
	 * @return string - the icons.
	 */
	public function show_icon( int $user_id, string $room_name ) {
		$reception_enabled          = Factory::get_instance( UserVideoPreferenceDAO::class )->read_user_video_settings( $user_id, $room_name, 'reception_enabled' );
		$floorplan_enabled          = Factory::get_instance( UserVideoPreferenceDAO::class )->read_user_video_settings( $user_id, $room_name, 'show_floorplan' );
		$custom_video               = Factory::get_instance( UserVideoPreferenceDAO::class )->read_user_video_settings( $user_id, $room_name, 'reception_video_enabled' );
		$anonymous_enabled          = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		$allow_role_control_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
		$restrict_to_friends        = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
		$restrict_to_groups         = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'restrict_group_to_members' );
		$icon_output                = null;
		if ( $reception_enabled || $floorplan_enabled ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-lock" title="Your Guests will see the Reception Template of your choice and will not be admitted into the room until you drag their icon in."></i>';
		}
		if ( ! $reception_enabled && ! $floorplan_enabled ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-unlock" title="Your Guests can freely enter and exit your room if you are in it."></i>';
		}
		if ( $floorplan_enabled ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-welcome-view-site" title="Your Guests will not see the Image of the Room Floorplan and only have a classic Video Experience"></i>';
		}
		if ( $custom_video && ( $reception_enabled || $floorplan_enabled ) ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-playlist-video" title="A custom video is playing in your reception"></i>';
		}
		if ( $anonymous_enabled ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-admin-users" title="Users Must be signed in to access your room"></i>';
		}
		if ( $allow_role_control_enabled ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-id" title="Guests must belong to specific roles for access to your room"></i>';
		}
		if ( $restrict_to_friends ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-share" title="Guests must be friends/connected to you to access your room"></i>';
		}
		if ( $restrict_to_groups ) {
			$icon_output .= '<i class="card dashicons mvr-icons dashicons-format-chat" title="Guests must be a member of this group (or moderator/admin) to access your room"></i>';
		}
		return $icon_output;

	}

}
