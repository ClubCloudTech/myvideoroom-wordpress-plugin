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
	private bool $timestamp;

	/**
	 * WooCommerce Video constructor.
	 *
	 * @param string $cart_id        The User ID.
	 * @param string $room_name      The Room Name.
	 * @param string $timestamp      Last Updated Timestamp.
	 * @param ?int   $id             The record id.
	 */
	public function __construct(
		string $cart_id,
		string $room_name,
		string $timestamp = null,
		?int $id
	) {
		$this->cart_id   = $cart_id;
		$this->room_name = $room_name;
		$this->timestamp = $timestamp;
		$this->id        = $id;
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
				'cart_id'   => $this->cart_id,
				'room_name' => $this->room_name,
				'timestamp' => $this->timestamp,
				'id'        => $this->id,
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
	 * @return int
	 */
	public function get_cart_id(): int {
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
	 * @return bool
	 */
	public function get_timestamp(): bool {
		return $this->timestamp;
	}

	/**
	 * Sets Timestamp.
	 *
	 * @param bool $timestamp - sets the Single Product Sync state.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_timestamp( bool $timestamp ): self {
		$this->timestamp = $timestamp;

		return $this;
	}
}
