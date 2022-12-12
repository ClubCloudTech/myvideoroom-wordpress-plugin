<?php
/**
 * Ajax Handling Function - switches inbound Ajax requests.
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/AjaxHandler.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Library\TemplateIcons;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShopView;

/**
 * Class Ajax Handler.
 * Handles all elements of Host and Guest Basket Sync
 */
class AjaxHandler {

	/**
	 * Get WooCommerce Basket Ajax Data
	 * Handles Ajax Posts for baskets and refreshes the window depending on what was passed into it
	 */
	public function get_ajax_page_basketwc() {

		$product_id      = Factory::get_instance( Ajax::class )->get_text_parameter( 'productId' );
		$input_type      = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );
		$auth_nonce      = Factory::get_instance( Ajax::class )->get_text_parameter( 'authNonce' );
		$room_name       = Factory::get_instance( Ajax::class )->get_text_parameter( 'roomName' );
		$host_status     = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
		$quantity        = Factory::get_instance( Ajax::class )->get_text_parameter( 'quantity' );
		$variation_id    = Factory::get_instance( Ajax::class )->get_text_parameter( 'variationId' );
		$record_id       = Factory::get_instance( Ajax::class )->get_text_parameter( 'recordId' );
		$last_queuenum   = Factory::get_instance( Ajax::class )->get_text_parameter( 'lastQueuenum' );
		$last_carthash   = Factory::get_instance( Ajax::class )->get_text_parameter( 'lastCarthash' );
		$last_storecount = Factory::get_instance( Ajax::class )->get_text_parameter( 'lastStorecount' );
		$message_room    = Factory::get_instance( Ajax::class )->get_text_parameter( 'messageRoom' );
		$target_window   = Factory::get_instance( Ajax::class )->get_text_parameter( 'target' );

		$response = array();

		if ( WooCommerce::SETTING_REFRESH_BASKET === $input_type ) {
			$input_type = 'reload';
		}
		switch ( $input_type ) {
			/*
			* Product Item Section.
			*
			*These Handlers Handle the Adding and Removal of Individual Products in Basket from the Queue
			*/

			// Case Delete a Product from queue to a Basket - has no Confirmation.

			case WooCommerce::SETTING_DELETE_PRODUCT:
				$success_state = Factory::get_instance( ShoppingBasket::class )->delete_product_from_cart( $product_id );

				if ( $success_state ) {
					$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Product Removed From Basket', 'myvideoroom' ) . '</strong></p>';
					Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
				} else {
					$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Product Removal Failed', 'myvideoroom' ) . '</strong></p>';
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

						// Case Add a Product To a Basket - has no Confirmation for Individual Products - Confirmation for All Products.

			case WooCommerce::SETTING_ADD_PRODUCT:
				// Clear Product add from Nonce in case its accept all.

				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ADD_PRODUCT . $product_id ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {

					$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Product Added to Basket', 'myvideoroom' ) . '</strong></p>';
					Factory::get_instance( ShoppingBasket::class )->add_queued_product_to_cart( $product_id, $quantity, $variation_id, $record_id, null, $room_name );
					Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );

				return \wp_send_json( $response );

			case WooCommerce::SETTING_ACCEPT_ALL_QUEUE:
				$message         = \esc_html__( 'accept all products in your suggested queue ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED, esc_html__( 'Accept All Products', 'myvideoroom' ), $room_name, $nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

			case WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					$success_state = Factory::get_instance( ShoppingBasket::class )->add_all_queued_products_to_cart( $room_name );
					if ( $success_state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'All Products Added To Queue', 'myvideoroom' ) . '</strong></p>';
						Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem adding items to queue - please refresh page', 'myvideoroom' ) . '</strong></p>';
					}
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

			case WooCommerce::SETTING_REJECT_ALL_QUEUE:
				$message         = \esc_html__( 'decline all products in your queue ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED, esc_html__( 'Reject All Products', 'myvideoroom' ), $room_name, $nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

			case WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					Factory::get_instance( ShoppingBasket::class )->delete_all_queued_products_from_cart( $room_name );
					$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'All Products Cleared from Queue', 'myvideoroom' ) . '</strong></p>';
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

				// Case Delete Product from Sync Queue Step1 - Pre Confirmation.

			case WooCommerce::SETTING_DELETE_PRODUCT_QUEUE:
				$message                      = \esc_html__( 'remove this product from your shared list (this action can not be undone) ?', 'myvideoroom' );
				$delete_queue_nonce           = wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE . $record_id );
				$delete_confirmation_nonce    = wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED . $record_id );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED, esc_html__( 'Remove Product', 'myvideoroom' ), $room_name, $delete_confirmation_nonce, $record_id, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $delete_queue_nonce, $message, $confirmation_button_approved, $record_id, $target_window, null, $target_window );
				return \wp_send_json( $response );

			// Case Delete Product from Sync Queue-  Step2 - Post Confirmation.

			case WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED . $record_id ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					$response['feedback'] = esc_html__( 'Product Removed from your shared list', 'myvideoroom' );
					Factory::get_instance( WooCommerceVideoDAO::class )->delete_record( $record_id );
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

			/*
			* Basket Item and Product Broadcast.
			*
			*These Handlers Handle the Basket Management and Product Broadcast (send one product to everyone in room).
			*/

			// Case Delete Entire Basket Step1 - Pre Confirmation.

			case WooCommerce::SETTING_DELETE_BASKET:
				$message                      = \esc_html__( 'clear your basket ?', 'myvideoroom' );
				$delete_basket_nonce          = wp_create_nonce( WooCommerce::SETTING_DELETE_BASKET_CONFIRMED );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_BASKET_CONFIRMED, esc_html__( 'Clear Basket', 'myvideoroom' ), $room_name, $delete_basket_nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

			// Case Delete Entire Basket Step2 - Post Confirmation.

			case WooCommerce::SETTING_DELETE_BASKET_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DELETE_BASKET_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					Factory::get_instance( ShoppingBasket::class )->clear_my_cart();
					Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

			// Case Broadcast Single Product Step1 - Pre Confirmation.

			case WooCommerce::SETTING_BROADCAST_PRODUCT:
				$message                      = \esc_html__( 'share this product ?', 'myvideoroom' );
				$broadcast_product_nonce      = wp_create_nonce( WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_product_share_button( WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED, esc_html__( 'Share Product', 'myvideoroom' ), $room_name, $broadcast_product_nonce, $quantity, $product_id, $variation_id, $target_window );
				if ( WooCommerce::SETTING_STORE_FRAME === $target_window ) {
					$response['shopconfirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved, null, WooCommerce::SETTING_STORE_FRAME, null, WooCommerce::SETTING_STORE_FRAME );
				} else {
					$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved, null, $target_window, null, $target_window );
				}

				return \wp_send_json( $response );

				// Case Delete Entire Basket Step2 - Post Confirmation.
			case WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Broadcast Product Action.
					Factory::get_instance( ShoppingBasket::class )->broadcast_single_product( $product_id, $room_name, $quantity, $variation_id );
					$response['feedback'] = esc_html__( 'The Product has Been Shared', 'myvideoroom' );
				}
				if ( WooCommerce::SETTING_STORE_FRAME === $target_window ) {
					$response['shopwindow'] = Factory::get_instance( ShopView::class )->render_room_management_table( $room_name );
				} else {
					$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				}

				return \wp_send_json( $response );

			/*
			* Basket Sync and Auto update Section.
			*
			*These Handlers Handle the Events for keeping baskets automatically synced.
			*/

			// Case Enable Sync - Turn on Synchronisation for Baskets for Masters who already own the basket.

			case WooCommerce::SETTING_ENABLE_SYNC:
				$message         = \esc_html__( 'stream your basket to others (keep it synced) ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED, esc_html__( 'Turn on Basket Sync', 'myvideoroom' ), $room_name, $nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

				// Case Turn on Basket sync - Post Confirmation.
			case WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Basket Sharing Action.
					$state = Factory::get_instance( HostManagement::class )->turn_on_basket_broadcast( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Basket is now being shared automatically', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem sharing your basket.', 'myvideoroom' ) . '</strong></p>';
					}
				}
				return \wp_send_json( $response );

			case WooCommerce::SETTING_DISABLE_SYNC:
				$message         = \esc_html__( 'stop sharing your Basket ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED, esc_html__( 'Turn Off Basket Sync', 'myvideoroom' ), $room_name, $nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

				// Case Turn on Basket sync - Post Confirmation.
			case WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Basket Sharing Action.

					$state = Factory::get_instance( HostManagement::class )->turn_off_basket_broadcast( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Basket is no longer being shared', 'myvideoroom' ) . '</strong></p>';
						Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem turning off your basket', 'myvideoroom' ) . '</strong></p>';
					}
				}
				return \wp_send_json( $response );

			// Case Enable Basket Download- Turn on Synchronisation for Baskets and download from central.

			case WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD:
				$message         = \esc_html__( 'clear your basket and keep it synced to the room ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED, esc_html__( 'Sync My Basket', 'myvideoroom' ), $room_name, $approved_nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

				// Case Turn on Basket sync - Post Confirmation.
			case WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Basket Sharing Action.

					$state = Factory::get_instance( HostManagement::class )->turn_on_basket_downloads( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Basket is now being updated by the room automatically', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem with setting up your basket synchronisation. Please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

			case WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD:
				$message         = \esc_html__( 'stop synchronising your basket ? Note- your contents will remain after you break the sync', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED, esc_html__( 'Stop Basket Download Sync', 'myvideoroom' ), $room_name, $approved_nonce, null, null, null, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

			case WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Basket Sharing Action.
					$state = Factory::get_instance( HostManagement::class )->turn_off_basket_downloads( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Basket is now under your control. ', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem with stopping your basket synchronisation. Please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}

				$response['basketwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				return \wp_send_json( $response );

			/*
			* Master Request Section.
			*
			*These Handlers Handle the Requests - and Accept/Decline for Becoming Room Master.
			*/

			// Case Request Master - Request Master Status from Current Master.

			case WooCommerce::SETTING_REQUEST_MASTER:
				$message         = \esc_html__( 'request Master Basket Ownership ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED, esc_html__( 'Request Control', 'myvideoroom' ), $room_name, $nonce, null, null, $target_window, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

				// Case Request Master - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Request.
					$state = Factory::get_instance( HostManagement::class )->request_master_status( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Request has been forwarded to Current Basket Owner for Approval', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}
				return \wp_send_json( $response );

				// Case Withdraw Request for Basket Ownership.
			case WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING:
				$message         = \esc_html__( 'cancel your basket ownership request ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW, esc_html__( 'Cancel Request', 'myvideoroom' ), $room_name, $approved_nonce, null, null, $target_window, null, $target_window );

				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

					// Case Request Master - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Request.
					$state = Factory::get_instance( HostManagement::class )->cancel_master_change_request( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Request has been cancelled successfully', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}
				return \wp_send_json( $response );

				// Case Decline Request for Basket Ownership.
			case WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING:
				$message                  = \esc_html__( 'reject the ownership transfer request ?', 'myvideoroom' );
				$nonce                    = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING );
				$declined_nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_DECLINED );
				$button_approved          = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_DECLINED, esc_html__( 'Decline Request', 'myvideoroom' ), $room_name, $declined_nonce, null, null, null, null, $target_window );
				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

					// Case Decline Request for Basket Ownership - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_DECLINED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_DECLINED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Request.
					$state = Factory::get_instance( HostManagement::class )->decline_master_change_request( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'The Request has been cancelled successfully', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}
				return \wp_send_json( $response );

				// Case Approved Request for Basket Ownership.
			case WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING:
				$message                  = \esc_html__( 'approve the transfer. You will no longer control the basket ?', 'myvideoroom' );
				$nonce                    = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING );
				$approved_nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_APPROVED );
				$button_approved          = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_APPROVED, esc_html__( 'Approve Request', 'myvideoroom' ), $room_name, $approved_nonce, null, null, null, null, $target_window );
				$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved, null, $target_window, null, $target_window );
				return \wp_send_json( $response );

					// Case Approve Request for Basket Ownership - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_APPROVED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_APPROVED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Request.

					$state = Factory::get_instance( HostManagement::class )->accept_master_change_request( $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'You are no longer basket owner', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}
				return \wp_send_json( $response );

			/*
			* Save Product to Store Category.
			*
			* These Handlers Handle the save to store category operation.
			*/
			// Case Save Basket to Room Category.
			case WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY:
				$message         = \esc_html__( 'save this product to the room ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY );
				$confirmed_nonce = wp_create_nonce( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED, esc_html__( 'Save it to Room', 'myvideoroom' ), $room_name, $confirmed_nonce, $product_id, null, $target_window, null, $target_window );

				if ( WooCommerce::SETTING_STORE_FRAME === $target_window ) {
					$response['shopconfirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved, null, WooCommerce::SETTING_STORE_FRAME, null, WooCommerce::SETTING_STORE_FRAME );
				} else {
					$response['confirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved, null, $target_window, null, $target_window );
				}

				return \wp_send_json( $response );

			// Case Approve Request for Basket Ownership - Post Confirmation.
			case WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Activate Category.

					$state = Factory::get_instance( ShopView::class )->add_category_to_product( $record_id, $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Product Added to Store', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}
				$response['unhide'] = 'mvr-basket-section';
				return \wp_send_json( $response );

			/*
			* Delete Product to Store Category.
			*
			* These Handlers Handle the save to store category operation.
			*/
			// Case Save Basket to Room Category.
			case WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY:
				$message         = \esc_html__( 'delete this product from the room ? ', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY . $product_id );
				$confirmed_nonce = wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY_CONFIRMED . $product_id );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY_CONFIRMED, esc_html__( 'Remove It From Room', 'myvideoroom' ), $room_name, $confirmed_nonce, $product_id, null, null, null, $target_window );

				$response['shopconfirmation'] = Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved, $product_id, WooCommerce::SETTING_STORE_FRAME, null, WooCommerce::SETTING_STORE_FRAME );
				return \wp_send_json( $response );

					// Case Approve Request for Delete from Room - Post Confirmation.
			case WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DELETE_PRODUCT_CATEGORY_CONFIRMED . $record_id ) ) {
					$response['feedback'] = esc_html__( 'This Operation is Not Authorised', 'myvideoroom' );
				} else {

					// Delete Category.

					$state = Factory::get_instance( ShopView::class )->delete_category_from_product( $record_id, $room_name );
					if ( true === $state ) {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'Your Request has been Completed successfully', 'myvideoroom' ) . '</strong></p>';
					} else {
						$response['feedback'] = '<p class="mvr-notification-align"><strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong></p>';
					}
				}
				$response['shopwindow'] = Factory::get_instance( ShopView::class )->render_room_management_table( $room_name );
				return \wp_send_json( $response );

			/*
			* Refreshes and Heartbeats.
			*
			*These Handlers Handle the Refresh and update triggers from the Ajax scripts.
			*/
			case 'notify':
				$response['notificationbar'] = Factory::get_instance( ShoppingBasket::class )->render_notification_tab( $room_name );
				return \wp_send_json( $response );

			case 'refresh':
				// Change States.
				$store_status  = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( WooCommerce::MODULE_WOOCOMMERCE_STORE_ID );
				$basket_status = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( WooCommerce::MODULE_WOOCOMMERCE_BASKET_ID );
				if ( $basket_status ) {
					$client_change_state             = Factory::get_instance( ShoppingBasket::class )->check_for_user_cart_changes( $last_carthash, $room_name );
					$notification_queue_change_state = Factory::get_instance( ShoppingBasket::class )->check_for_product_queue_changes( $last_queuenum, $room_name );
				}
				if ( $store_status ) {
					$store_change_state = Factory::get_instance( ShopView::class )->has_room_store_changed( $room_name, $last_storecount );
				}
				$my_session       = Factory::get_instance( RoomAdmin::class )->get_user_session();
				$change_heartbeat = Factory::get_instance( ShoppingBasket::class )->user_notification_heartbeat( $room_name, $my_session );
				$host_status      = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				// Only Check for Room Change if Global Update Flag has Fired.
				if ( $change_heartbeat ) {
					$room_change_heartbeat     = Factory::get_instance( RoomAdmin::class )->room_change_heartbeat( $room_name );
					$security_change_heartbeat = Factory::get_instance( RoomAdmin::class )->security_change_heartbeat( $room_name );
				}

				$response           = array();
				$response['source'] = 'refresh';
				if ( true === $store_change_state ) {
					$response['storestatus'] = 'change';
					$response['reason']      = 'Store Change';
					$response['storefront']  = Factory::get_instance( ShopView::class )->show_shop( $room_name );
				}

				if ( true === $client_change_state ) {
					$response['status']     = 'change';
					$response['reason']     = 'Client Change';
					$response['mainwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );

				}
				if ( true === $notification_queue_change_state ) {
					$response['status']     = 'change';
					$response['reason']     = 'Notification Queue';
					$response['mainwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				}

				if ( true === $change_heartbeat ) {
					$response['status']     = 'change';
					$response['reason']     = 'Change Heartbeat';
					$response['mainwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				}

				if ( $client_change_state || $store_change_state || $notification_queue_change_state || $change_heartbeat ) {
					$response['messagewindow']   = 'change';
					$response['notificationbar'] = Factory::get_instance( ShoppingBasket::class )->render_notification_tab( $room_name, $client_change_state, $store_change_state, $notification_queue_change_state, $security_change_heartbeat );
				} else {
					$response['status'] = 'nochange';
				}

				if ( $room_change_heartbeat ) {
					$response['settingchange'] = 'change';
					$response['reason']        = 'Room Change';
					$response['mainvideo']     = Factory::get_instance( RoomAdmin::class )->update_main_video_window( $room_change_heartbeat, $message_room );
					$response['videosetting']  = Factory::get_instance( RoomAdmin::class )->update_video_settings_window( $room_change_heartbeat );
					$response['icons']         = apply_filters( 'myvideoroom_template_icon_section', Factory::get_instance( TemplateIcons::class )->show_icon( $room_change_heartbeat->get_user_id(), $room_name ), $room_change_heartbeat->get_user_id(), $room_name, ! $host_status );

					if ( Factory::get_instance( Module::class )->is_module_active( Security::MODULE_SECURITY_NAME ) ) {
						$response['securitysetting'] = Factory::get_instance( RoomAdmin::class )->update_security_settings_window( $room_change_heartbeat );
					}
					Factory::get_instance( RoomSyncDAO::class )->reset_timestamp( $room_name );
				}

				if ( $security_change_heartbeat ) {
					$response['securitychange']  = 'change';
					$response['icons']           = apply_filters( 'myvideoroom_template_icon_section', null, $security_change_heartbeat->get_user_id(), $room_name, ! $host_status );
					$response['securitysetting'] = Factory::get_instance( RoomAdmin::class )->update_security_settings_window( $security_change_heartbeat );

				}
				return \wp_send_json( $response );

			case 'reload':
					$response = array();

						$response['notificationbar'] = Factory::get_instance( ShoppingBasket::class )->render_notification_tab( $room_name );
						$response['storefront']      = Factory::get_instance( ShopView::class )->show_shop( $room_name );
						$host_status                 = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
						$response['mainwindow']      = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );

				return \wp_send_json( $response );
		}
		die();
	}

}
