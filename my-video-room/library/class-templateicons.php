<?php
/**
 * Display Icon Templates in Header of Meetings
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

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

		$user_video_dao                 = Factory::get_instance( UserVideoPreferenceDAO::class );
		$video_default_settings_applied = Factory::get_instance( UserVideoPreferenceDAO::class )->read( $user_id, $room_name );
		$reception_enabled              = $user_video_dao->read_user_video_settings( $user_id, $room_name, 'reception_enabled' );
		$floorplan_enabled              = $user_video_dao->read_user_video_settings( $user_id, $room_name, 'show_floorplan' );
		$custom_video                   = $user_video_dao->read_user_video_settings( $user_id, $room_name, 'reception_video_enabled' );
		$icon_output                    = null;

		if ( ! $video_default_settings_applied ) {
			$icon_output .= $this->create_icon(
				'warning',
				__( 'Default Video Settings are being Applied at the site level as you haven\'t set any preferences yet.', 'myvideoroom' )
			);
		}

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
	 * @param bool    $visitor_status - Whether guest/host.
	 *
	 * @return string
	 */
	public function add_default_video_icons_to_header( ?string $template_icons, int $user_id, string $room_name, bool $visitor_status = null ): string {
		if ( true === $visitor_status && ! $template_icons ) {
			$template_icons .= '<form method="post" action=""><input type="submit" name="submit" id="submit" class="button mvr-form-button mvr-form-button-max" value="Exit Meeting"  />';
		} else {
			$template_icons .= Factory::get_instance( self::class )->show_icon( $user_id, $room_name );
		}
		return $template_icons;
	}

}
