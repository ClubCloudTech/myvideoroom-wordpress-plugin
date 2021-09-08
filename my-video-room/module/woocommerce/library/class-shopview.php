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
 * Handles all elements of rendering WooCommerce Category Related Archives
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

		$category_id = Factory::get_instance( WooCategory::class )->get_category_id_by_room_name( $room_name );

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => '12',
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_id,
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
		/*
		$loop = new \WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				\wc_get_template_part( 'content', 'product' );
			endwhile;
		} else {
			echo __( 'No products found' );
		}
		\wp_reset_postdata();
		return '';*/

		if ( ! function_exists( 'wc_get_products' ) ) {
			return '';
		}

		  $paged               = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
		  $ordering            = WC()->query->get_catalog_ordering_args();
		  $ordering['orderby'] = array_shift( explode( ' ', $ordering['orderby'] ) );
		  $ordering['orderby'] = stristr( $ordering['orderby'], 'price' ) ? 'meta_value_num' : $ordering['orderby'];
		  $products_per_page   = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );

		$featured_products = wc_get_products(
			array(
				'meta_key' => '_price',
				'status'   => 'publish',
				'limit'    => $products_per_page,
				'page'     => $paged,
				'featured' => true,
				'paginate' => true,
				'return'   => 'ids',
				'orderby'  => $ordering['orderby'],
				'order'    => $ordering['order'],
			)
		);

		wc_set_loop_prop( 'current_page', $paged );
		wc_set_loop_prop( 'is_paginated', wc_string_to_bool( true ) );
		wc_set_loop_prop( 'page_template', get_page_template_slug() );
		wc_set_loop_prop( 'per_page', $products_per_page );
		wc_set_loop_prop( 'total', $featured_products->total );
		wc_set_loop_prop( 'total_pages', $featured_products->max_num_pages );

		if ( $featured_products ) {
			do_action( 'woocommerce_before_shop_loop' );
			woocommerce_product_loop_start();
			foreach ( $featured_products->products as $featured_product ) {
				$post_object = get_post( $featured_product );
				setup_postdata( $GLOBALS['post'] =& $post_object );
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			woocommerce_product_loop_end();
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			do_action( 'woocommerce_no_products_found' );
		}
		return '';
	}
}
