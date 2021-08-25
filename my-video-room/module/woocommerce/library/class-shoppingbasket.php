<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShoppingBasket
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

/**
 * Class Shopping Basket
 * Handles all elements of rendering WooCommerce Shopping Baskets and Broadcasts.
 */
class ShoppingBasket {

	/**
	 * Shopping Basket Controller
	 *
	 * @param bool $host_status  Whether user is a host.
	 * @return Void
	 */
	public function render_basket( bool $host_status ) {
		
		$output_array = array();
		// Loop over $cart items.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$basket_array = array();

				$basket_array['product_id'] = $product_id = $cart_item['product_id'];
				$product                    = wc_get_product( $product_id );
				$basket_array['quantity']   = $cart_item['quantity'];
				$basket_array['name']       = $product->get_name();
				$basket_array['image']      = $product->get_image();
				$basket_array['price']      = WC()->cart->get_product_price( $product );
				$basket_array['subtotal']   = WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] );
				$basket_array['link']       = $product->get_permalink( $cart_item );

				array_push( $output_array, $basket_array );
		}
		// Render View.
		$render = require __DIR__ . '/../views/table-output.php';

		return $render( $output_array );

	}

}
