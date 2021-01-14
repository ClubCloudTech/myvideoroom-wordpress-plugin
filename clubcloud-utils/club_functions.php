<?php

require_once( 'functions/cc_connect.php' );
require_once( 'functions/cc_page_switches.php' );
require_once( 'functions/cc_filters_utilities.php' );
require_once( 'functions/cc_video_controllers.php' );
require_once( 'functions/cc_wchelpers.php' );
require_once( 'functions/cc_shortcode_constructor.php' );
require_once( 'functions/cc_menu_helpers.php' );



/*Useful Snippet Archive
//WCFM List Products by Vendor and List Bookings by Vendor
		$products_list  = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( $user_id, apply_filters( 'wcfm_limit_check_status', 'any' ), array( 'suppress_filters' => 1 ) );
		$vendor_bookings = apply_filters( 'wcfm_wcb_include_bookings', '' );
//WCFM Store Manipulation
$item = $orderinfo->get_items();			$product_name = $item->get_name();			$product_id = $item->get_product_id();		$vendor_id= $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id);
	$store_user      = wcfmmp_get_store( $vendor_id );			$store_info      = $store_user->get_shop_info();			$store_name      = $store_info['store_slug'];
// Woocomm - Get Booking IDs from Orders
	$orderdetail= WC_Booking_Data_Store::get_booking_ids_from_order_id( $order );
	$infoposta=  get_wc_booking($bookingid); - get booking detail
global $wp_filter;
		echo '<pre>';
		var_dump( $wp_filter['wp_logout'] );
		echo '</pre>';
this is to dump the filters attached to a given hook - in this case the wp_logout one.




*/

?>
