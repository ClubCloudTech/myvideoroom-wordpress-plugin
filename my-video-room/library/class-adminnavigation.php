<?php
/**
 * Manages the navigation
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Admin;

/**
 * Class Admin Navigation
 */
class AdminNavigation {

	const PAGE_SLUG_GETTING_STARTED = 'my-video-room';
	const PAGE_SLUG_ROOM_TEMPLATES  = 'my-video-room-templates';
	const PAGE_SLUG_REFERENCE       = 'my-video-room-shortcode-reference';
	const PAGE_SLUG_PERMISSIONS     = 'my-video-room-permissions';
	const PAGE_SLUG_MODULES         = 'my-video-room-modules';
	const PAGE_SLUG_ADVANCED        = 'my-video-room-advanced';

	/**
	 * Get the navigation items
	 *
	 * @param Admin $admin_manager The admin manager, required to activate the callbacks.
	 *
	 * @return array[]
	 */
	public function get_navigation_items( Admin $admin_manager ): array {
		$navigation_items = array(
			self::PAGE_SLUG_GETTING_STARTED => array(
				'title'    => esc_html__( 'Getting Started', 'myvideoroom' ),
				'callback' => array( $admin_manager, 'create_getting_started_page' ),
			),

			self::PAGE_SLUG_ROOM_TEMPLATES  => array(
				'title'    => esc_html__( 'Room Templates', 'myvideoroom' ),
				'callback' => array( $admin_manager, 'create_templates_page' ),
			),

			self::PAGE_SLUG_REFERENCE       => array(
				'title'    => esc_html__( 'Shortcode Reference', 'myvideoroom' ),
				'callback' => array( $admin_manager, 'create_shortcode_reference_page' ),
			),

			self::PAGE_SLUG_PERMISSIONS     => array(
				'title'    => esc_html__( 'Room Permissions', 'myvideoroom' ),
				'callback' => array( $admin_manager, 'create_permissions_page' ),
			),

			self::PAGE_SLUG_MODULES         => array(
				'title'    => esc_html__( 'Modules', 'myvideoroom' ),
				'callback' => array( $admin_manager, 'create_modules_page' ),
			),

			self::PAGE_SLUG_ADVANCED        => array(
				'title'      => esc_html__( 'Advanced', 'myvideoroom' ),
				'title_icon' => 'admin-generic',
				'callback'   => array( $admin_manager, 'create_advanced_settings_page' ),
			),
		);

		\do_action(
			'myvideoroom_admin_menu',
			function ( string $slug, string $title, callable $callback, int $offset = -1, string $dashicon = null ) use ( &$navigation_items ) {
				$new_item = array(
					$slug => array(
						'title'    => $title,
						'callback' => $callback,
					),
				);

				if ( $dashicon ) {
					$new_item[ $slug ]['title_icon'] = $dashicon;
				}

				$navigation_items = array_merge(
					array_slice( $navigation_items, 0, $offset, true ),
					$new_item,
					array_slice( $navigation_items, $offset, null, true )
				);
			}
		);

		return $navigation_items;
	}
}
