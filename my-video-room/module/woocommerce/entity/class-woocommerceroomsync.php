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
	 * @var string $timestamp - the Timestamp.
	 */
	private int $timestamp;

	/**
	 * Single Room Host Switch
	 *
	 * @var bool $room_host - the status whether user is a host.
	 */
	private bool $room_host;

	/**
	 * Single Basket Master Switch
	 *
	 * @var bool $single_product - the ID of the Variation.
	 */
	private bool $current_master;

	/**
	 * WooCommerce Room Sync Constructor.
	 *
	 * @param string $cart_id        The User ID.
	 * @param string $room_name      The Room Name.
	 * @param int    $timestamp      Last Updated Timestamp.
	 * @param bool   $room_host      User is a Room host.
	 * @param bool   $current_master User is Current Sync Basket Source.
	 * @param ?int   $id             The record id.
	 */
	public function __construct(
		string $cart_id,
		string $room_name,
		int $timestamp = null,
		bool $room_host = null,
		bool $current_master = null,
		?int $id
	) {
		$this->cart_id        = $cart_id;
		$this->room_name      = $room_name;
		$this->timestamp      = $timestamp;
		$this->room_host      = $room_host;
		$this->current_master = $current_master;
		$this->id             = $id;
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
				$data->room_host,
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
				'cart_id'        => $this->cart_id,
				'room_name'      => $this->room_name,
				'timestamp'      => $this->timestamp,
				'room_host'      => $this->room_host,
				'current_master' => $this->current_master,
				'id'             => $this->id,
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
	 * Gets Room Master Sync State.
	 *
	 * @return bool
	 */
	public function is_room_host(): bool {
		return $this->room_host;
	}

	/**
	 * Sets Room Host Sync State.
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
	 * Gets Single Product Sync State.
	 *
	 * @return bool
	 */
	public function is_current_master(): bool {
		return $this->current_master;
	}

	/**
	 * Sets Current Basket Master Sync State.
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
