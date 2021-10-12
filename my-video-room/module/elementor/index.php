<?php
/**
 * BuddyPress Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Elementor;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\Elementor\Module as Elementor;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			'elementor',
			'Elementor Integration Pack',
			array(
				sprintf(
				/* translators: %s is a link to the Elementor website builder plugin */
					\__(
						'Adds functionality to integration MyVideoRoom and %s. Allows shortcode for editing generated pages in elementor',
						'myvideoroom'
					),
					'Elementor'
				),
			),
			fn() => Factory::get_instance( Elementor::class )->init()
		)->add_compatibility_hook(
			fn() => Factory::get_instance( Elementor::class )->is_elementor_active()
		);
	}
);
