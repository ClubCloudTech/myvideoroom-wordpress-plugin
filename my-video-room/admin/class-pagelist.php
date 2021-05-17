<?php
/**
 * Manages the list of pages in the MyVideoRoom admin section.
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Admin;

use MyVideoRoomPlugin\Admin;

/**
 * Class Navigation
 */
class PageList {

	const PAGE_SLUG_GETTING_STARTED = 'my-video-room';
	const PAGE_SLUG_ROOM_TEMPLATES  = 'my-video-room-templates';
	const PAGE_SLUG_REFERENCE       = 'my-video-room-shortcode-reference';
	const PAGE_SLUG_PERMISSIONS     = 'my-video-room-permissions';
	const PAGE_SLUG_MODULES         = 'my-video-room-modules';
	const PAGE_SLUG_CUSTOM          = 'my-video-room-custom';

	/**
	 * Get the navigation items
	 *
	 * @param Admin $admin_manager The admin manager, required to activate the callbacks.
	 *
	 * @return \MyVideoRoomPlugin\Admin\Page[]
	 */
	public function get_page_list( Admin $admin_manager ): array {
		$navigation_items = array(
			self::PAGE_SLUG_GETTING_STARTED => new Page(
				self::PAGE_SLUG_GETTING_STARTED,
				\esc_html__( 'Getting Started', 'myvideoroom' ),
				array( $admin_manager, 'create_getting_started_page' )
			),

			self::PAGE_SLUG_ROOM_TEMPLATES  => new Page(
				self::PAGE_SLUG_ROOM_TEMPLATES,
				\esc_html__( 'Room Templates', 'myvideoroom' ),
				array( $admin_manager, 'create_templates_page' )
			),

			self::PAGE_SLUG_REFERENCE       => new Page(
				self::PAGE_SLUG_REFERENCE,
				\esc_html__( 'Shortcode Reference', 'myvideoroom' ),
				array( $admin_manager, 'create_shortcode_reference_page' )
			),

			self::PAGE_SLUG_PERMISSIONS     => new Page(
				self::PAGE_SLUG_PERMISSIONS,
				\esc_html__( 'Room Permissions', 'myvideoroom' ),
				array( $admin_manager, 'create_permissions_page' )
			),

			self::PAGE_SLUG_MODULES         => new Page(
				self::PAGE_SLUG_MODULES,
				\esc_html__( 'Modules', 'myvideoroom' ),
				array( $admin_manager, 'create_modules_page' )
			),

			self::PAGE_SLUG_CUSTOM          => new Page(
				self::PAGE_SLUG_CUSTOM,
				\esc_html__( 'Advanced', 'myvideoroom' ),
				array( $admin_manager, 'create_advanced_settings_page' ),
				'admin-generic',
			),
		);

		\do_action(
			'myvideoroom_admin_menu',
			function ( Page $navigation_item, int $offset = - 1 ) use ( &$navigation_items ) {
				$navigation_items = \array_merge(
					\array_slice( $navigation_items, 0, $offset, true ),
					array( $navigation_item->get_slug() => $navigation_item ),
					\array_slice( $navigation_items, $offset, null, true )
				);
			}
		);

		return $navigation_items;
	}
}
