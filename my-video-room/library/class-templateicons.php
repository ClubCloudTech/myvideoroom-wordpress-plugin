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
	 * @param int    $user_id   User ID to check.
	 * @param string $room_name Room Name to check.
	 *
	 * @return ?string - the icons.
	 */
	public function show_icon( int $user_id = null, string $room_name ): ?string {
		if ( ! $user_id && ! $room_name ) {
			return null;
		}
		if ( ! $user_id ) {
			$user_id = \get_current_user_id();
		}

		$video_default_settings_applied = Factory::get_instance( UserVideoPreferenceDAO::class )->get_by_id( $user_id, $room_name );

		$reception_enabled = true;
		$floorplan_enabled = false;
		$custom_video      = false;

		if ( $video_default_settings_applied ) {
			$reception_enabled = $video_default_settings_applied->is_reception_enabled();
			$floorplan_enabled = $video_default_settings_applied->is_floorplan_enabled();
			$custom_video      = $video_default_settings_applied->get_reception_video_url_setting() !== null;
		}

		$icon_output = null;

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
	 * @param ?string $template_icons The room name to use.
	 * @param int     $user_id        The user id to construct from.
	 * @param ?string $room_name      The room name to use.
	 * @param bool    $visitor_status - Whether guest/host.
	 *
	 * @return string
	 */
	public function add_default_video_icons_to_header( ?string $template_icons, int $user_id, string $room_name, bool $visitor_status = null ): string {
		if ( true === $visitor_status && ! $template_icons ) {
			$template_icons .= '<form method="post" action=""><input type="submit" name="submit" id="submit" class="mvr-main-refresh-button  myvideoroom-welcome-positive mvr-input-box" value="' . esc_html__( 'Refresh', 'myvideoroom' ) . '"  /></form>';
		} else {
			$template_icons .= $this->show_icon( $user_id, $room_name );
		}

		return $template_icons;
	}
	/**
	 * Filter for Adding Template Buttons to Shortcode Builder
	 *
	 * @param ?string $type The type of button to use.
	 *
	 * @return string
	 */
	public function format_button_icon( ?string $type ): string {
		switch ( $type ) {
			case 'login':
				$button_label = '<span title ="' . esc_html__( 'Login to access your room settings', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-admin-network"></span>';
				$button_class = 'mvr-ul-style-menu myvideoroom-button-separation';
				$a_class      = '';
				$id           = 'mvr-button-login';
				break;
			case 'photo':
				$button_label = '<span title ="' . esc_html__( 'Take your picture for room floorplans and easy recognition', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-camera"></span>';
				$button_class = 'mvr-ul-style-menu myvideoroom-button-separation';
				$a_class      = '';
				$id           = 'mvr-photo-image';
				break;
			case 'name':
				$button_label = '<span title ="' . esc_html__( 'You need to identify yourself for the meeting. Please enter a short name', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-format-chat"></span>';
				$button_class = 'mvr-ul-style-menu myvideoroom-button-separation';
				$a_class      = '';
				$id           = 'mvr-name-user';
				break;
			case 'checksound':
				$button_label  = '<span title ="' . esc_html__( 'Lets get your sound, and camera checked out and ready', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-admin-generic"></span>';
				$button_label .= '<span title ="' . esc_html__( 'Lets get your sound, and camera checked out and ready', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-controls-volumeon"></span>';
				$button_class  = 'mvr-ul-style-menu myvideoroom-button-separation';
				$a_class       = '';
				$id            = 'mvr-check-sound';
				break;
			case 'forgetme':
				$button_label = '<span title ="' . esc_html__( 'Delete your name, picture, and clear temporary information', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-hidden"></span>';
				$button_class = 'mvr-ul-style-menu myvideoroom-button-separation';
				$a_class      = '';
				$id           = 'mvr-forget-me';
				break;
		}

		return '<button id=' . $id . \wp_rand( 1, 10000 ) . ' class=" ' . $button_class . ' ' . $id . '" >
			<a class="' . $a_class . '">' . $button_label . '</a>
			</button>';

	}



}
