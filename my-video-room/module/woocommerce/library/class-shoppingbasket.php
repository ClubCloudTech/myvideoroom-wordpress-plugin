<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShoppingBasket
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceRoomSync as WooCommerceRoomSyncEntity;
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

		// Register Basket in Room.
		$this->register_room_presence( $room_name );

		$output_array = array();
		// Loop over $cart items.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$basket_array                 = array();
				$basket_array['product_id']   = $product_id = $cart_item['product_id'];
				$product                      = wc_get_product( $product_id );
				$basket_array['quantity']     = $cart_item['quantity'];
				$basket_array['variation_id'] = $cart_item['variation_id'];
				$basket_array['name']         = $product->get_name();
				$basket_array['image']        = $product->get_image();
				$basket_array['price']        = WC()->cart->get_product_price( $product );
				$basket_array['subtotal']     = WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] );
				$basket_array['link']         = $product->get_permalink( $cart_item );

				array_push( $output_array, $basket_array );
		}
		// Render View.
		$render = require __DIR__ . '/../views/table-output.php';

		return $render( $output_array, $room_name );
	}

	/**
	 * Render Confirmation Pages
	 *
	 * @param  string $action_type - The type of Operation to confirm.
	 * @param string $room_name -  Name of Room.
	 * @param  string $auth_nonce - Authentication Nonce.
	 * @param string $message - Message to Display.
	 * @param string $confirmation_button_approved - Button to Display for Approved.
	 * @return string
	 */
	public function cart_confirmation( string $action_type, string $room_name, string $auth_nonce, string $message, string $confirmation_button_approved ):string {

		// Render Confirmation Page View.
		$render = require __DIR__ . '/../views/basket-confirmation.php';
		return $render( $action_type, $room_name, $auth_nonce, $message, $confirmation_button_approved );

	}

	/**
	 * Register Room Presence
	 *
	 * @param string $room_name -  Name of Room.
	 * @return void
	 */
	public function register_room_presence( string $room_name ):void {
		$cart_session = session_id();
		$timestamp    = \current_time( 'timestamp' );
		$register     = new WooCommerceRoomSyncEntity(
			$cart_session,
			$room_name,
			$timestamp,
			null
		);

		Factory::get_instance( WooCommerceRoomSyncDAO::class )->create( $register );

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
	 * Render the Basket Nav Bar Button
	 *
	 * @param string $button_type - Feedback for Ajax Post.
	 * @param string $button_label - Label for Button.
	 * @param string $room_name -  Name of Room.
	 * @param string $nonce - Nonce for operation (if confirmation used).
	 * @param string $quantity - Product Quantity.
	 * @param string $product_id - Product ID.
	 * @param string $variation_id - Variation ID.
	 *
	 * @return string
	 */
	public function basket_product_share_button( string $button_type, string $button_label, string $room_name, string $nonce = null, string $quantity, string $product_id, string $variation_id ):string {

		return '
		<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
		<a href="" data-input-type="' . $button_type . '" 
		 data-auth-nonce="' . $nonce . '"
		 data-room-name="' . $room_name . '"
		 data-quantity="' . $quantity . '"
		 data-variation-id="' . $variation_id . '"
		 data-product-id="' . $product_id . '"
		 class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
		</div>
		';

	}

	/**
	 * Broadcast Single Product
	 *
	 * @param string $product_id - Product ID.
	 * @param string $room_name -  Name of Room.
	 * @param string $quantity - Product Quantity.
	 * @param string $variation_id - Variation ID.
	 */
	public function broadcast_single_product( string $product_id, string $room_name, string $quantity, string $variation_id ) {
		$participants = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_room_participants( $room_name );
		$timestamp    = \current_time( 'timestamp' );
		$source_cart  = session_id();
		foreach ( $participants as $room ) {

			// Skip Cart Items Originated by this User.
			if ( $source_cart === $room->cart_id ) {
				continue;
			} else {

				$register = new WooCommerceVideo(
					$room->cart_id,
					$source_cart,
					$room_name,
					null,
					$product_id,
					$quantity,
					$variation_id,
					true,
					$timestamp,
					null
				);
			}

			Factory::get_instance( WooCommerceVideoDAO::class )->create( $register );
		}

		return null;
	}

	/**
	 * Render Queue Table.
	 *
	 * @param string $room_name -  Name of Room.
	 *
	 * @return string
	 */
	public function render_sync_queue_table( string $room_name ): string {

		$cart_id         = session_id();
		$available_queue = Factory::get_instance( WooCommerceVideoDAO::class )->get_all_queue_records( $cart_id, $room_name );
		$output_array    = array();

		foreach ( $available_queue as $record_id ) {
			$cart_item                    = Factory::get_instance( WooCommerceVideoDAO::class )->get_record_by_record_id( $record_id );
			$basket_array                 = array();
			$product_id                   = $cart_item->get_product_id();
			$basket_array['record_id']    = $record_id;
			$basket_array['product_id']   = $product_id;
			$product                      = wc_get_product( $product_id );
			$basket_array['quantity']     = $cart_item->get_quantity();
			$basket_array['variation_id'] = $cart_item->get_variation_id();
			$basket_array['name']         = $product->get_name();
			$basket_array['image']        = $product->get_image();
			$basket_array['price']        = WC()->cart->get_product_price( $product );
			$basket_array['subtotal']     = WC()->cart->get_product_subtotal( $product, $cart_item->get_quantity() );
			$basket_array['link']         = $product->get_permalink( $cart_item );

			array_push( $output_array, $basket_array );

		}

		// Render View.
		$render = require __DIR__ . '/../views/sync-table-output.php';

		return $render( $output_array, $room_name );
	}

}
