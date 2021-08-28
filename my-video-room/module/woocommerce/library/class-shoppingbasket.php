<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShoppingBasket
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceVideo;

/**
 * Class Shopping Basket
 * Handles all elements of rendering WooCommerce Shopping Baskets and Broadcasts.
 */
class ShoppingBasket {

	/**
	 * Shopping Basket Controller
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status  Whether user is a host.
	 * @return Void
	 */
	public function render_basket( string $room_name, $host_status = null ) {

		// Initialise Function.



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

		return $render( $output_array, $room_name );

	}

	/**
	 * Delete Product from Cart
	 *
	 * @param  string $action_type - The type of Operation to confirm.
	 * @param string $room_name -  Name of Room.
	 * @param  string $auth_nonce - Authentication Nonce.
	 * @return string
	 */
	public function cart_confirmation( string $action_type, string $room_name, string $auth_nonce ):string {

		// Render Confirmation Page View.
		$render = require __DIR__ . '/../views/basket-confirmation.php';
		return $render( $action_type, $room_name, $auth_nonce );

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
	 * @param string $room_name -  Name of Room.
	 * @param  string $nonce - Nonce for operation (if confirmation used).
	 *
	 * @return string
	 */
	public function basket_nav_bar_button( string $button_type, string $button_label, string $room_name, string $nonce = null ):string {

		return '
		<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
		<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
		</div>
		';

	}

	/**
	 * Get the list of current Sync Items
	 *
	 * @param string $room_type     Category of Room if used.
	 *
	 * @return array
	 */
	public function get_rooms( string $room_type = null ): array {
		$available_rooms = Factory::get_instance( RoomMap::class )->get_all_post_ids_of_rooms( $room_type );

		return array_map(
			function ( $room_id ) {
				$room = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

				$room->url  = Factory::get_instance( RoomAdminLibrary::class )->get_room_url( $room->room_name );
				$room->type = Factory::get_instance( RoomAdminLibrary::class )->get_room_type( $room->room_name );

				return $room;
			},
			$available_rooms
		);
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
