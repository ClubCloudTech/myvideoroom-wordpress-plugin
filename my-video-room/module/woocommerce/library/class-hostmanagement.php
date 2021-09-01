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
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceRoomSync as WooCommerceRoomSyncEntity;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceVideo;
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
	public function get_master_status( $room_name ):?string {

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
	 * Initialise - Check AND Set Room Master
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status - If User is Host.
	 * @return ?string
	 */
	public function initialise_master_status( $room_name, $host_status = null ): bool {

		$my_session           = session_id();
		$current_master       = $this->get_master_status( $room_name );
		$masters              = Factory::get_instance( WooCommerceRoomSyncDAO::class )->get_room_masters( $room_name );
		$current_master_count = count( $masters );

		// Clean Invalid Hosts - Replace with this user if Host - or Most Recent Master if not.
		if ( $host_status && $current_master_count >= 2 ) {
			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_master( $my_session, $room_name );
		} elseif ( ! $host_status && $current_master_count >= 2 ) {
			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_master( $current_master, $room_name );
		}
		// Now Return Status.
		if ( $my_session === $current_master ) {
			return true;
		} elseif ( ! $current_master && true === $host_status ) {
			Factory::get_instance( WooCommerceRoomSyncDAO::class )->update_master( $my_session, $room_name );
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Initialise - Return if current session is master
	 *
	 * @param string $room_name -  Name of Room.
	 * @return bool
	 */
	public function am_i_master( $room_name ): bool {

		$my_session     = session_id();
		$current_master = $this->get_master_status( $room_name );

		if ( $my_session === $current_master ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Master Notification Button.
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $host_status -  Whether user is host.
	 * @return string
	 */
	public function master_button( $room_name, bool $host_status ): ?string {

		$id_text       = session_id();
		$master_status = Factory::get_instance( self::class )->am_i_master( $room_name );
		$nonce         = wp_create_nonce( WooCommerce::SETTING_ENABLE_MASTER );

		if ( $master_status ) {
			$button_label = \esc_html__( 'Share Basket', 'myvideoroom' );
			$button_type  = WooCommerce::SETTING_ENABLE_MASTER;
			return '
			<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
			<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</div>
			';
		} elseif ( $host_status ) {
			$button_label = \esc_html__( 'Request Shared Basket Control', 'myvideoroom' );
			$button_type  = WooCommerce::SETTING_DISABLE_MASTER;
			return '
			<div aria-label="button" class="mvr-form-button myvideoroom-woocommerce-basket-ajax">
			<a href="" data-input-type="' . $button_type . '" data-auth-nonce="' . $nonce . '" data-room-name="' . $room_name . '"data-record-id="' .  $id_text . '" class="myvideoroom-woocommerce-basket-ajax myvideoroom-button-link">' . $button_label . '</a>
			</div>
			';
		} else {
			return '';
		}
	}

}
