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
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

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
	 * @param bool   $ajax_host  Whether user is a host from Ajax call.
	 * @return Void
	 */
	public function render_basket( string $room_name, $host_status = null, bool $ajax_host = null ) {
		if ( ! $room_name ) {
			return null;
		}
		// Register this user in Room Presence Table.
		$this->register_room_presence( $room_name, boolval( $host_status ) );

		echo var_dump( Factory::get_instance( WooCommerceVideoDAO::class )->get_current_basket_sync_queue_records( $room_name ) );

		// Add Queue Length and Cart Hash for Sync flag.
		$current_cartnum   = strval( Factory::get_instance( self::class )->check_queue_length( $room_name ) );
		$current_cart_data = WC()->cart->get_cart_hash();

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

		return $render( $output_array, $room_name, $current_cartnum, $current_cart_data );
	}

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
	public function cart_confirmation( string $action_type, string $room_name, string $auth_nonce, string $message, string $confirmation_button_approved, string $data_for_confirmation = null ):string {

		// Render Confirmation Page View.
		$render = require __DIR__ . '/../views/basket-confirmation.php';
		return $render( $action_type, $room_name, $auth_nonce, $message, $confirmation_button_approved, $data_for_confirmation );

	}

	/**
	 * Register Room Presence
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status - If User is Host.
	 * @return void
	 */
	public function register_room_presence( string $room_name, bool $host_status ):void {
		// Setup.
		$cart_session = Factory::get_instance( HostManagement::class )->get_user_session();
		$timestamp    = \current_time( 'timestamp' );

		$am_i_master = Factory::get_instance( HostManagement::class )->initialise_master_status( $room_name, $host_status );

		$current_record = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( $cart_session, $room_name );

		if ( $current_record ) {
			$current_record->set_timestamp( $timestamp );
			$current_record->set_room_host( $host_status );

		} else {
			$current_record = new WooCommerceRoomSyncEntity(
				$cart_session,
				$room_name,
				$timestamp,
				null,
				$host_status,
				null,
				null,
				$am_i_master,
				null
			);
		}

		Factory::get_instance( WooCommerceRoomSyncDAO::class )->create( $current_record );
	}

	/**
	 * Delete Product from Cart
	 *
	 * @param  string $product_id - Product ID.
	 * @return bool
	 */
	public function delete_product_from_cart( string $product_id ):bool {
		$product_id    = intval( $product_id );
		$success_state = false;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] === $product_id ) {
				$success_state = WC()->cart->remove_cart_item( $cart_item_key );
				if ( $success_state ) {
					$success_state = true;
				}
			}
		}
		return $success_state;
	}

	/**
	 * Add Queued Product to Cart
	 *
	 * @param  string $product_id - Product ID.
	 * @param  string $quantity - How Many Items to Add.
	 * @param  string $variation_id - Product Variation ID.
	 * @param  string $record_id - the record ID to delete from Sync Table.
	 * 
	 * @return bool
	 */
	public function add_queued_product_to_cart( string $product_id, string $quantity, string $variation_id, string $record_id ):bool {

		$success_state = wc()->cart->add_to_cart( $product_id, $quantity, $variation_id );
		Factory::get_instance( WooCommerceVideoDAO::class )->delete_record( $record_id );

		if ( $success_state ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Add All Queued Products to Cart
	 * Gets all Cart Items in Shared Queue and Accepts them All.
	 *
	 * @param  string $room_name - Room we are in.
	 *
	 * @return bool
	 */
	public function add_all_queued_products_to_cart( string $room_name ):bool {
		$success_state = false;

		$queue_objects = $this->render_sync_queue_table( $room_name, true );

		foreach ( $queue_objects as $item ) {

			$success = $this->add_queued_product_to_cart( $item['product_id'], $item['quantity'], $item['variation_id'], strval( $item['record_id'] ) );
			if ( $success ) {
				$success_state = true;
			}
		}

		return $success_state;
	}

	/**
	 * Delete all Queued Items
	 * Gets all Cart Items in Shared Queue and Accepts them All.
	 *
	 * @param  string $room_name - Room we are in.
	 *
	 * @return void
	 */
	public function delete_all_queued_products_from_cart( string $room_name ):void {

		$queue_objects = $this->render_sync_queue_table( $room_name, true );

		foreach ( $queue_objects as $item ) {

			Factory::get_instance( WooCommerceVideoDAO::class )->delete_record( $item['record_id'] );

		}

	}

	/**
	 * Render the Basket Nav Bar Button
	 *
	 * @param  string $button_type - Feedback for Ajax Post.
	 * @param  string $button_label - Label for Button.
	 * @param string $room_name -  Name of Room.
	 * @param  string $nonce - Nonce for operation (if confirmation used).
	 * @param  string $product_or_id - Adds additional Data to Nonce for more security (optional).
	 *
	 * @return string
	 */
	public function basket_nav_bar_button( string $button_type, string $button_label, string $room_name, string $nonce = null, string $product_or_id = null ):string {

		$id_text = null;
		if ( $product_or_id ){
			$id_text = ' data-record-id="' . $product_or_id . '" ';
		}

		return '
		<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
		<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"' . $id_text . ' class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
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
		$source_cart  = Factory::get_instance( HostManagement::class )->get_user_session();
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
	 * Check for User Changes
	 *
	 * @param string $last_queue_ammount - The Number last recorded for Inbound Queue.
	 * @param string $last_carthash - The last stored cart hash.
	 * @param string $room_name - Room Name to Check.
	 * @param string $sync_type - the Mode the User has set of Sync.
	 */
	public function check_for_user_changes( string $last_queue_ammount, string $last_carthash, string $room_name, string $sync_type = null ) {

		// Initialise.
		$cart_id             = Factory::get_instance( HostManagement::class )->get_user_session();
		$count_current_queue = count( Factory::get_instance( WooCommerceVideoDAO::class )->get_queue_records( $cart_id, $room_name ) );
		$current_carthash    = WC()->cart->get_cart_hash();
		$change_heartbeat    = $this->user_notification_heartbeat( $room_name, $cart_id );

		// Check Inbound Queue for Changes.
		if ( intval( $last_queue_ammount ) !== $count_current_queue ) {
			$queue_changed = true;
		}

		// Check WooCommerce Cart for Changes.
		if ( $current_carthash !== $last_carthash ) {
			$woocart_changed = true;
		}

		if ( $woocart_changed || $queue_changed || $change_heartbeat ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Current Cart Number
	 *
	 * @param string $room_name - Room Name to Check.
	 */
	public function check_queue_length( string $room_name ) {
		$cart_id             = Factory::get_instance( HostManagement::class )->get_user_session();
		$count_current_queue = count( Factory::get_instance( WooCommerceVideoDAO::class )->get_queue_records( $cart_id, $room_name ) );

		return $count_current_queue;
	}

	/**
	 * Checks Global and User/Room record for recent changes and returns Change flag.
	 *
	 * @param string $room_name - Room Name to Check.
	 * @param string $user_hash - User Hash to check (leave blank for current user).
	 */
	public function user_notification_heartbeat( string $room_name, string $user_hash = null ): bool {
		if ( ! $user_hash ) {
			$user_hash = Factory::get_instance( HostManagement::class )->get_user_session();
		}

		$user_object = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( $user_hash, $room_name );
		if ( ! $user_object ) {
			return false;
		}
		$user_timestamp   = $user_object->get_last_notification() + WooCommerce::SETTING_HEARTBEAT_THRESHOLD;
		$global_object    = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );
		$global_timestamp = $global_object->get_last_notification() + WooCommerce::SETTING_HEARTBEAT_THRESHOLD;

		if ( ! $user_timestamp && ! $global_timestamp ) {
			return false;
		}
		$timestamp = \current_time( 'timestamp' );

		if ( $timestamp < $user_timestamp || $timestamp < $global_timestamp ) {
			return true;
		}

		return false;
	}

	/**
	 * Render Queue Table.
	 * Prepare Queue Table and Render the view, or return just Queue object to other functions.
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $object_only -  Flag to return only the object and not render the table.
	 *
	 * @return string|Array depending on return flag
	 */
	public function render_sync_queue_table( string $room_name, bool $object_only = null ) {
		if ( ! $room_name ){
			return null;
		}

		$cart_id         = Factory::get_instance( HostManagement::class )->get_user_session();
		$available_queue = Factory::get_instance( WooCommerceVideoDAO::class )->get_queue_records( $cart_id, $room_name );
		$output_array    = array();

		foreach ( $available_queue as $record_id ) {
			$cart_item = Factory::get_instance( WooCommerceVideoDAO::class )->get_record_by_record_id( $record_id );
			if ( $cart_item ) {

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
		}
			// Return Object.
		if ( $object_only ) {
			return $output_array;
		} else {
			// Render View.
			$render = require __DIR__ . '/../views/sync-table-output.php';
			return $render( $output_array, $room_name );
		}
	}

}
