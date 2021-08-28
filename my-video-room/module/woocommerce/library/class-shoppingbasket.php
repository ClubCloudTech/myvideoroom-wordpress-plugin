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
	public function render_basket( $host_status = null ) {

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

	/**
	 * Delete Product from Cart
	 *
	 * @param  string $action_type - The type of Operation to confirm.
	 * @param  string $auth_nonce - Authentication Nonce.
	 * @return string
	 */
	public function cart_confirmation( string $action_type, string $auth_nonce ):string {

		// Render Confirmation Page View.
		$render = require __DIR__ . '/../views/basket-confirmation.php';
		return $render( $action_type, $auth_nonce );

	}



	/**
	 * Delete Product from Cart
	 *
	 * @param  int $product_id - Product ID.
	 * @return void
	 */
		public function delete_product_from_cart( int $product_id ):void {

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] === $product_id ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}

	}

	/**
	 * Render the Basket Nav Bar Button
	 *
	 * @param  string $button_type - Feedback for Ajax Post.
	 * @param  string $button_label - Label for Button.
	 * @param  string $nonce - Nonce for operation (if confirmation used).
	 *
	 * @return string
	 */
	public function basket_nav_bar_button( string $button_type, string $button_label, string $nonce = null ):string {

		return '
		<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
		<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
		</div>
		';

	}







	// @TODO - remove function.

public function test_render() {
	?>
<div>
	<div class="widget_shopping_cart_content">
		<?php woocommerce_mini_cart(); ?>
	</div>
</div>
	<?php
	return '';
}

}
