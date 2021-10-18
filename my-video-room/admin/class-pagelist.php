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

	const PAGE_SLUG_DEFAULT = 'myvideoroom';

	const PAGE_SLUG_GETTING_STARTED = self::PAGE_SLUG_DEFAULT;
	const PAGE_SLUG_ROOM_TEMPLATES  = self::PAGE_SLUG_DEFAULT . '-room-builder';
	const PAGE_SLUG_REFERENCE       = self::PAGE_SLUG_DEFAULT . '-shortcode-reference';
	const PAGE_SLUG_PERMISSIONS     = self::PAGE_SLUG_DEFAULT . '-permissions';
	const PAGE_SLUG_MODULES         = self::PAGE_SLUG_DEFAULT . '-modules';
	const PAGE_SLUG_CUSTOM          = self::PAGE_SLUG_DEFAULT . '-custom';

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

			self::PAGE_SLUG_MODULES         => new Page(
				self::PAGE_SLUG_MODULES,
				\esc_html__( 'Modules', 'myvideoroom' ),
				array( $admin_manager, 'create_modules_page' )
			),

			self::PAGE_SLUG_PERMISSIONS     => new Page(
				self::PAGE_SLUG_PERMISSIONS,
				\esc_html__( 'Default Settings', 'myvideoroom' ),
				array( $admin_manager, 'create_permissions_page' ),
				'admin-generic',
			),

			self::PAGE_SLUG_REFERENCE       => new Page(
				self::PAGE_SLUG_REFERENCE,
				\esc_html__( 'Reference', 'myvideoroom' ),
				array( $admin_manager, 'create_shortcode_reference_page' ),
				'editor-help'
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
