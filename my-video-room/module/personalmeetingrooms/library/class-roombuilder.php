<?php
/**
 * Extends the room builder with custom permissions
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\RoomPermissionsOption;

/**
 * Class Module
 */
class RoomBuilder {

	const PERMISSIONS_FIELD_NAME = 'use_personal_meetings_rooms';

	/**
	 * RoomBuilder constructor.
	 */
	public function __construct() {
		\add_filter(
			'myvideoroom_roombuilder_create_shortcode',
			array(
				$this,
				'generate_shortcode_constructor',
			),
			0,
			1
		);
		\add_filter( 'myvideoroom_roombuilder_permission_options', array( $this, 'add_permissions_option' ) );
		\add_filter(
			'myvideoroom_roombuilder_permission_options_selected',
			array(
				$this,
				'ensure_correct_permission_is_selected',
			)
		);
	}

	/**
	 * Add an option for custom settings to the room builder permissions section
	 *
	 * @param RoomPermissionsOption[] $options The current permissions options.
	 *
	 * @return RoomPermissionsOption[]
	 */
	public function add_permissions_option( array $options ): array {
		$permissions_preference     = Factory::get_instance( HttpPost::class )->get_radio_parameter( 'room_builder_room_permissions_preference' );
		$use_personal_meeting_rooms = ( self::PERMISSIONS_FIELD_NAME === $permissions_preference );

		$options[] = new RoomPermissionsOption(
			self::PERMISSIONS_FIELD_NAME,
			$use_personal_meeting_rooms,
			\esc_html__( 'Use personal meeting rooms', 'myvideoroom' ),
			\esc_html__(
				'Personal meeting rooms allows any WordPress user to be host of their own room.',
				'myvideoroom'
			),
		);

		return $options;
	}

	/**
	 * Ensure correct permission is selected
	 *
	 * @param RoomPermissionsOption[] $options The current permissions options.
	 *
	 * @return RoomPermissionsOption[]
	 */
	public function ensure_correct_permission_is_selected( array $options ): array {
		$permissions_preference = Factory::get_instance( HttpPost::class )->get_radio_parameter( 'room_builder_room_permissions_preference' );
		$use_custom_permissions = ( self::PERMISSIONS_FIELD_NAME === $permissions_preference );

		foreach ( $options as $permission ) {
			if ( self::PERMISSIONS_FIELD_NAME !== $permission->get_key() ) {
				$permission->set_as_selected( $permission->is_selected() && ! $use_custom_permissions );
			}
		}

		return $options;
	}

	/**
	 * Get the correct shortcode constructor
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function generate_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor ): AppShortcodeConstructor {
		$post_library = Factory::get_instance( HttpPost::class );

		$permissions_preference = $post_library->get_radio_parameter( 'room_builder_room_permissions_preference' );
		$use_custom_permissions = ( self::PERMISSIONS_FIELD_NAME === $permissions_preference );

		if ( $use_custom_permissions ) {
			$shortcode_constructor->add_custom_string_param( 'host', 'personalmeetingroom' );
		}

		return $shortcode_constructor;
	}
}
