<?php
/**
 * BuddyPress Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Elementor;

use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\Elementor\Module as Elementor;

\add_action(
	'myvideoroom_init',
	function () {
		Module::register(
			'elementor',
			'Elementor',
			array(
				sprintf(
				/* translators: %s is a link to the Elementor website builder plugin */
					\__(
						'Adds functionality to integration MyVideoRoom and %s. Allows shortcode for editing generated pages in elementor',
						'myvideoroom'
					),
					'<a href="https://elementor.com/">Elementor</a>'
				),
			),
			fn() => new Elementor()
		)->add_compatibility_hook(
			function () {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				return \is_plugin_active( 'elementor/elementor.php' );
			}
		);
	}
);
