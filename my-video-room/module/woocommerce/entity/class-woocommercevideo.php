<?php
/**
 * WooCoomerce Video Sync Basket System -Data Object.
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceVideo.php
 */

namespace MyVideoRoomPlugin\Module\WooCommerce\Entity;

/**
 * Class WooCommerceVideo - Handler for Cart Sync.
 */
class WooCommerceVideo {

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
	 * Source Cart_id - identifies the Source Sync session.
	 *
	 * @var string
	 */
	private string $source_cart_id;

	/**
	 * Room_name
	 *
	 * @var string
	 */
	private string $room_name;

	/**
	 * Cart_data
	 *
	 * @var ?string
	 */
	private ?string $cart_data;

	/**
	 * Product ID
	 *
	 * @var string product_id - the Product ID.
	 */
	private string $product_id;

	/**
	 * Quantity
	 *
	 * @var string Quantity - the Product Quantity.
	 */
	private string $quantity;

	/**
	 * Variation ID
	 *
	 * @var string $variation_id - the ID of the Variation.
	 */
	private int $variation_id;

	/**
	 * Single Product Sync Switch
	 *
	 * @var bool $single_product - the ID of the Variation.
	 */
	private bool $single_product;

	/**
	 * Timestamp
	 *
	 * @var int $timestamp - the Timestamp.
	 */
	private int $timestamp;

	/**
	 * WooCommerce Video constructor.
	 *
	 * @param string $cart_id        The User ID.
	 * @param string $source_cart_id The Cart ID of the originating sync.
	 * @param string $room_name      The Room Name.
	 * @param string $cart_data      Data Object in Cart.
	 * @param string $product_id     Data Object in Cart.
	 * @param string $quantity       Data Object in Cart.
	 * @param string $variation_id   Data Object in Cart.
	 * @param bool   $single_product Product Single Sync Flag.
	 * @param int    $timestamp      Last Updated Timestamp.
	 * @param ?int   $id             The record id.
	 */
	public function __construct(
		string $cart_id,
		string $source_cart_id,
		string $room_name,
		string $cart_data = null,
		string $product_id = null,
		string $quantity = null,
		string $variation_id = null,
		bool $single_product = null,
		int $timestamp = null,
		?int $id
	) {
		$this->cart_id        = $cart_id;
		$this->source_cart_id = $source_cart_id;
		$this->room_name      = $room_name;
		$this->cart_data      = $cart_data;
		$this->product_id     = $product_id;
		$this->quantity       = $quantity;
		$this->variation_id   = $variation_id;
		$this->single_product = $single_product;
		$this->timestamp      = $timestamp;
		$this->id             = $id;
	}

	/**
	 * Create from a JSON object
	 *
	 * @param string $json The JSON representation of the object.
	 *
	 * @return ?\MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference
	 */
	public static function from_json( string $json ): ?self {
		$data = json_decode( $json );

		if ( $data ) {
			return new self(
				$data->cart_id,
				$data->source_cart_id,
				$data->room_name,
				$data->cart_data,
				$data->product_id,
				$data->quantity,
				$data->variation_id,
				$data->single_product,
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
				'cart_id'        => $this->cart_id,
				'source_cart_id' => $this->source_cart_id,
				'room_name'      => $this->room_name,
				'cart_data'      => $this->cart_data,
				'product_id'     => $this->product_id,
				'quantity'       => $this->quantity,
				'variation_id'   => $this->variation_id,
				'single_product' => $this->single_product,
				'timestamp'      => $this->timestamp,
				'id'             => $this->id,
			)
		);
	}

	/**
	 * Get the record id
	 *
	 * @return ?int
	 */
	public function get_record_id(): ?int {
		return $this->id;
	}

	/**
	 * Set the record id
	 *
	 * @param int $id - userid.
	 *
	 * @return $this
	 */
	public function set_record_id( int $id ): self {
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
	 * Gets Source Cart ID.
	 *
	 * @return string
	 */
	public function get_source_cart_id(): string {
		return $this->source_cart_id;
	}

	/**
	 * Set the Source Cart ID
	 *
	 * @param string $source_cart_id The Cart id of the source cart.
	 *
	 * @return $this
	 */
	public function set_source_cart_id( string $source_cart_id ): self {
		$this->source_cart_id = $source_cart_id;

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
	 * Gets Cart Data.
	 *
	 * @return ?string
	 */
	public function get_cart_data(): ?string {
		return $this->cart_data;
	}

	/**
	 * Sets Cart Data.
	 *
	 * @param string|null $cart_data - data to store from Cart.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_cart_data( string $cart_data = null ): WooCommerceVideo {
		$this->cart_data = $cart_data;

		return $this;
	}

	/**
	 * Gets Product ID.
	 *
	 * @return array
	 */
	public function get_product_id(): string {
		return $this->product_id;
	}

	/**
	 * Sets Product ID.
	 *
	 * @param string|null $product_id - ProductID.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_product_id( string $product_id = null ): WooCommerceVideo {
		$this->product_id = $product_id;

		return $this;
	}

	/**
	 * Gets Quantity.
	 *
	 * @return array
	 */
	public function get_quantity(): string {
		return $this->quantity;
	}

	/**
	 * Sets Quantity.
	 *
	 * @param string|null $quantity - Product Quantity.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_quantity( string $quantity = null ): WooCommerceVideo {
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * Gets Variation ID.
	 *
	 * @return array
	 */
	public function get_variation_id(): string {
		return $this->variation_id;
	}

	/**
	 * Sets Variation ID.
	 *
	 * @param string|null $variation_id - Variation ID.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_variation_id( string $variation_id = null ): WooCommerceVideo {
		$this->variation_id = $variation_id;

		return $this;
	}

	/**
	 * Gets Single Product Sync State.
	 *
	 * @return bool
	 */
	public function is_single_product(): bool {
		return $this->single_product;
	}

	/**
	 * Sets Single Product Sync State.
	 *
	 * @param bool $single_product - sets the Single Product Sync state.
	 *
	 * @return WooCommerceVideo
	 */
	public function set_single_product( bool $single_product ): WooCommerceVideo {
		$this->single_product = $single_product;

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
	 * @return int
	 */
	public function set_timestamp( int $timestamp ): WooCommerceVideo {
		$this->timestamp = $timestamp;

		return $this;
	}
}
