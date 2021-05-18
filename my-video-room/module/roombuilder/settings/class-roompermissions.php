<?php
/**
 * Returns the list of available options for setting room permissions
 *
 * @package MyVideoRoomPlugin/Module/RoomBuilder/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder\Settings;

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

/**
 * Class Reference
 */
class RoomPermissions {

	/**
	 * Get room permission options.
	 *
	 * @param ?AppShortcodeConstructor $app_config The current shortcode config.
	 *
	 * @return RoomPermissionsOption[]
	 */
	public function get_room_permission_options( AppShortcodeConstructor $app_config = null ): array {
		$room_permissions = array(
			new RoomPermissionsOption(
				'delegate_to_wordpress_roles',
				! $app_config || $app_config->is_host() === null,
				\esc_html__( 'Use WordPress roles to determine room permissions (recommended)', 'myvideoroom' ),
				\sprintf(
				/* translators: %s is a link to the room permissions admin page */
					\esc_html__(
						'When selected the permission of hosts and guests will be determined by the global settings. This means that you only need to only have one page, with a single shortcode. You can customise the global host settings in the %s page.',
						'myvideoroom'
					),
					'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_PERMISSIONS, false ) ) . '">' .
					\esc_html__( 'room permissions', 'myvideoroom' ) .
					'</a>'
				),
			),
			new RoomPermissionsOption(
				'shortcode_pair',
				$app_config && $app_config->is_host() !== null,
				\esc_html__( 'Generate a pair of shortcodes', 'myvideoroom' ),
				\esc_html__(
					' When selected this will create two shortcodes, one for the host and one for the guest. It is your responsibility to create two separate pages, one for each shortcode, and then manage access to each page. ',
					'myvideoroom'
				),
			),
		);

		// Add any extra options.
		$room_permissions = \apply_filters( 'myvideoroom_roombuilder_permission_options', $room_permissions );

		// Update the currently selected option.
		// This is done as a second step, so that regardless of the order in which options are added then the correct option can be selected.
		return \apply_filters( 'myvideoroom_roombuilder_permission_options_selected', $room_permissions );
	}

}
