<?php
/**
 * WooCommerceBookings Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/WooCommerceBookings
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Factory::get_instance( Module::class )
			->register(
				'woocommerce-bookings',
				__( 'WooCommerceBookings', 'myvideoroom' ),
				array(
					__(
						'Integrates WoocommerceBookings into MyVideoRoom which will automatically create booking rooms for each booking and can only be accessed during the time of the booking. The booking room becomes open 15 minutes ahead of time, can only be accessed by the  merchant and purchaser. Purchases will join the reception area waiting for the merchant to admit them. Merchants can deliver any product as a booking to take payment, and can see their upcoming video bookings in the booking center. Users can see their upcoming video bookings in their My Account pages in WooCommerce and go straight into their video bookings. Email templates send a booking reference number to a video room on product purchase. A booking center page is created automatically to ask a guest for their booking reference and connect them automatically to the host store booking.',
						'myvideoroom'
					),
					__(
						'Merchants can also set their booking room settings, permissions, receptions look and feel, creating a professional video consultation experience.',
						'myvideoroom'
					),
				),
			);
	}
);
