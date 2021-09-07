<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShopView.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

/**
 * Class ShopView Basket
 * Handles all elements of rendering WooCommerce Category Related Archives
 */
class ShopView {

	/**
	 * Render Confirmation Pages
	 *
	 * @param string $room_name -  Name of Room.
	 * @return void
	 */
	public function show_shop( string $room_name = null ): void {

		$this->does_category_exist( $room_name );

		$args     = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => '12',
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => 26,
					'operator' => 'IN',
				),
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => 'exclude-from-catalog', // Possibly 'exclude-from-search' too
					'operator' => 'NOT IN',
				),
			),
		);
		$products = new \WP_Query( $args );

	}

	/**
	 * Check a WooCommerce Category Exists by from Room Name.
	 *
	 * @param string $room_name -  Name of Room.
	 * @return ?string
	 */
	public function does_category_exist( string $room_name ) {

		$object = get_term_by( 'slug', $room_name, 'product_cat' );

		if ( $object ) {
			return $object;
		} else {
			return false;
		}

		return null;
	}

	/**
	 * Check a WooCommerce Category Exists by from Room Name.
	 *
	 * @param string $slug            -  Name of Room.
	 * @param string $category_name   -  Category Name.
	 * @return ?string
	 */
	public function create_product_category( string $slug, string $category_name ) {

		$room_check = $this->does_category_exist( $slug );

		if ( $room_check ) {
			return null;
		}

		$category_description = \esc_html__( 'MyVideoRoom - Room Store', 'myvideoroom' );
		$id                   = wp_insert_term(
			$category_name,
			'product_cat',
			array(
				'description' => $category_description,
				'parent'      => 0,
				'slug'        => $slug,
			)
		);
		if ( $id ) {
			return $id;
		} else {
			return null;
		}
	}
}
