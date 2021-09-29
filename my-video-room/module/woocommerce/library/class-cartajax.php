<?php
/**
 * Ajax Handling Function - switches inbound Ajax requests.
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/CartAjax.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Cart Ajax Handler.
 * Handles all elements of Add to Cart via Ajax in Shop View.
 */
class CartAjax {

	/**
	 * Handle Ajax Add to Cart Requests.
	 *
	 * @return void
	 */
	public function woocommerce_ajax_add_to_cart() {

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
		$variation_id      = absint( $_POST['variation_id'] );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			WC_AJAX::get_refreshed_fragments();
		} else {

			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			echo wp_send_json( $data );
		}

		wp_die();
	}
}
