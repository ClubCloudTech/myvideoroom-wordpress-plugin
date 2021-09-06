<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShopView.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceRoomSync as WooCommerceRoomSyncEntity;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceVideo;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class ShopView Basket
 * Handles all elements of rendering WooCommerce Category Related Archives
 */
class ShopView {

	/**
	 * Render Confirmation Pages
	 *
	 * @param  string $action_type - The type of Operation to confirm.
	 * @param string $room_name -  Name of Room.
	 * @param  string $auth_nonce - Authentication Nonce.
	 * @param string $message - Message to Display.
	 * @param string $confirmation_button_approved - Button to Display for Approved.
	 * @param string $data_for_confirmation - Extra parameter like record id, product id etc for strengthening nonce.
	 * @return string
	 */
	public function show_shop( string $room_name = null ): void {

		return null;
		$args     = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => '12',
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => 26,
					'operator' => 'IN',
				),
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => 'exclude-from-catalog', // Possibly 'exclude-from-search' too
					'operator' => 'NOT IN',
				),
			),
		);
		$products = new WP_Query( $args );
		var_dump( $products );

	}





}
