<?php
/**
 * WCFM Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/WCFM
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			'woocommerce',
			\esc_html__( 'WooCommerce Integration', 'myvideoroom' ),
			array(
				\esc_html__(
					'Integrates MyVideoRoom and WooCommerce stores giving a dedicated video room to host video calls with customers and a virtual shopfront. A video storefront Shortcode is added as well as extra tabs for meetings where a merchant can use to deliver consultations, handle drop-in visits, and host their own store level meetings securely.',
					'myvideoroom'
				),
				\esc_html__(
					'Merchants control their store video room settings, permissions, and reception look and feel creating a professional video consultation experience. You can also use this module coupled with the WooCommerce Bookings module, allowing for a full booking, and drop in video enabled e-commerce experience, all from your Store.',
					'myvideoroom'
				),
			),
			fn() => Factory::get_instance( WooCommerce::class )->init()
		)
		->add_activation_hook(
			fn() => Factory::get_instance( WooCommerce::class )->activate_module()
		)
		->add_admin_page_hook(
			fn() => Factory::get_instance( WooCommerce::class )->render_woocommerce_admin_page()
		)
		->add_deactivation_hook(
			fn() => Factory::get_instance( WooCommerce::class )->de_activate_module()
		);
	}
);
