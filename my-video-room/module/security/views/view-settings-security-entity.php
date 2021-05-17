<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\DAO\RoomMap;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference as ShortcodeSecurityVideoPreference;

return function (): string {
	wp_enqueue_style( 'mvr-template' );
	wp_enqueue_style( 'mvr-menutab-header' );
	ob_start();

	if ( null !== ( sanitize_text_field( wp_unslash( $_GET['id'] ) ) ) ) {
		//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
		$room_id_string = esc_textarea( wp_unslash( $_GET['id'] ) );
		$room_id        = intval( $room_id_string );

	} else {
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
	echo Factory::get_instance( ShortcodeSecurityVideoPreference::class )->choose_settings( $room_id, $room_name, null, 'roomhost' );

	return ob_get_clean();
};
