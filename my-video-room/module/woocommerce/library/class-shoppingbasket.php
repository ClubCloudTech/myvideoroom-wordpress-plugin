<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShoppingBasket
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Entity\RoomSync as RoomSyncEntity;
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
	 * @param int    $post_id      Rooms ID.
	 *
	 * @return Void
	 */
	public function render_basket( string $room_name, $host_status = null, int $post_id = null ) {

		if ( ! $room_name ) {
			return null;
		}
		// Register this user in Room Presence Table.
		$this->register_room_presence( $room_name, boolval( $host_status ) );

		// User and Broadcast Heartbeats.
		$this->basket_sync_heartbeat( $room_name );
		$this->broadcast_basket( $room_name );

		// Add Queue Length and Cart Hash for Sync flag.
		$current_cartnum   = strval( Factory::get_instance( self::class )->check_queue_length( $room_name ) );
		$current_cart_data = WC()->cart->get_cart_hash();
		$cart_objects      = $this->get_cart_objects( $room_name );
		$download_active   = Factory::get_instance( HostManagement::class )->am_i_downloading( $room_name );
		$master_status     = Factory::get_instance( HostManagement::class )->am_i_master( $room_name );
		$broadcast_status  = Factory::get_instance( HostManagement::class )->am_i_broadcasting( $room_name );

		// Render View.
		$render = require __DIR__ . '/../views/table-output.php';

		return $render( $cart_objects, $room_name, $current_cartnum, $current_cart_data, $download_active, $master_status, $broadcast_status );
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
	 * Render Notification Pages
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $client_change_state -  If the Client has a Change (basket Woocomm).
	 * @param bool   $store_change_state -  If the Store has changed.
	 * @param bool   $notification_queue_change_state -  If a product share queue item has changed.
	 *
	 * @return string
	 */
	public function render_notification_tab( string $room_name, bool $client_change_state = null, bool $store_change_state = null, bool $notification_queue_change_state = null ): ?string {

		if ( $client_change_state ) {
			$title               = esc_html__( 'A new Product has been automatically synced into your basket from another source', 'myvideoroom' );
			$target_focus_id     = 'mvr-shopping-basket';
			$message             = \esc_html__( ' New Basket Update ', 'myvideoroom' );
			$iconclass           = 'dashicons-cart';
			$client_notification = Factory::get_instance( NotificationHelpers::class )->render_client_change_notification( $title, $target_focus_id, $message, $iconclass );

		}
		if ( $store_change_state ) {
			$title                = esc_html__( 'The Room store has been updated, check it out', 'myvideoroom' );
			$target_focus_id      = 'mvr-shop';
			$message              = \esc_html__( ' New Update to Room Store ', 'myvideoroom' );
			$iconclass            = 'dashicons-store';
			$client_notification .= Factory::get_instance( NotificationHelpers::class )->render_client_change_notification( $title, $target_focus_id, $message, $iconclass );

		}
		if ( $notification_queue_change_state ) {
			$title                = esc_html__( 'A Product has been shared with you in the room, check it out', 'myvideoroom' );
			$target_focus_id      = 'mvr-shopping-basket';
			$message              = \esc_html__( ' Product Shared With You ', 'myvideoroom' );
			$iconclass            = 'dashicons-cart dashicons-plus';
			$client_notification .= Factory::get_instance( NotificationHelpers::class )->render_client_change_notification( $title, $target_focus_id, $message, $iconclass );

		}
		$popup_notification = apply_filters( 'myvideoroom_notification_popup', '', $room_name );

			// Render Confirmation Page View.
			$render = require __DIR__ . '/../views/notification-bar.php';
			return $render( $room_name, $client_notification, $popup_notification );
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
		$cart_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$timestamp    = \current_time( 'timestamp' );

		$current_record = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $cart_session, $room_name, $host_status );

		if ( $current_record ) {
			$current_record->set_timestamp( $timestamp );
			$current_record->set_room_host( $host_status );

		} else {
			$current_record = new RoomSyncEntity(
				$cart_session,
				$room_name,
				$timestamp,
				$timestamp,
				$host_status,
				null,
				null,
				$host_status,
				null
			);
			// Set Last Notification Timestamp for new room.
			Factory::get_instance( HostManagement::class )->notify_user( $room_name );
		}
		Factory::get_instance( RoomSyncDAO::class )->create( $current_record );

		// Check and Clean Master Status.
		Factory::get_instance( HostManagement::class )->initialise_master_status( $room_name, $host_status );
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
	 * @param  bool   $basket_sync_flag - Skips Updating Record ID (deleting from queue) if its for basket sync.
	 *
	 * @return bool
	 */
	public function add_queued_product_to_cart( string $product_id, string $quantity, string $variation_id, string $record_id = null, bool $basket_sync_flag = null ):bool {

		$success_state = wc()->cart->add_to_cart( $product_id, $quantity, $variation_id );

		if ( ! $basket_sync_flag ) {
			Factory::get_instance( WooCommerceVideoDAO::class )->delete_record( $record_id );
		}

		if ( $success_state ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Clear Cart
	 * Clears all Items in WooCommerce Cart.
	 *
	 * @return bool
	 */
	public function clear_my_cart():bool {
		$result = wc()->cart->empty_cart();
		return true;
	}

	/**
	 * Broadcast My Basket.
	 * Uploads all Basket Contents to Database
	 *
	 * @param string $room_name - The Room Name to Clear Sync on.
	 *
	 * @return bool
	 */
	public function broadcast_basket( string $room_name ):bool {

		$is_sync_available = Factory::get_instance( HostManagement::class )->is_sync_available( $room_name );
		$am_i_master       = Factory::get_instance( HostManagement::class )->am_i_master( $room_name );
		$am_i_broadcasting = Factory::get_instance( HostManagement::class )->am_i_broadcasting( $room_name );

		if ( ! $is_sync_available || ! $am_i_master || ! $am_i_broadcasting ) {
			return false;
		}

		// Clear Current Basket.
		Factory::get_instance( WooCommerceVideoDAO::class )->clear_broadcast_basket_queue( $room_name );

		// Get Current Basket Objects.
		$basket_array = $this->get_cart_objects( $room_name );

		if ( count( $basket_array ) < 1 ) {
			return false;
		}

		$cart_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$timestamp    = \current_time( 'timestamp' );

		foreach ( $basket_array as $item ) {
				$register = new WooCommerceVideo(
					WooCommerce::SETTING_BASKET_REQUEST_ON,
					$cart_session,
					$room_name,
					null,
					strval( $item['product_id'] ),
					strval( $item['quantity'] ),
					strval( $item['variation_id'] ),
					false,
					$timestamp,
					null
				);

			Factory::get_instance( WooCommerceVideoDAO::class )->create( $register );
		}
		return false;

	}

	/**
	 * Get Cart Objects.
	 * Gets an array of all Shopping Cart Objects for Usage in Sync Engines and Tables.
	 *
	 * @param string $room_name - The Room Name to render (optional).
	 * @return array
	 */
	public function get_cart_objects( string $room_name = null ): ?array {

		$output_array = array();
		// Loop over $cart items.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$basket_array                 = array();
				$product_id                   = $cart_item['product_id'];
				$basket_array['product_id']   = $product_id;
				$product                      = wc_get_product( $product_id );
				$basket_array['quantity']     = $cart_item['quantity'];
				$basket_array['variation_id'] = $cart_item['variation_id'];
				$basket_array['name']         = $product->get_name();
				$basket_array['image']        = $product->get_image();
				$basket_array['price']        = WC()->cart->get_product_price( $product );
				$basket_array['subtotal']     = WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] );
				$basket_array['link']         = $product->get_permalink( $cart_item );
			if ( $room_name ) {
				$basket_array['am_i_host']         = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				$basket_array['am_i_broadcasting'] = Factory::get_instance( HostManagement::class )->am_i_broadcasting( $room_name );
				$basket_array['am_i_downloading']  = Factory::get_instance( HostManagement::class )->am_i_downloading( $room_name );
			}

				array_push( $output_array, $basket_array );
		}
		return $output_array;
	}

	/**
	 * Refreshes Basket for User if Sync Enabled
	 * Gets all Cart Items in Shared Queue and Accepts them All.
	 *
	 * @param  string $room_name - Room we are in.
	 *
	 * @return bool
	 */
	public function basket_sync_heartbeat( string $room_name ):bool {

		$is_sync_available = Factory::get_instance( HostManagement::class )->is_sync_available( $room_name );
		$am_i_downloading  = Factory::get_instance( HostManagement::class )->am_i_downloading( $room_name );

		if ( ! $is_sync_available || ! $am_i_downloading ) {
			return false;
		}

		$this->clear_my_cart();
		$success_state = false;

		$queue_objects = Factory::get_instance( WooCommerceVideoDAO::class )->get_current_basket_sync_queue_records( $room_name );

		foreach ( $queue_objects as $item ) {

			$product_id   = $item->get_product_id();
			$quantity     = $item->get_quantity();
			$variation_id = $item->get_variation_id();

			$success = $this->add_queued_product_to_cart( $product_id, $quantity, $variation_id, null );
			if ( $success ) {
				$success_state = true;
			}
		}

		return $success_state;
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
	 * @param  string $style - Add a class for the button (optional).
	 * @param  string $target_id - Adds a class to the button to javascript take an action on.
	 * @param  string $href_class - Adds a class to the button to javascript take an action on.
	 *
	 * @return string
	 */
	public function basket_nav_bar_button( string $button_type, string $button_label, string $room_name, string $nonce = null, string $product_or_id = null, string $style = null, string $target_id = null, string $href_class = null ): string {

		$id_text = null;
		if ( $product_or_id ) {
			$id_text = ' data-record-id="' . $product_or_id . '" ';
		}

		if ( ! $style ) {
			$style = 'mvr-main-button-enabled';
		}

		return '
		<button  class=" ' . $style . ' myvideoroom-woocommerce-basket-ajax" data-target="' . $target_id . '">
		<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"' . $id_text . ' class="myvideoroom-woocommerce-basket-ajax ' . $href_class . '">' . $button_label . '</a>
		</button>
		';
	}


	/**
	 * Render the Product Share Nav Bar Button
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
		<button class="mvr-main-button-enabled myvideoroom-woocommerce-basket-ajax">
		<a href="" data-input-type="' . $button_type . '" 
		 data-auth-nonce="' . $nonce . '"
		 data-room-name="' . $room_name . '"
		 data-quantity="' . $quantity . '"
		 data-variation-id="' . $variation_id . '"
		 data-product-id="' . $product_id . '"
		 class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
		</button>
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
		$participants = Factory::get_instance( RoomSyncDAO::class )->get_room_participants( $room_name );
		$timestamp    = \current_time( 'timestamp' );
		$source_cart  = Factory::get_instance( RoomAdmin::class )->get_user_session();
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
	 * @param string $last_carthash - The last stored cart hash.
	 * @param string $room_name - Room Name to Check.
	 */
	public function check_for_user_cart_changes( string $last_carthash, string $room_name ) {

		// Initialise.
		$current_carthash = WC()->cart->get_cart_hash();

		// Check WooCommerce Cart for Changes.
		if ( $current_carthash !== $last_carthash ) {
			$woocart_changed = true;

			// Update Cart if User is Broadcasting Basket.
			$am_i_broadcasting = Factory::get_instance( HostManagement::class )->am_i_broadcasting( $room_name );
			if ( $am_i_broadcasting ) {
				Factory::get_instance( HostManagement::class )->notify_user( $room_name );
				$this->broadcast_basket( $room_name );
			}
		}

		if ( $woocart_changed ) {
			return true;
		}

		return false;
	}

	/**
	 * Check for Notification Queue Product Sharing Changes
	 *
	 * @param string $last_queue_ammount - The Number last recorded for Inbound Queue.
	 * @param string $room_name - Room Name to Check.
	 */
	public function check_for_product_queue_changes( string $last_queue_ammount, string $room_name ) {

		// Initialise.
		$cart_id             = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$count_current_queue = count( Factory::get_instance( WooCommerceVideoDAO::class )->get_queue_records( $cart_id, $room_name ) );

		// Check Inbound Queue for Changes.
		if ( intval( $last_queue_ammount ) !== $count_current_queue ) {
			$queue_changed = true;
		}

		if ( $queue_changed ) {
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
		$cart_id             = Factory::get_instance( RoomAdmin::class )->get_user_session();
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
			$user_hash = Factory::get_instance( RoomAdmin::class )->get_user_session();
		}

		$user_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $user_hash, $room_name );
		if ( ! $user_object ) {
			return false;
		}
		$user_timestamp = $user_object->get_last_notification() + WooCommerce::SETTING_HEARTBEAT_THRESHOLD;
		$global_object  = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );

		if ( ! $global_object ) {
			return false;
		}

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
	 * @param bool   $last_basket -  Flag to return the contents of the last shared basket.
	 *
	 * @return string|Array depending on return flag
	 */
	public function render_sync_queue_table( string $room_name, bool $object_only = null, bool $last_basket = null ) {
		if ( ! $room_name ) {
			return null;
		}
		// Setting as Global Cart ID in case use case is for the previous cart.

		if ( true === $last_basket ) {
			$cart_id = WooCommerce::SETTING_BASKET_REQUEST_ON;
		} else {
			$cart_id = Factory::get_instance( RoomAdmin::class )->get_user_session();
		}

		$available_queue = Factory::get_instance( WooCommerceVideoDAO::class )->get_queue_records( $cart_id, $room_name );
		$output_array    = array();

		foreach ( $available_queue as $record_id ) {
			$basket_array = $this->get_individual_cart_object( $record_id, $room_name );
			array_push( $output_array, $basket_array );
		}

			// Return Object.
		if ( $object_only ) {
			return $output_array;
		} else {
			// Render View.
			$render = require __DIR__ . '/../views/sync-table-output.php';
			return $render( $output_array, $room_name, $last_basket );
		}
	}

	/**
	 * Get Individual Cart Object
	 * Prepare Queue Table and Render the view, or return just Queue object to other functions.
	 *
	 * @param int    $record_id -  RecordID to return.
	 * @param string $room_name -  Name of Room.
	 *
	 * @return string|Array depending on return flag
	 */
	public function get_individual_cart_object( int $record_id = null, string $room_name = null ):?array {

		// Handling Object if passed in. If not getting it direct.
		if ( $cart ) {
			$cart_item = $cart;
		} else {
			$cart_item = Factory::get_instance( WooCommerceVideoDAO::class )->get_record_by_record_id( $record_id );
		}

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
		}
		if ( $room_name ) {
			$basket_array['am_i_host']         = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
			$basket_array['am_i_broadcasting'] = Factory::get_instance( HostManagement::class )->am_i_broadcasting( $room_name );
			$basket_array['am_i_downloading']  = Factory::get_instance( HostManagement::class )->am_i_downloading( $room_name );
		}
		return $basket_array;
	}
}
