<?php
/**
 * Display Icon Templates in Header of Meetings and Shortcodes
 *
 * @package MyVideoRoomPlugin\Module\Security\Library\SecurityNotifications
 */

namespace MyVideoRoomPlugin\Module\Security\Library;

use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class SecurityNotifications
 * Provides Iconography for Header Display Information in Front end.
 */
class SecurityNotifications {

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

	/**
	 * Show Security Status.
	 * Takes UserID and Room Name from Template pages and returns formatted room information Buttons for Control Forms.
	 *
	 * @param  string $output Value in Filter.
	 * @param  int    $user_id   User ID to check.
	 * @param  string $room_name Room Name to check.
	 *
	 * @return ?string - The Buttons.
	 */
	public function show_security_admin_status( string $output = null, int $user_id, string $room_name ): ?string {
		if ( ! $user_id && ! $room_name ) {
			return $output;
		}

		// room permissions info.
		$site_override              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		$room_disabled              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'room_disabled' );
		$anonymous_enabled          = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		$allow_role_control_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
		$block_role_control         = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'block_role_control_enabled' );
		$output                     = null;

		if ( ! $site_override ) {
			if ( ! $room_disabled ) {
				$output .= '<p class="mvr-main-button-enabled" >' . esc_html__( 'Site Enabled', 'my-video-room' ) . '</p>';
			} else {
				$output .= '<p class="mvr-main-button-disabled button" >' . esc_html__( 'Site Disabled', 'my-video-room' ) . '</p>';
			}
			if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
				$restrict_group_to_members_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'restrict_group_to_members_enabled' );
				$restrict_to_friends               = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
				if ( $restrict_group_to_members_enabled ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Restricted to Members', 'my-video-room' ) . '</p>';
				}
				if ( $restrict_to_friends ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Restricted to Friends', 'my-video-room' ) . '</p>';
				}
			}
			if ( $allow_role_control_enabled ) {
				$db_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allowed_roles' );
				if ( ! $db_setting ) {
					$db_setting = 'No One';
				}
				if ( $block_role_control ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Member Restrictions are Excluding : ', 'my-video-room' ) . $db_setting . '</p>';
				} else {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Member Restrictions Only Allowing : ', 'my-video-room' ) . '' . $db_setting . '</p>';
				}
			}

			if ( $anonymous_enabled ) {
				$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Anonymous Disabled', 'my-video-room' ) . '</p>';
			}
		} else {
			$output .= Factory::get_instance( SecurityButtons::class )->site_wide_enabled( 'nourl' );
			$output .= '<p class="mvr-preferences-paragraph">' . esc_html__( 'You are overriding User and Room settings with ones applied centrally below.', 'my-video-room' ) . '</p>';
		}
		return $output;
	}

	/**
	 * Show Security Shortcode Setting Status.
	 * Takes UserID and Room Name from Template pages and returns formatted room information Buttons for Control Forms.
	 *
	 * @param  string $output - filter content.
	 * @param  int    $user_id   User ID to check.
	 * @param  string $room_name Room Name to check.
	 *
	 * @return ?string - The Buttons.
	 */
	public function show_security_settings_status( string $output = null, int $user_id, string $room_name ): ?string {
		if ( ! $user_id && ! $room_name ) {
			return $output;
		}
		// room permissions info.
		$site_override              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		$room_disabled              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'room_disabled' );
		$anonymous_enabled          = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		$allow_role_control_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
		$block_role_control         = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'block_role_control_enabled' );
		$output                     = null;

		if ( ! $site_override ) {
			if ( ! $room_disabled ) {
				$output .= '<p class="mvr-main-button-enabled" >' . esc_html__( 'Room Enabled', 'my-video-room' ) . '</p>';
			} else {
				$output .= '<p class="mvr-main-button-disabled" >' . esc_html__( 'Room Disabled', 'my-video-room' ) . '</p>';
			}
			if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
				$restrict_group_to_members_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'restrict_group_to_members_enabled' );
				$restrict_to_friends               = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
				if ( $restrict_group_to_members_enabled ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Restricted to Members', 'my-video-room' ) . '</p>';
				}
				if ( $restrict_to_friends ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Restricted to Friends', 'my-video-room' ) . '</p>';
				}
			}
			if ( $allow_role_control_enabled ) {
				$db_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allowed_roles' );
				if ( ! $db_setting ) {
					$db_setting = 'No One';
				}
				if ( $block_role_control ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Member Restrictions are Excluding : ', 'my-video-room' ) . $db_setting . '</p>';
				} else {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Member Restrictions Only Allowing : ', 'my-video-room' ) . '' . $db_setting . '</p>';
				}
			}

			if ( $anonymous_enabled ) {
				$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Anonymous Disabled', 'my-video-room' ) . '</p>';
			}
		} else {
			$output .= Factory::get_instance( SecurityButtons::class )->site_wide_enabled( 'nourl' );
			$output .= '<p class="mvr-preferences-paragraph">' . esc_html__( 'An Administrator is overriding your settings with ones applied centrally. Certains Settings stored here may not be applied', 'my-video-room' ) . '</p>';
		}
		return $output;
	}

	/**
	 * Show Security Shortcode Room Hosts Status.
	 * Takes UserID and Room Name from Template pages and returns formatted room information Buttons for Control Forms.
	 *
	 * @param  string $output - filter content.
	 * @param  int    $user_id   User ID to check.
	 * @param  string $room_name Room Name to check.
	 *
	 * @return ?string - The Buttons.
	 */
	public function show_security_roomhosts_status( string $output = null, int $user_id, string $room_name ): ?string {
		if ( ! $user_id && ! $room_name ) {
			return $output;
		}
		// room permissions info.
		$site_override              = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		$anonymous_enabled          = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'anonymous_enabled' );
		$allow_role_control_enabled = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allow_role_control_enabled' );
		$block_role_control         = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'block_role_control_enabled' );
		$output                     = null;

		if ( ! $site_override ) {

			if ( $allow_role_control_enabled ) {
				$db_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'allowed_roles' );
				if ( ! $db_setting ) {
					$db_setting = 'No One';
				}
				if ( $block_role_control ) {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Hosts All Except : ', 'my-video-room' ) . str_replace( '|', ' - ', $db_setting ) . '</p>';
				} else {
					$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Hosts Only Allowing : ', 'my-video-room' ) . str_replace( '|', ' - ', $db_setting ) . '</p>';
				}
			}

			if ( $anonymous_enabled ) {
				$output .= '<p class="mvr-main-button-notice">' . esc_html__( 'Anonymous Hosting Enabled', 'my-video-room' ) . '</p>';
			}
		} else {
			$output .= Factory::get_instance( SecurityButtons::class )->site_wide_enabled( 'nourl' );
			$output .= '<p class="mvr-preferences-paragraph">' . esc_html__( 'An Administrator is overriding your settings with ones applied centrally. Certains Settings stored here may not be applied', 'my-video-room' ) . '</p>';
		}
		return $output;
	}
}
