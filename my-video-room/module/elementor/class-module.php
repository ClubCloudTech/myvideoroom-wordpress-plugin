<?php
/**
 * The entry point for the elementor plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Elementor;

/**
 * Class Module
 */
class Module {

	/**
	 * Module constructor.
	 */
	public function __construct() {
		\add_filter(
			'myvideoroom_sitevideo_edit_actions',
			function ( array $actions, int $post_id ) {
				array_unshift(
					$actions,
					array(
						'Edit in Elementor',
						get_site_url() . '/wp-admin/post.php?post=' . esc_textarea( $post_id ) . '&action=elementor',
						'fab fa-elementor',
					)
				);

				return $actions;
			},
			10,
			2
		);
	}
}
