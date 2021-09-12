<?php
/**
 * WooCommerce Product Category Function Helpers for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/WooCategory.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class WooCommerce Category Management
 * Handles all elements of rendering WooCommerce Category Related Archives
 */
class WooCategory {

	/**
	 * Check a WooCommerce Category Exists by Room Name.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return ?string
	 */
	public function does_category_exist( string $room_name ) {

		$object = get_term_by( 'slug', $room_name, 'product_cat' );
		if ( $object ) {
			return true;
		} else {
			return false;
		}

		return null;
	}

	/**
	 * Get Category ID by Room Name.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return ?int - the category room name.
	 */
	public function get_category_id_by_room_name( string $room_name ): ?int {

		$object = get_term_by( 'slug', $room_name, 'product_cat' );
		return $object->term_id;

	}

	/**
	 * Get Parent of Room.
	 *
	 * @return ?int - the category room name. or Null if no room parent category
	 */
	public function get_room_parent_category_id(): ?int {
		$room_name = WooCommerce::TABLE_NAME_WOOCOMMERCE_ROOM;
		$object    = get_term_by( 'slug', $room_name, 'product_cat' );
		if ( $object ) {
			return $object->term_id;
		} else {
			return null;
		}
	}



	/**
	 * Check a WooCommerce Category Exists by from Room Name.
	 *
	 * @param string $slug            -  Name of Room.
	 * @param string $category_name   -  Category Name.
	 * @param int    $parent_id       -  Category Parent ID.
	 * @return ?string
	 */
	public function create_product_category( string $slug, string $category_name, int $parent_id = null ) {

		$room_check = $this->does_category_exist( $slug );

		if ( $room_check ) {
			return null;
		}
		// Resolve if no Parent ID what the ID is.
		if ( $parent_id ) {
			$parent = $parent_id;
		} else {
			$parent_category = $this->get_room_parent_category_id();
			if ( $parent_category ) {
				$parent = $parent_category;
			} else {
				$parent = 0;
			}
		}

		$category_description = \esc_html__( 'MyVideoRoom - Room Store', 'myvideoroom' );
		$record               = wp_insert_term(
			$category_name,
			'product_cat',
			array(
				'description' => $category_description,
				'parent'      => $parent,
				'slug'        => $slug,
			)
		);

		$created_id = $record['term_id'];

		if ( $created_id ) {
			return $created_id;
		} else {
			return null;
		}
	}

	/**
	 * Create Categories for all Room Mappings.
	 *
	 * @return void
	 */
	public function activate_product_category(): void {

		$rooms_available = Factory::get_instance( MVRSiteVideoRoomHelpers::class )->get_rooms();
		$parent_id       = $this->create_product_category( WooCommerce::TABLE_NAME_WOOCOMMERCE_ROOM, WooCommerce::MAIN_CATEGORY_DISPLAY );
		foreach ( $rooms_available as $room ) {
			$this->create_product_category( $room->room_name, $room->display_name, $parent_id );

		}
	}


	/**
	 * Get Category Product Count. By Room Name or ID
	 *
	 * @param string $room_name         -  Category Name.
	 * @param int    $category_id       -  Category Parent ID.
	 * @return int
	 */
	public function get_category_count( string $room_name = null, int $category_id = null ): ?int {
		if ( $room_name ) {
			$category_id = $this->get_category_id_by_room_name( $room_name );
		}

		$term = get_term( $category_id, 'product_cat' ); // <--- tested in my system with this ID

		return $term->count;

	}

	/**
	 * Render Save Category Button
	 *
	 * @param string $button -    Button in pipeline.
	 * @param array  $item    -   Object of Room Info.
	 * @param string $room_name - Object of Room Info.
	 * @return ?string
	 */
	public function render_save_category_button( ?string $button = null, array $item, string $room_name ): string {

		$am_i_host = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );

		if ( $am_i_host ) {

			return $button .= '
			<a href=""
			class="mvr-icons myvideoroom-woocommerce-basket-ajax"
			data-product-id="' . esc_attr( $item['product_id'] ) . '"
			data-quantity="' . esc_attr( $item['quantity'] ) . '"
			data-variation-id="' . esc_attr( $item['variation_id'] ) . '"
			data-input-type="' . esc_attr( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY ) . '"
			data-room-name="' . esc_attr( $room_name ) . '"
			data-auth-nonce="' . esc_attr( wp_create_nonce( WooCommerce::SETTING_SAVE_PRODUCT_CATEGORY ) ) . '"
			title="' . esc_html__( 'Save this product to the room permanently (note: this adds it to the room category)', 'myvideoroom' ) . '"
			target="_blank"	><span class="myvideoroom-dashicons dashicons-cloud-upload"></span></a>';
		} else {
			return '';
		}
	}


}
