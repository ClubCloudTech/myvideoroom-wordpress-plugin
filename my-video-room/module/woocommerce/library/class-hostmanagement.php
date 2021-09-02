<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/HostManagement.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class Host Management.
 * Handles all elements of Host and Guest Basket Sync
 */
class HostManagement {

	/**
	 * Get Room Master
	 *
	 * @param string $room_name -  Name of Room.
	 * @return ?string
	 */
	public function get_master_status( string $room_name ):?string {

		$masters = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_room_masters( $room_name );
		// Case No Masters.
		if ( ! $masters ) {
			return null;
			// Case One or More. Returns first ID as Query is sorted descending (latest first ) by timestamp for last accessed.
		} else {
			return $masters[0]->cart_id;
		}
	}

	/**
	 * Get Room Hosts
	 *
	 * @param string $room_name -  Name of Room.
	 * @return array of Hosts.
	 */
	public function get_room_hosts( string $room_name ): array {

		$hosts_objects = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_room_hosts_from_db( $room_name );

		$output = array();
		foreach ( $hosts_objects as $host ) {
			\array_push( $output, $host->cart_id );
		}
		return $output;
	}

	/**
	 * Initialise - Return if current session is a Host
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function am_i_host( $room_name ): bool {

		$host_status = false;
		$my_session  = $this->get_user_session();
		$room_hosts  = $this->get_room_hosts( $room_name );

		foreach ( $room_hosts as $host ) {

			if ( $host === $my_session ) {
				$host_status = true;
			}
		}
		return $host_status;
	}

	/**
	 * Initialise - Return if current session is master
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function am_i_master( $room_name ): bool {

		$my_session     = $this->get_user_session();
		$current_master = $this->get_master_status( $room_name );

		if ( $my_session === $current_master ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Returns if User is Currently Syncing Basket.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function am_i_syncing( $room_name ): bool {

		$my_session  = $this->get_user_session();

		$sync_object = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );

		if ( ! $sync_object ) {
			return false;
		}

		if ( $my_session === $sync_object->get_sync_state() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns if User is Currently Syncing Basket.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function is_sync_available( $room_name ): bool {

		$sync_object = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );
		if ( ! $sync_object ) {
			return false;
		}

		if ( $sync_object->get_sync_state() ) {
			return true;
		}
		return false;
	}

	/**
	 * Turn On Sync - Turn on Basket Sync
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $new_master_id -  New Master ID to set (if blank will use current user).
	 * @return bool
	 */
	public function turn_on_basket_sync( string $room_name, string $new_master_id = null ): bool {

		if ( ! $new_master_id ) {
			$new_master_id = $this->get_user_session();
		}

		$am_i_host   = $this->am_i_host( $room_name );
		$am_i_master = $this->am_i_master( $room_name );

		if ( ! $am_i_master || ! $am_i_host ) {
			echo 'Error - User ' . esc_attr( $new_master_id ) . ' is not Host or Master - ';
			return false;
		}
		// Change State.
		Factory::get_instance( WooCommerceRoomSyncDAO::class )->change_basket_sync_state( $room_name, $new_master_id );

		// Notify User's Queue, and Main one.
		$this->notify_user( $room_name, $new_master_id );
		$this->notify_user( $room_name );

		return true;

	}

	/**
	 * Turn Off Sync - Turns Off Basket Sync
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function turn_off_basket_sync( $room_name ): bool {

		// Change State.
		$state = Factory::get_instance( WooCommerceRoomSyncDAO::class )->change_basket_sync_state( $room_name );

		if ( ! $state ) {
			return false;
		}

		// Notify Main one Queue.
		$this->notify_user( $room_name );

		return true;

	}


	/**
	 * Get My Basket Request Change State
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $hash_id - User Hash to match. (optional) - will update master record if nothing received.
	 * @param bool   $return_all - Flag to return the whole hash.
	 * @return null|string
	 */
	public function get_my_basket_request_state( string $room_name, string $hash_id = null, bool $return_all = null ): ?string {

		if ( ! $hash_id ) {
			$hash_id = $this->get_user_session();
		}

		$user_object = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( $hash_id, $room_name );
		$state_hash  = $user_object->get_sync_state();

		if ( ! $state_hash ) {
			return null;
		}
		if ( $return_all ) {
			return $state_hash;
		} else {
			$state = strtok( $state_hash, ',' );
			return $state;
		}

	}


	/**
	 * Flag for Notification
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $hash_id - User Hash to match. (optional) - will update master record if nothing received.
	 * @return bool
	 */
	public function notify_user( string $room_name, string $hash_id = null ): bool {

		if ( ! $hash_id ) {
			$hash_id = WooCommerce::SETTING_BASKET_REQUEST_USER;
		}

		// Change State.
		Factory::get_instance( WooCommerceRoomSyncDAO::class )->notify_user( $room_name, $hash_id );

		return false;

	}

	/**
	 * Flag for Master Change Request
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $hash_id - User Hash to match. (optional) - will update master record if nothing received.
	 * @return bool
	 */
	public function notify_master_change_request( string $room_name, string $hash_id = null ): bool {

		if ( ! $hash_id ) {
			$hash_id = $this->get_master_status( $room_name );
		}

		// Request Master Change.
		$my_session = $this->get_user_session();

		// Notify Master.
		$master = Factory::get_instance( WooCommerceRoomSyncDAO::class )->change_basket_sync_state( $room_name, $hash_id, WooCommerce::SETTING_REQUEST_MASTER . ',' . $my_session );
		// Update Own User for Pending.
		$requestor = Factory::get_instance( WooCommerceRoomSyncDAO::class )->change_basket_sync_state( $room_name, $my_session, WooCommerce::SETTING_REQUEST_MASTER_PENDING . ',' . $hash_id );

		// Return Success State.
		if ( $master && $requestor ) {
			return true;

		} else {

			return false;
		}

	}

	/**
	 * Cancel Master Change Request
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $hash_id - User Hash to match. (optional) - will update master record if nothing received.
	 * @return bool
	 */
	public function cancel_master_change_request( string $room_name, string $hash_id = null ): bool {

		if ( ! $hash_id ) {
			$hash_id = $this->get_master_status( $room_name );
		}

		// Request Master Change.
		$my_session = $this->get_user_session();

		// Notify Master if Current Queue request is from this user.
		$original_hash = $this->get_my_basket_request_state( $room_name, null, true );

		$reported_master = substr($original_hash, strpos($original_hash, ',') + 1);
		$current_master  = $this->get_master_status( $room_name );

		// Remove Notification from Master Record if our request is still the one considered valid.
		if ( $current_master === $reported_master ) {
			$master = Factory::get_instance( WooCommerceRoomSyncDAO::class )->change_basket_sync_state( $room_name, $current_master, null, true );
		} else {
			$master = true;
		}
		// Update Own User to remove Pending Sync Flag.
		$requestor = Factory::get_instance( WooCommerceRoomSyncDAO::class )->change_basket_sync_state( $room_name, $my_session, null, true );

		// Return Success State.
		if ( $master && $requestor ) {
			return true;

		} else {

			return false;
		}

	}



	/**
	 * Initialise - Check AND Set Room Master
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status - If User is Host.
	 * @return ?string
	 */
	public function initialise_master_status( $room_name, $host_status = null ): bool {

		$my_session           = $this->get_user_session();
		$current_master       = $this->get_master_status( $room_name );
		$masters              = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_room_masters( $room_name );
		$current_master_count = count( $masters );

		// Clean Invalid Hosts - Replace with this user if Host - or Most Recent Master if not.
		if ( $host_status && $current_master_count >= 2 ) {

			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_master( $my_session, $room_name );

		} elseif ( ! $host_status && $current_master_count >= 2 ) {

			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_master( $current_master, $room_name );
		}

		if ( ! $current_master && true === $host_status ) {

			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_master( $my_session, $room_name );

			return true;
		}
		// Now Return Status.

		return $this->am_i_master( $room_name );

	}

	/**
	 * Request Master Status.
	 * Used by users to request master status from a master.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function request_master_status( string $room_name ): bool {

		if ( ! $this->am_i_host( $room_name ) ) {
			return false;
		}

		// Get Current Master.
		$current_master = $this->get_master_status( $room_name );

		// If no current master - set user as master and skip workflow.
		if ( ! $current_master ) {
			$this->initialise_master_status( $room_name, true );
			return true;
		}

		// Notify Current Master.
		$success = $this->notify_master_change_request( $room_name, $current_master );

		if ( $success ) {
			return true;

		} else {
			return false;
		}
	}

	/**
	 * Master Notification Button.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return string
	 */
	public function master_button( $room_name ): ?string {

		$id_text        = $this->get_user_session();
		$host_status    = $this->am_i_host( $room_name );
		$sync_is_on     = $this->am_i_syncing( $room_name );
		$master_status  = Factory::get_instance( self::class )->am_i_master( $room_name );
		$sync_requested = $this->get_my_basket_request_state( $room_name );
		if ( $sync_requested ) {
			return null;
		}

		if ( $sync_is_on ) {
			$nonce        = wp_create_nonce( WooCommerce::SETTING_DISABLE_SYNC );
			$button_label = \esc_html__( 'Stop Sharing Basket', 'myvideoroom' );
			$button_type  = WooCommerce::SETTING_DISABLE_SYNC;
			return '
			<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
			<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</div>
			';
		}

		if ( $master_status ) {
			$nonce        = wp_create_nonce( WooCommerce::SETTING_ENABLE_SYNC );
			$button_label = \esc_html__( 'Share Basket', 'myvideoroom' );
			$button_type  = WooCommerce::SETTING_ENABLE_SYNC;
			return '
			<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
			<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</div>
			';
		} elseif ( $host_status ) {
			$nonce        = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER );
			$button_label = \esc_html__( 'Request Shared Basket Control', 'myvideoroom' );
			$button_type  = WooCommerce::SETTING_REQUEST_MASTER;
			return '
			<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
			<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</div>
			';
		} else {
			return '';
		}
	}

	/**
	 * Sync Notification Button.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return string
	 */
	public function sync_notification_button( $room_name ): ?string {

		$sync_status = $this->get_my_basket_request_state( $room_name );

		switch ( $sync_status ) {
			case WooCommerce::SETTING_REQUEST_MASTER_PENDING:
				$id_text               = $this->get_user_session();
				$withdraw_nonce        = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING );
				$withdraw_button_label = \esc_html__( 'Cancel Transfer Request', 'myvideoroom' );
				$withdraw_button_type  = WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING;

				return '
				<div> <p> ' . \esc_html__( 'You have Requested to Take Control of the Shared Basket', 'myvideoroom' ) . '</p>

					<button class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
					<a href="" data-input-type="' . $withdraw_button_type . '" data-auth-nonce="' . $withdraw_nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $withdraw_button_label . '</a>
					</button>
				</div>
				';
				break;

			case WooCommerce::SETTING_REQUEST_MASTER:
				$id_text              = $this->get_user_session();
				$accept_nonce         = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING );
				$accept_button_label  = \esc_html__( 'Accept Transfer Request', 'myvideoroom' );
				$accept_button_type   = WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING;
				$decline_nonce        = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING );
				$decline_button_label = \esc_html__( 'Decline Transfer Request', 'myvideoroom' );
				$decline_button_type  = WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING;

				return '
				<div> <p> ' . \esc_html__( 'A request to take control of the shared basket has been received', 'myvideoroom' ) . '</p>

					<button class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
					<a href="" data-input-type="' . $accept_button_type . '" data-auth-nonce="' . $accept_nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $accept_button_label . '</a>
					</button>

					<button class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
					<a href="" data-input-type="' . $decline_button_type . '" data-auth-nonce="' . $decline_nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $decline_button_label . '</a>
					</button>
				</div>
				';
				break;
		
		
			}
return '';

	}


	/**
	 * Check for Basket Requests - Verify Basket Change Request Status.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function check_basket_sync_request( string $room_name ): ?string {

		$basket_state = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );

		// If No Basket Sync state (eg deleted table etc) repair.

		if ( ! $basket_state ) {
			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_basket_transfer_state( $room_name );
			$basket_state = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );
		}

		return $basket_state->get_basket_change_setting();
	}


	/**
	 * Get Session ID for Cart Synchronisation.
	 *
	 * @param ?int $user_id The user id. (optional).
	 *
	 * @return string the session ID of the user.
	 */
	public function get_user_session( int $user_id = null ): string {

		if ( $user_id ) {
			return  wp_hash( $user_id );

		} elseif ( is_user_logged_in() ) {

			return wp_hash( get_current_user_id() );
		} else {

			// Get php session hash.
			if ( ! session_id() ) {
				session_start();
			}
			return session_id();
		}
	}
}
