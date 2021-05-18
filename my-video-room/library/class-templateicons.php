<?php
/**
 * Display Icon Templates in Header of Meetings
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDAO;

/**
 * Class TemplateIcons
 * Provides Iconography for Header Display Information in Front end.
 */
class TemplateIcons {

	/**
	 * Takes UserID and Room Name from Template pages and returns formatted room information icons.
	 *
	 * @param  int    $user_id   User ID to check.
	 * @param  string $room_name Room Name to check.
	 *
	 * @return ?string - the icons.
	 */
	public function show_icon( int $user_id, string $room_name ): ?string {
		if ( ! $user_id && ! $room_name ) {
			return null;
		}

		$user_video_dao     = Factory::get_instance( UserVideoPreferenceDAO::class );
		$security_video_dao = Factory::get_instance( SecurityVideoPreferenceDAO::class );

		$reception_enabled          = $user_video_dao->read_user_video_settings( $user_id, $room_name, 'reception_enabled' );
		$floorplan_enabled          = $user_video_dao->read_user_video_settings( $user_id, $room_name, 'show_floorplan' );
		$custom_video               = $user_video_dao->read_user_video_settings( $user_id, $room_name, 'reception_video_enabled' );
		$anonymous_enabled          = $security_video_dao->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		$allow_role_control_enabled = $security_video_dao->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
		$restrict_to_friends        = $security_video_dao->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
		$restrict_to_groups         = $security_video_dao->read_security_settings( $user_id, $room_name, 'restrict_group_to_members_enabled' );
		$icon_output                = null;

		if ( $reception_enabled || $floorplan_enabled ) {
			$icon_output .= $this->create_icon(
				'lock',
				__( 'Your guests will see the reception template of your choice and will not be admitted into the room until you drag their icon in.', 'myvideoroom' )
			);
		}

		if ( ! $reception_enabled && ! $floorplan_enabled ) {
			$icon_output .= $this->create_icon(
				'unlock',
				__( 'Your guests can freely enter and exit your room if you are in it.', 'myvideoroom' )
			);
		}

		if ( $floorplan_enabled ) {
			$icon_output .= $this->create_icon(
				'welcome-view-site',
				__( 'Your guests will not see the image of the room floorplan and only have a classic video experience.', 'myvideoroom' )
			);
		}

		if ( $custom_video && ( $reception_enabled || $floorplan_enabled ) ) {
			$icon_output .= $this->create_icon(
				'playlist-video',
				__( 'A custom video is playing in your reception.', 'myvideoroom' )
			);
		}

		if ( $anonymous_enabled ) {
			$icon_output .= $this->create_icon(
				'admin-users',
				__( 'Users must be signed in to access your room.', 'myvideoroom' )
			);
		}

		if ( $allow_role_control_enabled ) {
			$icon_output .= $this->create_icon(
				'id',
				__( 'Guests must belong to specific roles for access to your room.', 'myvideoroom' )
			);
		}

		if ( $restrict_to_friends ) {
			$icon_output .= $this->create_icon(
				'share',
				__( 'Guests must be friends/connected to you to access your room,', 'myvideoroom' )
			);
		}

		if ( $restrict_to_groups ) {
			$icon_output .= $this->create_icon(
				'format-chat',
				__( 'Guests must be a member of this group (or moderator/admin) to access your room.', 'myvideoroom' )
			);
		}

		return $icon_output;
	}

	/**
	 * Create an icon
	 *
	 * @param string $icon  The icon.
	 * @param string $title The text.
	 *
	 * @return string
	 */
	private function create_icon( string $icon, string $title ): string {
		return '<i class="card dashicons mvr-icons dashicons-' . esc_attr( $icon ) . '" title="' . esc_html( $title ) . '"></i>';
	}
}
