<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShopView.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;

/**
 * Class ShopView Basket
 * Handles all elements of rendering WooCommerce Category Related Archives.
 */
class ShopView {

	/**
	 * Render Shopfront
	 *
	 * @param string $room_name -  Name of Room.
	 * @return ?string
	 */
	public function show_shop( string $room_name = null ): ?string {

		// Initialize and Autorepair categories.
		Factory::get_instance( WooCategory::class )->activate_product_category();
		\wp_enqueue_script( 'myvideoroom-woocommerce-basket-js' );
		\wp_enqueue_script( 'myvideoroom-woocommerce-carthandler' );
		\wp_enqueue_script( 'myvideoroom-admin-tabs' );
		// TODO need to update FRED
		$category_check = Factory::get_instance( WooCategory::class )->does_category_exist( $room_name );

		$host_status     = Factory::get_instance( HostManagement::class )->am_i_host( $room_name );
		$output          = do_shortcode( '[products category=' . $room_name . ']' );
		$last_basket     = Factory::get_instance( ShoppingBasket::class )->render_sync_queue_table( $room_name, null, true );
		$last_storecount = Factory::get_instance( WooCategory::class )->get_category_count( $room_name );
		$room_admin_tab  = $this->render_room_management_table( $room_name );
		$render          = require __DIR__ . '/../views/shop-output.php';

		return $render( $output, $last_basket, $last_storecount, $host_status, $room_admin_tab );
	}


	/**
	 * Add a Category to a Product
	 *
	 * @param string $product_id -   Object of Room Info.
	 * @param string $room_name - Object of Room Info.
	 * @return bool
	 */
	public function add_category_to_product( string $product_id, string $room_name ): bool {

		$term_to_add = Factory::get_instance( WooCategory::class )->get_category_id_by_room_name( $room_name );
		if ( ! $term_to_add ) {
			$room_id = Factory::get_instance( RoomApp::class )->get_post_id_by_room_name( $room_name );
			$room    = Factory::get_instance( RoomApp::class )->get_room_info( $room_id );
			Factory::get_instance( WooCategory::class )->create_product_category( $room_name, $room->display_name );
			$term_to_add = Factory::get_instance( WooCategory::class )->get_category_id_by_room_name( $room_name );
		}

		$term_ids = array();
		$terms    = wp_get_object_terms( intval( $product_id ), 'product_cat' );
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $item ) {
				$term_ids[] = $item->term_id;
			}
		}

		$term_ids = array();
		$terms    = wp_get_object_terms( $product_id, 'product_cat' );
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $item ) {
				$term_ids[] = $item->term_id;
			}
		}
		$term_ids[] = $term_to_add;
		wp_set_object_terms( $product_id, $term_ids, 'product_cat' );
		return true;
	}

	/**
	 * Delete Category from Product
	 *
	 * @param string $product_id - Object of Room Info.
	 * @param string $room_name - Object of Room Info.
	 * @return bool
	 */
	public function delete_category_from_product( string $product_id, string $room_name ): bool {

		$term_to_remove = Factory::get_instance( WooCategory::class )->get_category_id_by_room_name( $room_name );
		if ( ! $term_to_remove ) {
			return false;
		}

		$term_ids = array();
		$terms    = wp_get_object_terms( intval( $product_id ), 'product_cat' );
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $item ) {
				$term_ids[] = $item->term_id;
			}
		} else {
			return false;
		}

		$term_ids = array();
		$terms    = wp_get_object_terms( $product_id, 'product_cat' );
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $item ) {
				if ( $term_to_remove !== $item->term_id ) {
					$term_ids[] = $item->term_id;
				}
			}
		}

		wp_set_object_terms( $product_id, $term_ids, 'product_cat' );
		return true;
	}

	/**
	 * Has Shop Size Changed?
	 *
	 * @param string $room_name - Object of Room Info.
	 * @param string $last_storecount - The last count store had.
	 * @return bool
	 */
	public function has_room_store_changed( string $room_name, string $last_storecount ): bool {
		$last_storecount    = intval( $last_storecount );
		$current_storecount = Factory::get_instance( WooCategory::class )->get_category_count( $room_name );
		if ( $last_storecount === $current_storecount ) {
			return false;

		} else {
			return true;
		}
	}

	/**
	 * Render Room Management Table.
	 * Prepare Queue Table and Render the view, or return just Queue object to other functions.
	 *
	 * @param string $room_name -  Name of Room.
	 * @param bool   $object_only -  Flag to return only the object and not render the table.
	 *
	 * @return string|Array depending on return flag
	 */
	public function render_room_management_table( string $room_name, bool $object_only = null ) {
		if ( ! $room_name ) {
			return null;
		}

		$available_queue = Factory::get_instance( WooCategory::class )->get_product_ids_by_category( $room_name );
		$output_array    = array();

		foreach ( $available_queue as $record_id ) {
			$basket_array = Factory::get_instance( ShoppingBasket::class )->get_individual_cart_object( $record_id, $room_name, true );
			array_push( $output_array, $basket_array );
		}

			// Return Object.
		if ( $object_only ) {
			return $output_array;
		} else {
			// Render View.
			$render = require __DIR__ . '/../views/room-manage-output.php';
			return $render( $output_array, $room_name, $last_basket );
		}
	}
}
