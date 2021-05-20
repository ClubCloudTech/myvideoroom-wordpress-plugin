<?php
/**
 * Display section templates
 *
 * @package MyVideoRoomExtrasPlugin\Library
 */

namespace MyVideoRoomPlugin\Module\Security\Templates;

use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\Module\Security\Security;

/**
 * Class SectionTemplate
 */
class SecurityButtons {

	/**
	 * Check Room Enabled, and Site Overrides For Room Enabled.
	 *
	 * @param ?string $input_type The type of room to check.
	 *
	 * @return ?string
	 */
	public static function site_wide_enabled( string $input_type = null ): ?string {
		$security_video_dao = Factory::get_instance( SecurityVideoPreferenceDao::class );

		$site_override = $security_video_dao->read_security_settings(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT,
			'site_override_enabled'
		);

		if ( ! $site_override ) {
			return null;
		}
		// Format Plugin Base Link to Security Center.
		$plugin_foldername = plugin_basename( __DIR__ );
		$plugin_path       = strstr( $plugin_foldername, '/', true );
		$admin_page        = Security::MODULE_SECURITY_ADMIN_PAGE;

		// get Site Override Status.

		$room_disabled = $security_video_dao->read_security_settings(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT,
			'room_disabled'
		);

		$room_anonymous = $security_video_dao->read_security_settings(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT,
			'anonymous_enabled'
		);

		$roles = $security_video_dao->read_security_settings(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT,
			'allow_role_control_enabled'
		);

		// Rendering for NO URL Option buttons.
		if ( 'nourl' === $input_type ) {
			$path = null;
		} else {
			$path = ' href="' . get_admin_url() . 'admin.php?page=' . $plugin_path . '&tab=' . $admin_page . '#disabled" ';
		}

		$output = '<a ' . $path . ' class="button button-primary" style="background-color:#daab33">' . esc_html__( 'Site Enforcement Active', 'my-video-room' ) . '</a>';

		if ( $room_disabled && null === $input_type ) {
			$output .= '<a ' . $path . ' class="button button-primary" style="background-color:Red">' . esc_html__( 'Site Video Disabled', 'my-video-room' ) . '</a>';
		}

		if ( ( $room_anonymous || $roles ) && $site_override ) {
			$output .= '<a ' . $path . ' class="button button-primary" style="background-color:blue">' . esc_html__( 'Site Mandatory Settings Applied', 'my-video-room' ) . '</a>';
		}

		if ( $room_anonymous && $site_override ) {
				$output .= '<a ' . $path . ' class="button button-primary" style="background-color:blue">' . esc_html__( 'Site Anonymous Block Applied', 'my-video-room' ) . '</a>';
		}

		return $output;
	}
} // End Class.
