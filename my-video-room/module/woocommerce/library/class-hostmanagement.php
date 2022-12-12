<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/HostManagement.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\DAO\SessionState;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\RoomAdmin;
use MyVideoRoomPlugin\Library\SectionTemplates;
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

		$masters = Factory::get_instance( RoomSyncDAO::class )->get_room_masters( $room_name );
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

		$hosts_objects = Factory::get_instance( RoomSyncDAO::class )->get_room_hosts_from_db( $room_name );

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
		$my_session  = Factory::get_instance( RoomAdmin::class )->get_user_session();
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

		$my_session     = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$current_master = $this->get_master_status( $room_name );

		if ( $my_session === $current_master ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Notify If Broadcasting.
	 * Sends a message to the global heartbeat if user is currently broadcasting a basket.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function notify_if_broadcasting( string $room_name ): bool {

		$am_i_broadcasting = $this->am_i_broadcasting( $room_name );

		if ( $room_name && $am_i_broadcasting ) {
			Factory::get_instance( SessionState::class )->notify_user( $room_name );
			return true;
		}
		return false;
	}

	/**
	 * Returns if User is Currently Syncing Basket.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function am_i_broadcasting( string $room_name ): bool {

		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();

		$sync_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );

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
	public function am_i_downloading( $room_name ): bool {

		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();

		$sync_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $my_session, $room_name );

		if ( ! $sync_object ) {
			return false;
		}

		if ( WooCommerce::SETTING_BASKET_REQUEST_ON === $sync_object->get_basket_change() ) {

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns if Sync is currently available.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function is_sync_available( string $room_name ): bool {

		$sync_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );
		if ( ! $sync_object ) {
			return false;
		}

		if ( $sync_object->get_sync_state() ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns if Master is still Active.
	 *
	 * @param string $room_name -  Name of Room.
	 * @param string $master_id -  ID of Master if Known.
	 * @return bool
	 */
	public function is_master_active( $room_name, string $master_id = null ): bool {

		if ( ! $master_id ) {
			$master_id = $this->get_master_status( $room_name );

			if ( ! $master_id ) {
				return false;
			}
		}

		$sync_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $master_id, $room_name );

		if ( ! $sync_object ) {
			return false;
		}

		$last_active          = $sync_object->get_timestamp();
		$tolerance_for_active = WooCommerce::SETTING_TOLERANCE_FOR_LAST_ACTIVE;
		$timestamp            = \current_time( 'timestamp' );

		if ( $timestamp - $tolerance_for_active <= $last_active ) {
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
	public function turn_on_basket_broadcast( string $room_name, string $new_master_id = null ): bool {

		if ( ! $new_master_id ) {
			$new_master_id = Factory::get_instance( RoomAdmin::class )->get_user_session();
		}

		$am_i_host   = $this->am_i_host( $room_name );
		$am_i_master = $this->am_i_master( $room_name );

		if ( ! $am_i_master || ! $am_i_host ) {
			return false;
		}
		// Change State.
		Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $new_master_id );

		// Notify User's Queue, and Main one.
		Factory::get_instance( SessionState::class )->notify_user( $room_name, $new_master_id );
		Factory::get_instance( SessionState::class )->notify_user( $room_name );

		return true;

	}

	/**
	 * Turn Off Sync - Turns Off Basket Sync
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function turn_off_basket_broadcast( $room_name ): bool {

		// Change State.
		$state = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name );

		if ( ! $state ) {
			return false;
		}

		// Notify Main Queue.
		Factory::get_instance( SessionState::class )->notify_user( $room_name );

		return true;

	}

	/**
	 * Does Room Exist - checks if room exists.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function does_room_exist( $room_name ): bool {

		$current_record = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );
		if ( $current_record ) {
			return true;
		} else {
			return false;
		}
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
			$hash_id = Factory::get_instance( RoomAdmin::class )->get_user_session();
		}

		$user_object = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( $hash_id, $room_name );
		if ( $user_object ) {
			$state_hash = $user_object->get_sync_state();
		}

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
		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();

		// Notify Master.
		$master = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $hash_id, WooCommerce::SETTING_REQUEST_MASTER . ',' . $my_session );
		// Update Own User for Pending.
		$requestor = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $my_session, WooCommerce::SETTING_REQUEST_MASTER_PENDING . ',' . $hash_id );

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
		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();

		// Notify Master if Current Queue request is from this user.
		$original_hash = $this->get_my_basket_request_state( $room_name, null, true );

		$reported_master = substr( $original_hash, strpos( $original_hash, ',' ) + 1 );
		$current_master  = $this->get_master_status( $room_name );

		// Remove Notification from Master Record if our request is still the one considered valid.
		if ( $current_master === $reported_master ) {
			$master = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $current_master, null, true );
		} else {
			$master = true;
		}
		// Update Own User to remove Pending Sync Flag.
		$requestor = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $my_session, null, true );

		// Return Success State.
		if ( $master && $requestor ) {
			return true;

		} else {

			return false;
		}

	}

	/**
	 * Accept Request and Change Master.
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $autoaccept -  If this is an autoaccept request (dont decode user - it is logged in hash).
	 * @return bool
	 */
	public function accept_master_change_request( string $room_name, bool $autoaccept = null ): bool {

		// Check Permissions.
		$master = $this->am_i_master( $room_name );
		if ( ! $master ) {
			return false;
		}

		if ( $autoaccept ) {
			$user_requestor = Factory::get_instance( RoomAdmin::class )->get_user_session();

		} else {
			// Decode User from request.
			$original_hash  = $this->get_my_basket_request_state( $room_name, null, true );
			$user_requestor = substr( $original_hash, strpos( $original_hash, ',' ) + 1 );
		}

		// Make the Change.
		$success = Factory::get_instance( RoomSyncDAO::class )->update_master( $user_requestor, $room_name );

		// Notify Users.

		if ( $success ) {
			// Stop Sharing Basket.
			Factory::get_instance( self::class )->turn_off_basket_broadcast( $room_name );
			// Clear User and Master Records.
			$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
			$master     = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $my_session, null, true );
			$requestor  = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $user_requestor, null, true );
			$this->turn_off_basket_downloads( $room_name );
			// add_filter( 'myvideoroom_notification_popup', array( Factory::get_instance( NotificationHelpers::class ), 'render_accept_master_notification' ), 10, 2 );

		} else {
			return false;
		}

		// Return Success State.
		if ( $master && $requestor ) {
			return true;

		} else {

			return false;
		}

	}

	/**
	 * Decline Master Change Request
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function decline_master_change_request( string $room_name ): bool {

		// Request Master Change.
		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();

		// Notify Master if Current Queue request is from this user.
		$original_hash = $this->get_my_basket_request_state( $room_name, null, true );
		if ( ! $original_hash ) {
			return null;
		}
		$user_requestor = substr( $original_hash, strpos( $original_hash, ',' ) + 1 );

		// Update Own User to remove Pending Sync Flag.
		$master    = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $my_session, null, true );
		$requestor = Factory::get_instance( RoomSyncDAO::class )->change_basket_sync_state( $room_name, $user_requestor, null, true );

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

		$my_session           = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$current_master       = $this->get_master_status( $room_name );
		$is_master_active     = $this->is_master_active( $room_name );
		$am_i_downloading     = $this->am_i_downloading( $room_name );
		$am_i_master          = $this->am_i_master( $room_name );
		$masters              = Factory::get_instance( RoomSyncDAO::class )->get_room_masters( $room_name );
		$current_master_count = count( $masters );

		// Clean Invalid Hosts - Replace with this user if Host - or Most Recent Master if not.
		if ( $host_status && $current_master_count >= 2 ) {

			Factory::get_instance( RoomSyncDAO::class )->update_master( $my_session, $room_name );

		} elseif ( ! $host_status && $current_master_count >= 2 ) {

			Factory::get_instance( RoomSyncDAO::class )->update_master( $current_master, $room_name );
		}
		// Auto Take Ownership if Invalid Master.
		if ( ( ! $current_master || ! $is_master_active ) && true === $host_status ) {

			Factory::get_instance( RoomSyncDAO::class )->update_master( $my_session, $room_name );

			return true;
		}
		// Clean inconsistent basket sync state (masters cant download baskets).
		if ( $am_i_master && $am_i_downloading ) {
			$this->turn_off_basket_downloads( $room_name );
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
		$current_master   = $this->get_master_status( $room_name );
		$is_master_active = $this->is_master_active( $room_name );

		// Clear any errors if I am master.
		$my_hash = Factory::get_instance( RoomAdmin::class )->get_user_session();
		if ( $my_hash === $current_master ) {
			$this->accept_master_change_request( $room_name );
		}

		// If no current master, or master is inactive - set user as master and skip workflow.
		if ( ! $current_master || ! $is_master_active ) {
			$this->accept_master_change_request( $room_name, true );
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
	 * @param bool   $button_only -  Flag to return button only and not text.
	 * @return string
	 */
	public function master_button( $room_name, bool $button_only = null ): ?string {

		$id_text        = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$host_status    = $this->am_i_host( $room_name );
		$sync_is_on     = $this->am_i_broadcasting( $room_name );
		$master_status  = Factory::get_instance( self::class )->am_i_master( $room_name );
		$sync_requested = $this->get_my_basket_request_state( $room_name );

		if ( $sync_requested ) {
			return null;
		}

		if ( $sync_is_on && $master_status ) {
			if ( ! $button_only ) {
				$pre_text = '<p class ="mvr-notification-align"><strong>' . \esc_html__( 'Your Basket is Broadcasting', 'myvideoroom' ) . ' </strong></p> ';
			}

			$nonce        = wp_create_nonce( WooCommerce::SETTING_DISABLE_SYNC );
			$button_label = \esc_html__( 'Stop Sharing', 'myvideoroom' );
			$button_type  = WooCommerce::SETTING_DISABLE_SYNC;
			$target       = 'mvr-shopping-basket';
			$title_box    = 'title ="' . esc_html__( ' You are currently sharing your basket with the room', 'myvideoroom' ) . '"';
			return $pre_text . ' 
			<button ' . $title_box . '  class="mvr-form-button mvr-main-button-cancel myvideoroom-woocommerce-basket-ajax" data-target="' . $target . '">
			<a  data-target="' . $target . '" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a> </button>';
		}

		if ( $master_status ) {
			if ( ! $button_only ) {
				$pre_text = '<p class ="mvr-notification-align"><strong>' . \esc_html__( 'Room Basket Owner ', 'myvideoroom' ) . '</strong></p>';
			}
			$nonce        = wp_create_nonce( WooCommerce::SETTING_ENABLE_SYNC );
			$button_label = Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::BUTTON_SHARE_BASKET );
			$button_type  = WooCommerce::SETTING_ENABLE_SYNC;
			$target       = 'mvr-shopping-basket';
			$title_box    = 'title ="' . esc_html__( 'Your Basket can be shared with others in the room', 'myvideoroom' ) . '"';
			return $pre_text . '
			<button ' . $title_box . '  class="mvr-form-button mvr-main-button-enabled myvideoroom-woocommerce-basket-ajax" data-target="' . $target . '">
			<a  data-target="' . $target . '" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</button>';
		} elseif ( $host_status ) {
			$nonce        = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER );
			$button_label = Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::BUTTON_REQUEST_BASKET );
			$button_type  = WooCommerce::SETTING_REQUEST_MASTER;
			$target       = 'mvr-shopping-basket';
			$title_box    = 'title ="' . esc_html__( 'As a host, you can request control of the room basket from the current owner', 'myvideoroom' ) . '"';
			return '
			<button ' . $title_box . '  class="mvr-form-button mvr-main-button-enabled myvideoroom-woocommerce-basket-ajax" data-target="' . $target . '">
			<a  data-target="' . $target . '" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</button>
			';
		} else {
			return '';
		}
	}

	/**
	 * Sync Notification Button.
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $button_only -  Flag for only returning button and not description.
	 * @return string
	 */
	public function sync_notification_button( $room_name, bool $button_only = null ): ?string {

		$sync_status       = $this->get_my_basket_request_state( $room_name );
		$sync_is_available = $this->is_sync_available( $room_name );
		$am_i_master       = $this->am_i_master( $room_name );
		$am_i_downloading  = $this->am_i_downloading( $room_name );
		$target            = 'mvr-shopping-basket';

		switch ( $sync_status ) {
			case WooCommerce::SETTING_REQUEST_MASTER_PENDING:
				$id_text               = Factory::get_instance( RoomAdmin::class )->get_user_session();
				$withdraw_nonce        = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING );
				$withdraw_button_label = \esc_html__( 'Cancel Transfer Request', 'myvideoroom' );
				$withdraw_button_type  = WooCommerce::SETTING_REQUEST_MASTER_WITHDRAW_PENDING;
				$title_box             = 'title ="' . \esc_html__( 'You have Requested to Take Control of the Shared Basket', 'myvideoroom' ) . '"';
				return '
				<strong>' . \esc_html__( 'Basket Ownership Pending ', 'myvideoroom' ) . '</strong><button  ' . $title_box . '  class="mvr-form-button mvr-main-button-cancel myvideoroom-woocommerce-basket-ajax">
					<a  data-target="' . $target . '" data-input-type="' . $withdraw_button_type . '" data-auth-nonce="' . $withdraw_nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $withdraw_button_label . '</a>
					</button>';

			case WooCommerce::SETTING_REQUEST_MASTER:
				$id_text              = Factory::get_instance( RoomAdmin::class )->get_user_session();
				$accept_nonce         = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING );
				$accept_button_type   = WooCommerce::SETTING_REQUEST_MASTER_APPROVED_PENDING;
				$accept_button_label  = Factory::get_instance( SectionTemplates::class )->template_icon_switch( 'accept' ) . \esc_html__( 'Accept', 'myvideoroom' );
				$decline_nonce        = wp_create_nonce( WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING );
				$decline_button_label = Factory::get_instance( SectionTemplates::class )->template_icon_switch( 'reject' ) . \esc_html__( 'Decline', 'myvideoroom' );
				$decline_button_type  = WooCommerce::SETTING_REQUEST_MASTER_DECLINED_PENDING;
				$title_box            = 'title ="' . \esc_html__( 'A request to take control of the shared basket has been received', 'myvideoroom' ) . '"';
				return '<strong>' . \esc_html__( 'Basket Owner Change Request', 'myvideoroom' ) . '</strong>- <button ' . $title_box . '  class="mvr-form-button mvr-main-button-enabled myvideoroom-woocommerce-basket-ajax" data-target = "mvr-shopping-basket">
					<a  data-target="' . $target . '" data-input-type="' . $accept_button_type . '" data-auth-nonce="' . $accept_nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $accept_button_label . '</a>
					</button>
					<button  ' . $title_box . '  class="mvr-form-button mvr-main-button-cancel myvideoroom-woocommerce-basket-ajax" data-target = "mvr-shopping-basket">
					<a  data-target="' . $target . '" data-input-type="' . $decline_button_type . '" data-auth-nonce="' . $decline_nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $decline_button_label . '</a>
					</button>';
		}
		// Client Section.
		if ( $am_i_downloading ) {
			if ( ! $button_only ) {
				$pre_text = '<p class ="mvr-notification-align"><strong>' . \esc_html__( 'Basket Sync On ', 'myvideoroom' ) . '</strong></p> ';
			}
			$id_text               = Factory::get_instance( RoomAdmin::class )->get_user_session();
			$withdraw_nonce        = wp_create_nonce( WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD );
			$withdraw_button_label = \esc_html__( 'Stop Syncing Basket', 'myvideoroom' );
			$withdraw_button_type  = WooCommerce::SETTING_DISABLE_BASKET_DOWNLOAD;
			$title_box             = 'title ="' . \esc_html__( 'You are currently syncing your basket from the room automatically', 'myvideoroom' ) . '"';
			return '
			<button ' . $title_box . '  class="mvr-form-button mvr-main-button-stop mvr-main-button-cancel myvideoroom-woocommerce-basket-ajax" 
				<a  data-target="' . $target . '" data-input-type="' . $withdraw_button_type . '" data-auth-nonce="' . $withdraw_nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $withdraw_button_label . '</a>
				</button>';
		}

		if ( $sync_is_available && ! $am_i_master && ! $am_i_downloading ) {
			if ( ! $button_only ) {
				$pre_text = '<p class ="mvr-notification-align"><strong>' . \esc_html__( 'Room AutoSync Basket Available ', 'myvideoroom' ) . '</strong></p> ';
			}
			$id_text               = Factory::get_instance( RoomAdmin::class )->get_user_session();
			$withdraw_nonce        = wp_create_nonce( WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD );
			$withdraw_button_label = \esc_html__( 'Sync My Basket', 'myvideoroom' );
			$withdraw_button_type  = WooCommerce::SETTING_ENABLE_BASKET_DOWNLOAD;
			$title_box             = 'title ="' . \esc_html__( 'A group shared basket is available - would you like to synchronise your basket to the room ?', 'myvideoroom' ) . '"';
			return $pre_text . '
			<button ' . $title_box . '  class="mvr-main-button-enabled myvideoroom-button-separation myvideoroom-woocommerce-basket-ajax">
				<a  data-target="' . $target . '" data-input-type="' . $withdraw_button_type . '" data-auth-nonce="' . $withdraw_nonce . '" data-room-name="' . $room_name . '"data-record-id="' . $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $withdraw_button_label . '</a>
				</button>';
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

		$basket_state = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );

		// If No Basket Sync state (eg deleted table etc) repair.

		if ( ! $basket_state ) {
			Factory::get_instance( RoomSyncDAO::class )->update_basket_transfer_state( $room_name );
			$basket_state = Factory::get_instance( RoomSyncDAO::class )->get_by_id_sync_table( WooCommerce::SETTING_BASKET_REQUEST_USER, $room_name );
		}

		return $basket_state->get_basket_change();
	}

	/**
	 * Turn On Basket Download Engine.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function turn_on_basket_downloads( string $room_name ): bool {

		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$state      = Factory::get_instance( RoomSyncDAO::class )->update_basket_transfer_state( $room_name, $my_session, WooCommerce::SETTING_BASKET_REQUEST_ON );
		Factory::get_instance( SessionState::class )->notify_user( $room_name );
		if ( $state ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Turn Off Basket Download Engine.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function turn_off_basket_downloads( string $room_name ): bool {

		$my_session = Factory::get_instance( RoomAdmin::class )->get_user_session();
		$state      = Factory::get_instance( RoomSyncDAO::class )->update_basket_transfer_state( $room_name, $my_session, WooCommerce::SETTING_BASKET_REQUEST_OFF );
		Factory::get_instance( SessionState::class )->notify_user( $room_name );

		if ( $state ) {
			return true;
		} else {
			return false;
		}
	}

}
