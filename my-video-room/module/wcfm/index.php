<?php
/**
 * WCFM Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/WCFM
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Factory::get_instance( Module::class )
			->register(
				'wcfm',
				__( 'WCFM', 'myvideoroom' ),
				array(
					__(
						'Integrates MyVideoRoom and WCFM multi-vendor marketplace giving each merchant a dedicated video room to host video calls with their customers in. A video store tab is created in WCFM storefronts that automatically adds a video storefront for a merchant can use to deliver consultations, handle drop-in visits, and host their own store level meetings securely.',
						'myvideoroom'
					),
					__(
						'Merchants control their store video room settings, permissions, and reception look and feel creating a professional video consultation experience. Store staff roles are also integrated into the plugin, with staff members automatically getting hosting permissions of store rooms. You can also use this module coupled with the WooCommerce Bookings module, allowing for a full booking, and drop in video enabled e-commerce experience, all from your WCFM Store.',
						'myvideoroom'
					),
				),
			);
	}
);
