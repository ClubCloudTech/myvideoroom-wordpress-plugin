<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/*ClubCloud - a Function to return Order Information by Signed in User///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes userID
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_orders_by_user( $userid ) {
	$order_statuses = array( 'wc-on-hold', 'wc-processing', 'wc-completed' );
	$array_holder   = array();
	$customer       = $userid;
	// Get all customer orders
	$customer_orders = get_posts( array(
		'numberposts' => - 1,
		'meta_key'    => '_customer_user',
		'orderby'     => 'date',
		'order'       => 'DESC',
		'meta_value'  => $customer,
		'post_type'   => wc_get_order_types(),
		'post_status' => array_keys( wc_get_order_statuses() ),
		'post_status' => array( 'wc-processing', 'wc-completed' ),
	) );
	$Order_Array     = []; //
	foreach ( $customer_orders as $customer_order ) {
		$orderq        = wc_get_order( $customer_order );
		$bookingscheck = WC_Booking_Data_Store::get_booking_ids_from_order_id( $customer_order->ID );
		$array_count   = count( $bookingscheck ) . "Bookings Count<br>";
		if ( $array_count >= 1 ) {
			$Order_Array[] = $orderq->get_id();
		}
	}

	//return $array_holder;
	return $Order_Array;
}

//ClubCloud A Function to return Staff Store Parent Name//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_getmystore() {
	$user       = wp_get_current_user();
	$roles      = ( array ) $user->roles;
	$parentID   = $user->_wcfm_vendor;
	$store_user = wcfmmp_get_store( $parentID );
	$store_info = $store_user->get_shop_info();

	return $store_info['store_name'];
}

add_shortcode( 'ccgetmystore', 'cc_getmystore' );
// ClubCloud - A Shortcode to extract the Current Store name and format it correctly///////////////////////////////////////////////////////////////////////////////////////////////////
// Used by Merchant Pages to generate Hyper-links for Video  - the WCFM shortcode doesnt format the Store Name Correctly
//May be deprecated by Use of CCname
function cc_getstore() {
	$post = get_post();
	global $WCFM, $WCFMmp;
	$store_id   = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$store_user = wcfmmp_get_store( $store_id );
	$store_info = $store_user->get_shop_info();
	$store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'wc-multivendor-marketplace' );
	$store_name = apply_filters( 'wcfmmp_store_title', $store_name, $store_id );

	return $store_name;
}

add_shortcode( 'ccstore', 'cc_getstore' );
//ClubCloud a Function to Get Slug from a Store////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_getslug( $atts = array() ) {
	extract( shortcode_atts( array(
		'type' => $type = 'store'
	), $atts ) );
	if ($type=="login"){
		$user  = wp_get_current_user();
		return $user->user_login;

	}
	$post = get_post();
	global $WCFM, $WCFMmp;
//get vendor from store
	$store_id   = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$store_user = wcfmmp_get_store( $store_id );
	$store_info = $store_user->get_shop_info();
	$store_data = $store_info['store_slug'];
	if($type=="user"){
		$user   = wp_get_current_user(); 
		$slug 	= $user->user_nicename;
		return $slug;
	}

	return $store_data;
}

add_shortcode( 'ccslug', 'cc_getslug' );

/*ClubCloud - a Function to return Product Information and Vendor Info from Booking Numbers///////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes order number
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_orderinfo_by_booking( $bookingid, $fieldoption, $vendorid ) {
	global $WCFM, $WCFMmp;  // set up default parameters
	if ( $bookingid == "" and $vendorid == "" ) {
		return "Blank ID";
	}  //reject blank booking and vendor numbers
	$bookval = cc_validate_booking( $bookingid );
	if ( $bookingid != "" and $bookval == false ) {
		return "Invalid or Deleted Booking";
	} //reject invalid booking numbers if entered
	$booking   = get_wc_booking( $bookingid );
	$productid = $booking->product_id;
	if ( $vendorid != "" ) {
	} else {
		$vendorid = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $productid );
	}
	$store_user = wcfmmp_get_store( $vendorid );
	$store_info = $store_user->get_shop_info();
	$store_slug = $store_info['store_slug'];
	$store_name = $store_info['store_name'];
	if ( $fieldoption == "store_slug" ) {
		return $store_slug;
	} else if ( $fieldoption == "store_name" ) {
		return $store_name;
	} else if ( $fieldoption == "vendorid" ) {
		return $vendorid;
	}
}

/*ClubCloud - a Function to return Order Information by Signed in User///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes userID, and time offset to generate correct timeframes
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_bookings_by_order( $order, $time_offset, $showpast ) {
	global $bp, $WCFM, $WCFMmp;
	$current_time = current_time( 'timestamp' );
	$orderdetail  = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order );
	if ( $showpast == "" ) {
		$showpast = "true";
	}
	$time_offsetdisplay = $time_offset / 60;
	$outnobookarray     = array();  // Set up counters and prepare arrays
	$outbookarray       = array();
	$futurecount        = 0;
	$invalidcount       = 0;
	$orderwindowcount   = 0;
	foreach ( $orderdetail as $booking_id ) { //implement time filtration and reject past - or early meetings
		$booking         = get_wc_booking( $booking_id );
		$start_date      = $booking->get_start_date();
		$end_date        = $booking->get_end_date();
		$end_timestamp   = strtotime( $end_date );
		$start_timestamp = strtotime( $start_date );
		$current15_time  = $current_time + $time_offset;
		//Get Store Information for Friendly Display
		$infoposta  = get_wc_booking( $booking_id );
		$infopostb  = $infoposta->product_id;
		$vendorid   = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $infopostb );
		$store_user = wcfmmp_get_store( $vendorid );
		$store_info = $store_user->get_shop_info();
		$store_name = $store_info['store_name'];

		if ( $current_time >= $end_timestamp and $showpast == "true" )//only for debug return $booking_id. " booking id and ".$start_date."<br>";
		{
			$orderpast = '<div style="font-size:1.50em;color:black">Booking ' . $booking_id . ' occurs in the past and can no longer be accessed. <br></div>';
			array_push( $outnobookarray, $orderpast );
		} elseif ( $current15_time < $start_timestamp ) {
			$orderfuture = '<div style="font-size:1.50em;color:black">Booking ' . $booking_id . ' occurs in the future, please return within ' . $time_offsetdisplay . ' Minutes of your session..<br></div>';
			array_push( $outnobookarray, $orderfuture );
			$futurecount ++;
		} //Room Window for entry is open - push options into two arrays.
        elseif ( $current_time < $end_timestamp ) {
			$window_bookingid = $booking_id;
			$orderwindowcount ++;
			$menuchoice = '<div style="font-size:1.25em;color:black"><a href="https://justcoach.uk/go?booking=' . $booking_id . '&order=' . $order . '">Booking - ' . $booking_id . ' with ' . $store_name . ' Starts at: ' . $start_date . '..</a> <br></div>';
			array_push( $outbookarray, $menuchoice );
		}
	}
	$outdatas = array(
		"validcount"   => $orderwindowcount,
		"invalidcount" => $invalidcount,
		"futurecount"  => $futurecount,
		"validlinks"   => $outbookarray,
		"rejections"   => $outnobookarray
	);
	if ( $returnfalse == true ) {
		return;
	} else {
		return $outdatas;
	}
}

/*ClubCloud - a Function to return Product Information and Vendor Info from Order Numbers///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes order number
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_orderinfo_by_ordernum( $order ) {
	global $bp, $WCFM, $WCFMmp;    // set up default parameters
	$order = $order + 0;
	if ( $order == "" ) {
		return "Blank Order";
	}
	$orderinfo = wc_get_order( $order );
	//return $orderinfo;
	$items     = $orderinfo->get_items();
	$outarray1 = array();
	foreach ( $items as $item ) {
		$product_name = $item->get_name();
		$product_id   = $item->get_product_id();
		$vendor_id    = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
		$store_user   = wcfmmp_get_store( $vendor_id );
		$store_info   = $store_user->get_shop_info();
		$store_name   = $store_info['store_slug'];
		$outdatas     = array(
			"productname" => $product_name,
			"productid"   => $product_id,
			"vendorid"    => $vendor_id,
			"storename"   => $store_name
		);
		array_push( $outarray1, $outdatas );
	}

	return $outarray1;
}

/*Club Cloud - A Function to Validate a Order Number - and Ensure it exists	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- Woocommerce Order Number (postID) - passed into it as string
Returns - False for invalid Number  - True for valid order number*/
function cc_validate_order( $ordnum ) {
	if ( get_post_type( $ordnum ) == "shop_order" ) {
		return true;
	} else {
		return false;
	}
}

/*Club Cloud - A Function to Validate a Booking ID - and Ensure it exists////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- BookingID - passed into it as string
Returns - False for invalid booking
Booking Object if Booking exists*/
function cc_validate_booking( $booking ) {
	if ( $booking == "" ) {  //reject blank bookings
		return false;
	}
	$bookingnum  = get_wc_booking( $booking );  //get booking from woocomm
	$orderobject = $bookingnum->order_id;      //check if there is an order ID in the booking object (impossible not to have one if it is real)
	if ( $orderobject == "" ) {
		return false;
	} else {
		return $bookingnum;
	}  //return the object
}

/*Club Cloud - A Function to Filter Expired Bookings /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- BookingID - passed into it as string - TimeOffset (to check how long in future we allow meetings to be entered) $returnmenuoption - if Menu items for multiple bookings need to be constructed
Returns - Formatted Error Message or Booking Object if Booking exists*/
function cc_validate_booking_time( $booking, $time_offset, $returnmenuoption, $xprofile_field ) {
	$min_offset = $time_offset / 60;//converting time offset back to minutes for friendly user message

	if ( cc_validate_booking( $booking ) == false ) {  //reject invalid bookings
		return;
	}
	$current_time    = current_time( 'timestamp' );
	$bookingdetail   = get_wc_booking( $booking );
	$start_date      = $bookingdetail->get_start_date();
	$end_date        = $bookingdetail->get_end_date();
	$end_timestamp   = strtotime( $end_date );
	$start_timestamp = strtotime( $start_date );
	$current15_time  = $current_time + $time_offset;
	if ( $current_time >= $end_timestamp ) {
		if ( $returnmenuoption == "singlebook" or $returnmenuoption == "checkonly" ) {
			return '<div style="font-size:2.0em;color:black"><br>Booking- ' . $booking . ' occurs in the past and can no longer be accessed.. <br></div>';
		} else {
			return;
		}
	} else if ( $current15_time < $start_timestamp ) {
		if ( $returnmenuoption == "singlebook" or $returnmenuoption == "checkonly" ) {
			return '<div style="font-size:2.0em;color:black"><br>Booking ' . $booking . ' occurs too far in the future. Please return ' . $min_offset . ' Minutes before your session..<br></div>';
		} else if ( $returnmenuoption == "singleid" ) {
			return $booking;
		} else {
			return;
		}
	}
	if ( $returnmenuoption == "singlebook" or $returnmenuoption == "message" or $returnmenuoption == "singleid" or $returnmenuoption == "merchantbook" or $returnmenuoption == "messagecustomer" ) {    //return $returnmenuoption." RetMenu at 1669<br>";
		$store_slug       = cc_orderinfo_by_booking( $booking, "store_slug", 0 );
		$store_name       = cc_orderinfo_by_booking( $booking, "store_name", 0 );
		$xprofile_setting = cc_xprofile_build( $xprofile_field, $booking, 0 );
		$cust_data        = get_wc_booking( $booking );
		$bookingid        = $booking;
		if ( $returnmenuoption == "singleid" ) {
			return $bookingid;
		}
		$shortcode         = do_shortcode( '[clubvideo map="' . $xprofile_setting . '" name="' . $store_slug . '-' . $bookingid . '" reception=true ]' );
		$shortcodemerchant = do_shortcode( '[clubvideo map="' . $xprofile_setting . '" name="' . $store_slug . '-' . $bookingid . '" admin=true ]' );

		if ( $returnmenuoption == "message" ) {
			return cc_get_bookingheader( $booking, "merchant", $store_name );
		}
		if ( $returnmenuoption == "messagecustomer" ) {
			return cc_get_bookingheader( $booking, "customer", $store_name );
		}
		if ( $returnmenuoption == "merchantbook" ) {
			return $shortcodemerchant;
		}
		if ( $returnmenuoption == "singlebook" ) {
			return $shortcode;
		}
	} else {
		return "true";
	}
}

/*Club Cloud A function to format Merchant helpful information in Bookings ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- BookingID - passed into it as string Returns - Formatted Message to Merchant or Error*/
function cc_get_bookingheader( $bookingid, $sc_type, $merchantname ) {
	$bookingisvalid = cc_validate_booking( $bookingid );
	if ( $bookingisvalid == false ) {        //trapping blank entry
		return "Invalid Booking Number is Entered";
	}
	$dp            = get_wc_booking( $bookingid );
	$booking_start = Date( 'F j, Y, g:i a', $dp->start );
	$booking_end   = Date( 'F j, Y, g:i a', $dp->end );
	//Formatting Customer Information
	$customer_num = $dp->customer_id;
	$customerinfo = get_userdata( $customer_num );
	$user_nice    = $customerinfo->user_nicename;
	if ( $sc_type == "merchant" ) {
		return '<div style="font-size:2.0em;color:black">Booking: ' . $bookingid . ' Starts: ' . $booking_start . ' Ends: ' . $booking_end . ' with Customer: ' . $user_nice . '<br></div>';
	}
	if ( $sc_type == "customer" ) {
		return '<div style="font-size:2.0em;color:black">Booking: ' . $bookingid . ' Starts: ' . $booking_start . ' Ends: ' . $booking_end . ' with Merchant: ' . $merchantname . '<br></div>';
	}
	if ( $sc_type == "customerid" ) {
		return $customer_num;
	} else {
		return "Invalid Shortcode Argument<br>";
	}
}

/**
 * This function/filter will add ics files from bookings created with WooCommerce Bookings to
 * the Processing and Completed emails sent from WooCommerce itself.
 *
 * @param arr $attachments Current array of attachments being filtered.
 * @param str $email_id The id of the email being sent.
 * @param obj $order The order for which the email is being sent.
 *
 * @return arr                The filtered list of attachments.
 */
function jp_add_ics_to_woocommerce_emails( $attachments, $email_id, $order ) {

	// The woocommerce email ids for which you want to attach the ics files.
	$available = array(
		'customer_processing_order',
		'customer_completed_order',
	);

	// Check to make sure we have a match.
	if ( in_array( $email_id, $available ) ) {

		// Get the booking ids from the order, and get the exporter object.
		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order->get_id() );
		$generate    = new WC_Bookings_ICS_Exporter;

		// Go through each id and add the attachments.
		foreach ( $booking_ids as $booking_id ) {
			$booking       = get_wc_booking( $booking_id );
			$attachments[] = $generate->get_booking_ics( $booking );

			// If the object is not unset, then the New Booking Email is sent twice.
			unset( $booking );
		}
	}

	return $attachments;
}

add_filter( 'woocommerce_email_attachments', 'jp_add_ics_to_woocommerce_emails', 10, 3 );

