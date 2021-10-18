<?php
/**
 * Renders the Main Header for all Meetings.
 *
 * @param string|null $current_user_setting
 * @param array       $available_layouts
 *
 * @package MyVideoRoomPlugin\Core\Views\view-roomheader.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\MeetingIdGenerator;

return function (
	?string $module_name,
	string $name_output,
	int $user_id = null,
	string $room_name = null,
	bool $visitor_status = false,
	string $invite_menu = null,
	string $post_site_title = null
) {
	if ( $visitor_status ) {

		$invite_menu = Factory::get_instance( MeetingIdGenerator::class )->invite_menu_shortcode(
			array(
				'type'    => 'guestlink',
				'user_id' => $user_id,
			)
		);
	}
	$template_icons = null;
	$template_icons = apply_filters( 'myvideoroom_template_icon_section', $template_icons, $user_id, $room_name, $visitor_status );

		$return_array = array(
			'module_name'     => $module_name,
			'name_output'     => $name_output,
			'room_name'       => $room_name,
			'visitor_status'  => $visitor_status,
			'invite_menu'     => $invite_menu,
			'post_site_title' => $post_site_title,
			'template_icons'  => $template_icons,
		);
		return $return_array;
};
