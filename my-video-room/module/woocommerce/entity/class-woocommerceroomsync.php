<?php
/**
 * WooCoomerce Video Sync Basket System -Data Object.
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceRoomSync.php
 */

namespace MyVideoRoomPlugin\Module\WooCommerce\Entity;

/**
 * Class WooCommerceVideo - Handler for Cart Sync.
 */
class WooCommerceRoomSync {

	/**
	 * The record id
	 *
	 * @var ?int
	 */
	private ?int $id;

	/**
	 * Cart_id - identifies the user session in cart.
	 *
	 * @var string
	 */
	private string $cart_id;

	/**
	 * Room_name
	 *
	 * @var string
	 */
	private string $room_name;

	/**
	 * Timestamp
	 *
	 * @var int $timestamp - the Timestamp.
	 */
	private int $timestamp;

	/**
	 * Last Notification Timestamp
	 *
	 * @var int $last_notification - the  Last Notification Timestamp.
	 */
	private ?int $last_notification;

	/**
	 * Single Room Host Switch
	 *
	 * @var bool $room_host - the status whether user is a host.
	 */
	private bool $room_host;

	/**
	 * Basket Change of Ownership Status
	 *
	 * @var ?string $basket_change
	 */
	private ?string $basket_change;

	/**
	 * Basket State of Sync
	 *
	 * @var ?string $sync_state
	 */
	private ?string $sync_state;

	/**
	 * Single Basket Master Switch
	 *
	 * @var bool $single_product - the ID of the Variation.
	 */
	private bool $current_master;


	/**
	 * WooCommerce Room Sync Constructor.
	 *
	 * @param  string  $cart_id                The User ID.
	 * @param  string  $room_name              The Room Name.
	 * @param  int     $timestamp              Last Updated Timestamp.
	 * @param  int     $last_notification      Last Received Update.
	 * @param  bool    $room_host              User is a Room host.
	 * @param ?string $basket_change           Basket Change of Ownership Status.
	 * @param ?string $sync_state              State of Automatic Basket Sync.
	 * @param  bool    $current_master         User is Current Sync Basket Source.
	 * @param  ?int    $id                     The record id.
	 */
	public function __construct(
		string $cart_id,
		string $room_name,
		?int $timestamp = null,
		int $last_notification = null,
		bool $room_host = null,
		string $basket_change = null,
		string $sync_state = null,
		bool $current_master = null,
		?int $id
	) {
		$this->cart_id           = $cart_id;
		$this->room_name         = $room_name;
		$this->timestamp         = $timestamp;
		$this->last_notification = $last_notification;
		$this->room_host         = $room_host;
		$this->basket_change     = $basket_change;
		$this->sync_state        = $sync_state;
		$this->current_master    = $current_master;
		$this->id                = $id;
	}

	/**
	 * Create from a JSON object
	 *
	 * @param string $json The JSON representation of the object.
	 *
	 * @return ?\MyVideoRoomPlugin\Module\WooCommerce\Entity\SecurityVideoPreference
	 */
	public static function from_json( string $json ): ?self {
		$data = json_decode( $json );

		if ( $data ) {
			return new self(
				$data->cart_id,
				$data->room_name,
				$data->timestamp,
				$data->last_notification,
				$data->room_host,
				$data->basket_change,
				$data->sync_state,
				$data->current_master,
				$data->id,
			);
		}

		return null;
	}

	/**
	 * Convert to JSON
	 * Used for caching.
	 *
	 * @return string
	 */
	public function to_json(): string {
		return wp_json_encode(
			array(
				'cart_id'           => $this->cart_id,
				'room_name'         => $this->room_name,
				'timestamp'         => $this->timestamp,
				'last_notification' => $this->last_notification,
				'room_host'         => $this->room_host,
				'basket_change'     => $this->basket_change,
				'sync_state'        => $this->sync_state,
				'current_master'    => $this->current_master,
				'id'                => $this->id,
			)
		);
	}

	/**
	 * Get the record id
	 *
	 * @return ?int
	 */
	public function get_id(): ?int {
		return $this->id;
	}

	/**
	 * Set the record id
	 *
	 * @param int $id - userid.
	 *
	 * @return $this
	 */
	public function set_id( int $id ): self {
		$this->id = $id;

		return $this;
	}

	/**
	 * Gets Cart ID.
	 *
	 * @return string
	 */
	public function get_cart_id(): string {
		return $this->cart_id;
	}

	/**
	 * Set the Cart ID
	 *
	 * @param string $cart_id The new Cart id.
	 *
	 * @return $this
	 */
	public function set_cart_id( string $cart_id ): self {
		$this->cart_id = $cart_id;

		return $this;
	}

	/**
	 * Gets Room Name.
	 *
	 * @return string
	 */
	public function get_room_name(): string {
		return $this->room_name;
	}

	/**
	 * Set the Room Name
	 *
	 * @param string $room_name - the Room name.
	 *
	 * @return $this
	 */
	public function set_room_name( string $room_name ): self {
		$this->room_name = $room_name;

		return $this;
	}

	/**
	 * Gets Timestamp.
	 *
	 * @return int
	 */
	public function get_timestamp(): int {
		return $this->timestamp;
	}

	/**
	 * Sets Timestamp.
	 *
	 * @param int $timestamp - sets the Single Product Sync state.
	 *
	 * @return WooCommerceRoomSync
	 */
	public function set_timestamp( int $timestamp ): self {
		$this->timestamp = $timestamp;

		return $this;
	}

	/**
	 * Gets Last Notification Timestamp.
	 *
	 * @return int
	 */
	public function get_last_notification(): ?int {
		return $this->last_notification;
	}

	/**
	 * Sets Last Notification Timestamp.
	 *
	 * @param int $last_notification - Last Notification Timestamp.
	 *
	 * @return WooCommerceRoomSync
	 */
	public function set_last_notification( int $last_notification ): self {
		$this->last_notification = $last_notification;

		return $this;
	}

	/**
	 *  Checks Room Host State.
	 *
	 * @return bool
	 */
	public function is_room_host(): bool {
		return $this->room_host;
	}

	/**
	 * Sets Room Host State.
	 *
	 * @param bool $room_host - sets the Single Product Sync state.
	 *
	 * @return WooCommerceRoomSync
	 */
	public function set_room_host( bool $room_host ): WooCommerceRoomSync {
		$this->room_host = $room_host;

		return $this;
	}

	/**
	 * Sets Basket Sync State.
	 *
	 * @param string|null $basket_change - Basket Change of Ownership Status.
	 *
	 * @return UserVideoPreference
	 */
	public function set_basket_change_setting( string $basket_change = null ): WooCommerceRoomSync {
		$this->basket_change = $basket_change;

		return $this;
	}

	/**
	 * Gets Basket Change State.
	 *
	 * @return ?string
	 */
	public function get_basket_change(): ?string {
		return $this->basket_change;
	}

	/**
	 * Sets Basket Sync State.
	 *
	 * @param string|null $sync_state - Basket Change of Ownership Status.
	 *
	 * @return UserVideoPreference
	 */
	public function set_sync_state( string $sync_state = null ): WooCommerceRoomSync {
		$this->sync_state = $sync_state;

		return $this;
	}

	/**
	 * Gets Basket Change State.
	 *
	 * @return ?string
	 */
	public function get_sync_state(): ?string {
		return $this->sync_state;
	}

	/**
	 * Gets Current Master Basket State.
	 *
	 * @return bool
	 */
	public function is_current_master(): bool {
		return $this->current_master;
	}

	/**
	 * Sets Current Master Basket State.
	 *
	 * @param bool $current_master - sets the Single Product Sync state.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_current_master( bool $current_master ): WooCommerceRoomSync {
		$this->current_master = $current_master;

		return $this;
	}

}
