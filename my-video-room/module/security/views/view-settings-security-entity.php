<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @package MyVideoRoomPlugin\Views\Public
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference as ShortcodeSecurityVideoPreference;

return function (): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	ob_start();

	$http_get_library = Factory::get_instance( HttpGet::class );
	$room_id          = $http_get_library->get_integer_parameter( 'id' );

	if ( ! $room_id ) {
		echo 'No Room ID Provided - exiting';
		wp_safe_redirect( get_site_url() );
		exit;
	}

	$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
	$room_name   = $room_object->room_name . Security::MULTI_ROOM_HOST_SUFFIX;
	if ( ! $room_name ) {
		return 'Invalid Room Number';
	}
	//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Escaped.
	echo Factory::get_instance( ShortcodeSecurityVideoPreference::class )->choose_settings( $room_id, $room_name, 'roomhost', true );

	return ob_get_clean();
};
