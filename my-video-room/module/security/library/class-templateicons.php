<?php
/**
 * Display Icon Templates in Header of Meetings and Shortcodes
 *
 * @package MyVideoRoomPlugin\Module\Security\Library\TemplateIcons
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Factory;

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

		$security_video_dao = Factory::get_instance( SecurityVideoPreferenceDAO::class );

		$anonymous_enabled          = $security_video_dao->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		$allow_role_control_enabled = $security_video_dao->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
		$restrict_to_friends        = $security_video_dao->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
		$restrict_to_groups         = $security_video_dao->read_security_settings( $user_id, $room_name, 'restrict_group_to_members_enabled' );
		$icon_output                = null;

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
	/**
	 * Filter for Adding Template Buttons to Shortcode Builder
	 *
	 * @param string  $template_icons The room name to use.
	 * @param int     $user_id The user id to construct from.
	 * @param ?string $room_name The room name to use.
	 *
	 * @return string
	 */
	public function add_default_video_icons_to_header( ?string $template_icons, int $user_id, string $room_name ): string {
		$template_icons .= Factory::get_instance( self::class )->show_icon( $user_id, $room_name );
		return $template_icons;
	}
}
