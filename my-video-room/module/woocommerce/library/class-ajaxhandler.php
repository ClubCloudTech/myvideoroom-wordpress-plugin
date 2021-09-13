<?php
/**
 * Ajax Handling Function - switches inbound Ajax requests.
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/AjaxHandler.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\RoomAdmin;
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
					echo '<strong>' . esc_html__( 'Product Removed From Basket', 'myvideoroom' ) . '</strong>';
					Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
				} else {
					echo '<strong>' . esc_html__( 'Product Removal Failed', 'myvideoroom' ) . '</strong>';
				}

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

						// Case Add a Product To a Basket - has no Confirmation for Individual Products - Confirmation for All Products.

			case WooCommerce::SETTING_ADD_PRODUCT:
				// Clear Product add from Nonce in case its accept all.

				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ADD_PRODUCT . $product_id ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {

					echo '<strong>' . esc_html__( 'Product Added to Basket', 'myvideoroom' ) . '</strong>';
					Factory::get_instance( ShoppingBasket::class )->add_queued_product_to_cart( $product_id, $quantity, $variation_id, $record_id, null, $room_name );
					Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
				}

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );

				break;

			case WooCommerce::SETTING_ACCEPT_ALL_QUEUE:
				$message         = \esc_html__( 'accept all products in your suggested queue ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED, esc_html__( 'Accept All Products', 'my-video-room' ), $room_name, $nonce );
					// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );
				break;

			case WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ACCEPT_ALL_QUEUE_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					$success_state = Factory::get_instance( ShoppingBasket::class )->add_all_queued_products_to_cart( $room_name );
					if ( $success_state ) {
						echo '<strong>' . esc_html__( 'All Products Added To Queue', 'myvideoroom' ) . '</strong>';
						Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
					} else {
						echo '<strong>' . esc_html__( 'There was a problem adding items to queue - please refresh page', 'myvideoroom' ) . '</strong>';
					}
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			case WooCommerce::SETTING_REJECT_ALL_QUEUE:
				$message         = \esc_html__( 'decline all products in your queue ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED, esc_html__( 'Reject All Products', 'my-video-room' ), $room_name, $nonce );
					// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );

				break;

			case WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REJECT_ALL_QUEUE_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					Factory::get_instance( ShoppingBasket::class )->delete_all_queued_products_from_cart( $room_name );
					echo '<strong>' . esc_html__( 'All Products Cleared from Queue', 'myvideoroom' ) . '</strong>';
				}

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

				// Case Delete Product from Sync Queue Step1 - Pre Confirmation.

			case WooCommerce::SETTING_DELETE_PRODUCT_QUEUE:
				$message                      = \esc_html__( 'remove this product from your shared list (this action can not be undone) ?', 'myvideoroom' );
				$delete_queue_nonce           = wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE . $record_id );
				$delete_confirmation_nonce    = wp_create_nonce( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED . $record_id );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED, esc_html__( 'Remove Product', 'my-video-room' ), $room_name, $delete_confirmation_nonce, $record_id );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $delete_queue_nonce, $message, $confirmation_button_approved, $record_id );
				break;

			// Case Delete Product from Sync Queue-  Step2 - Post Confirmation.

			case WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED . $record_id ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					esc_html_e( 'Product Removed from your shared list', 'myvideoroom' );
					Factory::get_instance( WooCommerceVideoDAO::class )->delete_record( $record_id );
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			/*
			* Basket Item and Product Broadcast.
			*
			*These Handlers Handle the Basket Management and Product Broadcast (send one product to everyone in room).
			*/

			// Case Delete Entire Basket Step1 - Pre Confirmation.

			case WooCommerce::SETTING_DELETE_BASKET:
				$message                      = \esc_html__( 'clear your basket ?', 'myvideoroom' );
				$delete_basket_nonce          = wp_create_nonce( WooCommerce::SETTING_DELETE_BASKET_CONFIRMED );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DELETE_BASKET_CONFIRMED, esc_html__( 'Clear Basket', 'my-video-room' ), $room_name, $delete_basket_nonce );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved );
				break;

			// Case Delete Entire Basket Step2 - Post Confirmation.

			case WooCommerce::SETTING_DELETE_BASKET_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DELETE_BASKET_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					Factory::get_instance( ShoppingBasket::class )->clear_my_cart();
					Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			// Case Broadcast Single Product Step1 - Pre Confirmation.

			case WooCommerce::SETTING_BROADCAST_PRODUCT:
				$message                      = \esc_html__( 'share this product ?', 'myvideoroom' );
				$broadcast_product_nonce      = wp_create_nonce( WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_product_share_button( WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED, esc_html__( 'Share Product', 'my-video-room' ), $room_name, $broadcast_product_nonce, $quantity, $product_id, $variation_id );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved );
				break;

				// Case Delete Entire Basket Step2 - Post Confirmation.
			case WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_BROADCAST_PRODUCT_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Broadcast Product Action.
					Factory::get_instance( ShoppingBasket::class )->broadcast_single_product( $product_id, $room_name, $quantity, $variation_id );
					echo esc_html_e( 'The Product has Been Shared', 'myvideoroom' );
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			/*
			* Basket Sync and Auto update Section.
			*
			*These Handlers Handle the Events for keeping baskets automatically synced.
			*/

			// Case Enable Sync - Turn on Synchronisation for Baskets for Masters who already own the basket.

			case WooCommerce::SETTING_ENABLE_SYNC:
				$message         = \esc_html__( 'stream your basket to others (keep it synced) ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED, esc_html__( 'Turn on Basket Sync', 'my-video-room' ), $room_name, $nonce );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );
				break;

				// Case Turn on Basket sync - Post Confirmation.
			case WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ENABLE_SYNC_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Basket Sharing Action.
					$state = Factory::get_instance( HostManagement::class )->turn_on_basket_broadcast( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Basket is now being shared automatically', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem sharing your basket.', 'myvideoroom' ) . '</strong>';
					}
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			case WooCommerce::SETTING_DISABLE_SYNC:
				$message         = \esc_html__( 'stop sharing your Basket ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED, esc_html__( 'Turn Off Basket Sync', 'my-video-room' ), $room_name, $nonce );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );
				break;

				// Case Turn on Basket sync - Post Confirmation.
			case WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DISABLE_SYNC_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Basket Sharing Action.

					$state = Factory::get_instance( HostManagement::class )->turn_off_basket_broadcast( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Basket is no longer being shared', 'myvideoroom' ) . '</strong>';
						Factory::get_instance( HostManagement::class )->notify_if_broadcasting( $room_name );
					} else {
						echo '<strong>' . esc_html__( 'There was a problem turning off your basket', 'myvideoroom' ) . '</strong>';
					}
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			// Case Enable Basket Download- Turn on Synchronisation for Baskets and download from central.

			case WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD:
				$message         = \esc_html__( 'clear your basket and keep it synced to the room ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED, esc_html__( 'Turn on Basket Download Sync', 'my-video-room' ), $room_name, $approved_nonce );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );
				break;

				// Case Turn on Basket sync - Post Confirmation.
			case WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Basket Sharing Action.

					$state = Factory::get_instance( HostManagement::class )->turn_on_basket_downloads( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Basket is now being updated by the room automatically', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem with setting up your basket synchronisation. Please refresh', 'myvideoroom' ) . '</strong>';
					}
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			case WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD:
				$message         = \esc_html__( 'stop synchronising your basket ? Note- your contents will remain after you break the sync', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED, esc_html__( 'Stop Basket Download Sync', 'my-video-room' ), $room_name, $approved_nonce );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );
				break;

			case WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Basket Sharing Action.
					$state = Factory::get_instance( HostManagement::class )->turn_off_basket_downloads( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Basket is now under your control. ', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem with stopping your basket synchronisation. Please refresh', 'myvideoroom' ) . '</strong>';
					}
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			/*
			* Master Request Section.
			*
			*These Handlers Handle the Requests - and Accept/Decline for Becoming Room Master.
			*/

			// Case Request Master - Request Master Status from Current Master.

			case WooCommerce::SETTING_REQUEST_MASTER:
				$message         = \esc_html__( 'request Master Basket Ownership ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED, esc_html__( 'Request Control', 'my-video-room' ), $room_name, $nonce );

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $button_approved );
				break;

				// Case Request Master - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Request.
					$state = Factory::get_instance( HostManagement::class )->request_master_status( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Request has been forwarded to Current Basket Owner for Approval', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong>';
					}
				}
				$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

				// Case Withdraw Request for Basket Ownership.
			case WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING:
				$message         = \esc_html__( 'cancel your basket ownership request ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW, esc_html__( 'Cancel Request', 'myvideoroom' ), $room_name, $approved_nonce );

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved );
				break;

					// Case Request Master - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Request.
					$state = Factory::get_instance( HostManagement::class )->cancel_master_change_request( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Request has been cancelled successfully', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong>';
					}
				}

				$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

				// Case Decline Request for Basket Ownership.
			case WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING:
				$message         = \esc_html__( 'reject the ownership transfer request ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING );
				$declined_nonce  = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_DECLINED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_DECLINED, esc_html__( 'Decline Request', 'myvideoroom' ), $room_name, $declined_nonce );

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved );
				break;

					// Case Decline Request for Basket Ownership - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_DECLINED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_DECLINED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn Off Request.
					$state = Factory::get_instance( HostManagement::class )->decline_master_change_request( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'The User Request has been cancelled successfully', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong>';
					}
				}

				$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

				// Case Approved Request for Basket Ownership.
			case WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING:
				$message         = \esc_html__( 'approve the transfer. You will no longer control the basket ?', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING );
				$approved_nonce  = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_APPROVED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_REQUEST_MASTER_APPROVED, esc_html__( 'Approve Request', 'myvideoroom' ), $room_name, $approved_nonce );

				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved );
				break;

					// Case Approve Request for Basket Ownership - Post Confirmation.
			case WooCommerce::SETTING_REQUEST_MASTER_APPROVED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_REQUEST_MASTER_APPROVED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Turn On Request.

					$state = Factory::get_instance( HostManagement::class )->accept_master_change_request( $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Request has been approved successfully', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong>';
					}
				}

				$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			/*
			* Save Product to Store Category.
			*
			* These Handlers Handle the save to store category operation.
			*/
			// Case Save Basket to Room Category.
			case WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY:
				$message         = \esc_html__( 'save this product to the room ? This will add it to the room category. To undo, simply remove the category from this product to remove it from the room', 'myvideoroom' );
				$nonce           = wp_create_nonce( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY );
				$confirmed_nonce = wp_create_nonce( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED );
				$button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED, esc_html__( 'Save it to Room', 'myvideoroom' ), $room_name, $confirmed_nonce, $product_id );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $nonce, $message, $button_approved );
				break;

					// Case Approve Request for Basket Ownership - Post Confirmation.
			case WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Activate Category.

					$state = Factory::get_instance( ShopView::class )->add_category_to_product( $record_id, $room_name );
					if ( true === $state ) {
						echo '<strong>' . esc_html__( 'Your Request has been Completed successfully', 'myvideoroom' ) . '</strong>';
					} else {
						echo '<strong>' . esc_html__( 'There was a problem making your request - please refresh', 'myvideoroom' ) . '</strong>';
					}
				}

				$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			/*
			* Refreshes and Heartbeats.
			*
			*These Handlers Handle the Refresh and update triggers from the Ajax scripts.
			*/

			case 'refresh':
				// Change States.
				$client_change_state             = Factory::get_instance( ShoppingBasket::class )->check_for_user_cart_changes( $last_carthash, $room_name );
				$store_change_state              = Factory::get_instance( ShopView::class )->has_room_store_changed( $room_name, $last_storecount );
				$notification_queue_change_state = Factory::get_instance( ShoppingBasket::class )->check_for_product_queue_changes( $last_queuenum, $room_name );
				$change_heartbeat                = Factory::get_instance( ShoppingBasket::class )->user_notification_heartbeat( $room_name, Factory::get_instance( RoomAdmin::class )->get_user_session() );
				$room_change_heartbeat           = Factory::get_instance( ShoppingBasket::class )->user_notification_heartbeat( $room_name, Factory::get_instance( RoomAdmin::class )->get_user_session() );

				$response = array();

				if ( true === $store_change_state ) {
					$response['storestatus'] = 'change';
					$response['storefront']  = Factory::get_instance( ShopView::class )->show_shop( $room_name );
				}

				if ( true === $client_change_state ) {
					$host_status            = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
					$response['status']     = 'change';
					$response['mainwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );

				}
				if ( true === $notification_queue_change_state ) {
					$host_status            = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
					$response['status']     = 'change';
					$response['mainwindow'] = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				}

				if ( $client_change_state || $store_change_state || $notification_queue_change_state || $change_heartbeat ) {
					$response['messagewindow']   = 'change';
					$response['notificationbar'] = Factory::get_instance( ShoppingBasket::class )->render_notification_tab( $room_name, $client_change_state, $store_change_state, $notification_queue_change_state );
				} else {
					$response['status'] = 'nochange';
				}

				return \wp_send_json( $response );

			case 'reload':
					$response = array();

						$response['notificationbar'] = Factory::get_instance( ShoppingBasket::class )->render_notification_tab( $room_name );
						$response['storefront']      = Factory::get_instance( ShopView::class )->show_shop( $room_name );
						$host_status                 = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
						$response['mainwindow']      = Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );

				return \wp_send_json( $response );

			default:
				$host_status = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
		}
		die();
	}

}
