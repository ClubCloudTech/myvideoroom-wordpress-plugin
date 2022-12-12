<?php
/**
 * Display section templates
 *
 * @package MyVideoRoomPlugin\Library
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

		$site_override = $security_video_dao->get_by_id(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( ! $site_override || ! $site_override->is_site_override_enabled() ) {
			return null;
		}
		// Format Plugin Base Link to Security Center.
		$plugin_foldername = plugin_basename( __DIR__ );
		$plugin_path       = strstr( $plugin_foldername, '/', true );
		$admin_page        = Security::MODULE_SECURITY_NAME;

		// get Site Override Status.

		$room_disabled  = $site_override->is_room_disabled();
		$room_anonymous = $site_override->is_anonymous_enabled();
		$roles          = $site_override->is_allow_role_control_enabled();

		// Rendering for NO URL Option buttons.
		if ( 'nourl' === $input_type ) {
			$path = null;
		} else {
			$path = ' href="' . get_admin_url() . 'admin.php?page=' . $admin_page . '" ';
		}

		$output = '<i class="myvideoroom-dashicons dashicons-lock" title="' . \esc_html__( 'Site Security Settings Apply', 'myvideoroom' ) . '"></i><a ' . $path . ' class="button button-primary" style="background-color:#daab33">' . esc_html__( 'Site Enforcement Active', 'myvideoroom' ) . '</a>';

		if ( $room_disabled && null === $input_type ) {
			$output .= '<a ' . $path . ' class="button button-primary" style="background-color:Red">' . esc_html__( 'Site Video Disabled', 'myvideoroom' ) . '</a>';
		}

		if ( $room_anonymous || $roles ) {
			$output .= '<a ' . $path . ' class="button button-primary" style="background-color:blue">' . esc_html__( 'Site Mandatory Settings Applied', 'myvideoroom' ) . '</a>';
		}

		if ( $room_anonymous ) {
			$output .= '<a ' . $path . ' class="button button-primary" style="background-color:blue">' . esc_html__( 'Site Anonymous Block Applied', 'myvideoroom' ) . '</a>';
		}

		return $output;
	}
} // End Class.
