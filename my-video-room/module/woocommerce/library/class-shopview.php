<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShopView.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;
use MyVideoRoomPlugin\DAO\RoomMap;

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

		$room           = Factory::get_instance( WooCategory::class )->activate_product_category();
		$category_check = Factory::get_instance( WooCategory::class )->does_category_exist( $room_name );

		if ( ! $category_check ) {
			return '';
		}
		$output = do_shortcode( '[products category=' . $room_name . ']' );

		$render = require __DIR__ . '/../views/shop-output.php';
		return $render( $output, $room_name );
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
			target="_blank"	><span class="dashicons dashicons-cloud-upload"></span></a>';
		} else {
			return '';
		}
	}

	/**
	 * Render Save Category Button
	 *
	 * @param string $product_id -   Object of Room Info.
	 * @param string $room_name - Object of Room Info.
	 * @return bool
	 */
	public function add_category_to_product( string $product_id, string $room_name ): bool {

		$term_to_add = Factory::get_instance( WooCategory::class )->get_category_id_by_room_name( $room_name );
		if ( ! $term_to_add ) {
			$room_id     = Factory::get_instance( RoomApp::class )->get_post_id_by_room_name( $room_name );
			$room_object = Factory::get_instance( RoomApp::class )->get_room_info( $room_id );
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

}
