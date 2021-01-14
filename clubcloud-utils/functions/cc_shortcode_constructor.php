<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once(__DIR__ . '/../libs/MeetingIdGenerator.php');

/*ClubCloud - a Function to Construct the clubvideo Shortcode correctly with right settings///////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - Shortcode Type, Order Number, VendorID(optional), XProfile Field Number, BookingID, and Time Offset)
# Returns - a correctly formatted shortcode, or rejection of Booking information*/
function cc_shortcode_build( $sc_type, $ordernum, $vendorid, $xprofile_field, $bookingid, $time_offset, $showpast ) {
	global $WCFM, $WCFMmp;
	if ( $bookingid != "" and is_array( $bookingid ) == false ) {
		$bookingisvalid = cc_validate_booking( $bookingid );
		if ( $bookingisvalid == false ) {        //trapping blank entry
			return "Invalid Booking Number is Entered";
		}
	}
	if ( is_array( $bookingid ) == true )//if booking id is array - sanitise and validate each booking array entry (to remove booking orphans, and non valid bookings)
	{
		$bookingout = array();
		foreach ( $bookingid as $bookingitem ) {
			$vendor_temp = cc_validate_booking_time( $bookingitem, $time_offset, "singleid", $xprofile_field );
			if ( $vendor_temp != "" ) {
				array_push( $bookingout, $vendor_temp );
			}
		}
		$bookingid = $bookingout;
	}
	if ( $sc_type == "merchantsimple" )//setting Merchant flag to True - and changing processing type to multi-booking
	{
		$hasbookingflag   = true;
		$merchant_flag    = true;
		$window_bookingid = $bookingid;
		$sc_type          = "multibooking";
	} else if ( $sc_type == "merchant" )//setting Merchant flag to True - and changing processing type to multi-booking
	{
		$merchant_flag = true;
		$sc_type       = "multibooking";
	}
	if ( $sc_type == "simple" or $sc_type == "multibooking" ) {
	}       //Reject Gate of No Type Input
	else {
		return "Invalid Shortcode Type";
	}
	//debug only return "<br>1336 Booking -".$bookingid." Order Num ".$ordernum. " SC Type -".$sc_type. " X Profile Setting - ".$xprofile_field ." Vendor ID".$vendorid."<br>";
	// set up default parameters
	$time_offsetdisplay = $time_offset / 60;
	$current_time       = current_time( 'timestamp' );
	if ( is_array( $bookingid ) == true ) //Option 1 - function got passed multiple variables in array - apply passed variables
	{
		$orderdetail = $bookingid;
	} elseif ( $sc_type == "merchant" or $merchant_flag == true )//option 2 - check if you are a merchant, and pull your bookings if you are
	{
		$orderdetail = apply_filters( 'wcfm_wcb_include_bookings', '' );
	} else {
		$orderdetail = WC_Booking_Data_Store::get_booking_ids_from_order_id( $ordernum );
	}//Option 3 you must be a user - so pull your individual bookings.

//Set cases where we have booking ID and order number to simple to bypass heavier logic in multi
	if ( $bookingid != "" and $ordernum != "" and $merchant_flag == false ) {
		$sc_type = "simple";
	}
	if ( $vendorid == "" and count( $orderdetail ) <= 1 ) {
		$vendorid = $orderdetail[0]['vendorid'];
	}
	//Get Vendor IDs for Single Bookings
	if ( $vendorid == "" and $bookingid != "" ) {
		if ( is_array( $bookingid ) == true ) {
			$bookfirstid = $bookingid[0];
		} else {
			$bookfirstid = $bookingid;
		}
		$infoposta = get_wc_booking( $bookfirstid );
		$infopostb = $infoposta->product_id;
		$vendorid  = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $infopostb );
	}
	if ( count( $orderdetail ) <= 1 and $bookingid == "" ) {
		$bookingid = $orderdetail[0];
	}
	//return "<br>982 Booking -".$bookingid." Order Num ".$ordernum. " SC Type -".$sc_type. " X Profile Setting - ".$xprofile_field ." Vendor ID".$vendorid."<br>";
	if ( $vendorid != "" ) {   //if We have Vendor - Get Store Info
		$xprofile_setting = cc_xprofile_build( $xprofile_field, 0, $vendorid );
	}
	$store_slug = cc_orderinfo_by_booking( $bookingid, "store_slug", 0 );
	$store_name = cc_orderinfo_by_booking( $bookingid, "store_name", 0 );
	//debug only return  $store_name." Indicated Storename at 990<br>";
	if ( $xprofile_setting == "" and is_array( $bookingid ) == true ) {
		$xprofile_setting = cc_xprofile_build( $xprofile_field, $bookingid[0], 0 );
	} else {
		$xprofile_setting = cc_xprofile_build( $xprofile_field, $bookingid, 0 );
	}
	//return $xprofile_setting;
	if ( $xprofile_setting == "A Vendor Blank Setting Was Returned from Xprofile Constructor- User Does not Have this Setting Applied" ) {
		$vendor_detail   = get_userdata( $vendorid );
		$vendor_username = $vendor_detail->user_login;

		return "X-Profile Field Return Failure - Vendor : " . $vendorid . " Username : " . $vendor_username . "- Store name : " . $store_name . " probably doesn't have a default setup for Storefront Consult Room<br>";
	}
	//debug onlyreturn "Both provided -Booking ID ".$bookingid." and Order Num ".$ordernum. " Storename was - ".$store_name."<br>";
	switch ( $sc_type ) {
		case "simple":
			$shortcode = do_shortcode( '[clubvideo map="' . $xprofile_setting . '" name="' . $store_slug . '-' . $bookingid . '"]' );
			$outdatas  = array(
				"validcount" => 1,
				"message"    => cc_get_bookingheader( $bookingid, "customer", $store_name ),
				"shortcode"  => $shortcode
			);

			return $outdatas;
			break;
		case "multibooking":
			$outpastarray           = array();  // Set up counters and prepare arrays
			$outfuturearray         = array();
			$outbookarray           = array();
			$out_to_shortcode_array = array();
			//debug only return "got to 1382 Multibooking<br>";
			$invalidcount     = 0;
			$orderwindowcount = 0;
			$futurecount      = 0;
			foreach ( $orderdetail as $booking_id ) { //implement time filtration and reject past - or early meetings
				$booking         = get_wc_booking( $booking_id );
				$start_date      = $booking->get_start_date();
				$end_date        = $booking->get_end_date();
				$end_timestamp   = strtotime( $end_date );
				$start_timestamp = strtotime( $start_date );
				$current15_time  = $current_time + $time_offset;
				//Get Store Information for Friendly Display
				$store_name = cc_orderinfo_by_booking( $booking_id, "store_slug", 0 );

				if ( $current_time >= $end_timestamp and $showpast == "true" ) {
					$orderpast = '<div style="font-size:1.75em;color:black">Booking ' . $booking_id . ' occurs in the past and can no longer be accessed... <br></div>';
					array_push( $outpastarray, $orderpast );
				} elseif ( $current15_time < $start_timestamp ) {
					$orderfuture = '<div style="font-size:1.75em;color:black">Booking ' . $booking_id . ' <br></div>';
					$futurecount ++;
					array_push( $outfuturearray, $orderfuture );
				} //Room Window for entry is open - push options into two arrays.
                elseif ( $current_time < $end_timestamp ) {
					$orderwindowcount ++;
					$window_bookingid = $booking_id;
					$menuchoice       = '<div style="font-size:1.5em;color:black"><a href="https://justcoach.uk/go?booking=' . $booking_id . '&order=' . $ordernum . '">Booking - ' . $booking_id . ' with ' . $store_name . ' Starts at: ' . $start_date . '</a> <br></div>';
					array_push( $outbookarray, $menuchoice );
				}
			}     //return "Order Past- ".$orderpastcount. "- Order Future - ".$orderfuturecount." - Order Window- ".$orderwindowcount. " Total Loops ".$nobookarray_count."<br>";
			//In Case there is only one viable option - Get Data for Message and call the Shortcode
			if ( $orderwindowcount == 1 or $hasbookingflag == true ) {
				$store_slug       = cc_orderinfo_by_booking( $window_bookingid, "store_slug", 0 );
				$store_name       = cc_orderinfo_by_booking( $window_bookingid, "store_name", 0 );
				$xprofile_setting = cc_xprofile_build( $xprofile_field, $window_bookingid, 0 );
				$cust_data        = get_wc_booking( $window_bookingid );
				$bookingid        = $window_bookingid;
				if ( $merchant_flag == true ) {
					$shortcode = do_shortcode( '[clubvideo map="' . $xprofile_setting . '" name="' . $store_slug . '-' . $bookingid . '" lobby=true admin=true]' );
					$outdatas  = array(
						"validcount"   => $orderwindowcount,
						"invalidcount" => $invalidcount,
						"futurecount"  => $futurecount,
						"message"      => cc_get_bookingheader( $bookingid, "merchant", $store_name ),
						"shortcode"    => $shortcode
					);

					return $outdatas;
				} else {
					$shortcode = do_shortcode( '[clubvideo map="' . $xprofile_setting . '" name="' . $store_slug . '-' . $bookingid . '"]' );
					$outdatas  = array(
						"validcount"   => $orderwindowcount,
						"invalidcount" => $invalidcount,
						"futurecount"  => $futurecount,
						"message"      => cc_get_bookingheader( $bookingid, "customer", $store_name ),
						"shortcode"    => $shortcode
					);

					return $outdatas;
				}
			} else {
				$outdatas = array(
					"validcount"   => $orderwindowcount,
					"futurecount"  => $futurecount,
					"invalidcount" => $invalidcount,
					"pastcount"    => count( $outpastarray ),
					"past"         => $outpastarray,
					"future"       => $outfuturearray,
					"shortcode"    => $outbookarray
				);
			}

			return $outdatas;
			break;    //end Multibooking
	}//end case Multibook
}

/*ClubCloud - a Function to Construct Xprofile Field Settings from Fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - VendorID, XProfile Field Number     # Returns the Clubcloud template to use*/
function cc_xprofile_build( $xprofile_field, $bookingid, $vendorid ) {
	$defaultreturn='boardroom';
	
	global $WCFM, $WCFMmp;
	if ( $bookingid == "" and $vendorid == "" ) {   //trapping blank entry
		return 'Function Needs either a Booking Number OR a Vendor ID<br>';
	}
	if ( $xprofile_field == "" ) {
		return "Error- X-Profile Field is required<br>";
	}
	if ( $vendorid != "" ) {
		$returnfield = xprofile_get_field_data( $xprofile_field, $vendorid );
		if ( $returnfield == "" ) {
			return $defaultreturn;  // setting default of boardroom across the site
		} else {
			return $returnfield;
		}
	}
	if ( $vendorid == "" ) {
		$infoposta   = get_wc_booking( $bookingid );
		$infopostb   = $infoposta->product_id;
		$vendorid    = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $infopostb );
		$returnfield = xprofile_get_field_data( $xprofile_field, $vendorid );
		if ( $returnfield == "" ) {
			return $defaultreturn; 
		} else {
			return $returnfield;
		}
	}
}

/*ClubCloud - an Function to Construct Invites for meetings /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - Invite - the invite number    # Function is called on to support Shortcode meeting functions*/
function cc_invite( $invite, $direction, $input ) {
	
	if (isset ($invite) == false && isset ($input) == false && $direction !="user")
	return null;
	
	if (isset($input)){
		$comp = '@'; //Look for email
	//First Check for Email address
		if (strpos($input, $comp) == TRUE){
		$user = get_user_by( 'email', $host );
		}
		else $user = get_user_by( 'login', $input ); //next look for login in host name

		if ( $user == "" )//check by nicename if user name doesnt resolve
		{
			$user = get_user_by( 'slug', $input );
		}
		$userid = $user->ID;
		if ($userid == "" ){
			return "Invalid Username or Email entered<br>";
		}
	}


	if ($direction=="in"){
		$userid = ClubCloud_MeetingIdGenerator::getUserIdFromMeetingHash($invite);
	}
	elseif ($direction == "user"){
		$userid = ClubCloud_MeetingIdGenerator::getMeetingHashFromUserId($invite);
	}
	elseif ($direction == "out"){
		$userid = ClubCloud_MeetingIdGenerator::getMeetingHashFromUserId($userid);
	}
	return $userid;


}

//ClubCloud - A Shortcode to Return Header Displays and Meeting Invites correctly in Sequences for Menus///////////////////////////////////////////////////////////////////////////////////
//This is meant to be the new universal formatting invite list
function cc_invite_menu( $atts = array() ) {
	extract( shortcode_atts( array(
		'type'  => $type='host',
		'host' => $headerhostid = htmlspecialchars( $_GET["host"]),
		'invite' => $headerinviteid = htmlspecialchars( $_GET["invite"])
	), $atts ) );
	
	if ($type == 'host'){
		$user   = wp_get_current_user();
		$userid = $user->ID;
		$outmeetingid = cc_invite($userid , "user", null);
		return get_site_url() .	'/meet/?invite=' . $outmeetingid;
	}

	if ($type == 'guestname' OR $type == 'guestlink'){  //note this scenario requires the input in the header GET of an invite number
		$input = $host ;
		$userid = cc_invite($invite , "in", null);
		if ($userid == ''){
			$userobj = get_user_by( 'slug', $input );
			$userid = $userobj->ID;
		}
		if ($userid == ''){
			$userobj = get_user_by( 'login', $input );
			$userid = $userobj->ID;
			
		}
		if ($userid == ''){
			$userobj = get_user_by( 'email', $input );
			$userid = $userobj->ID;
			$invite = cc_invite($userid , "user", null);
			
		}
		$userdetail = get_user_by( 'ID', $userid );

		if ($type == 'guestname'){
		return $userdetail->display_name;
		}
		elseif ($type == 'guestlink' AND $invite !=''){
			return get_site_url() .	'/meet/?invite=' . $invite;
		}
	}

}
add_shortcode( 'ccinvitemenu', 'cc_invite_menu' );


//ClubCloud - A Shortcode to Return WCFM Search///////////////////////////////////////////////////////////////////////////////////
//This is meant to be the new universal formatting invite list
function cc_searchwcfm( $atts = array() ) {
	extract( shortcode_atts( array(
		'host' => $headerhostid = htmlspecialchars( $_GET["s"])
		), $atts ) );
		$host = preg_replace('/[^A-Za-z0-9\-]/', ' ', $host);
		return do_shortcode( '[wcfm_stores theme="compact" search_term ="'.$host.'"]' );

}
add_shortcode( 'ccsearchwcfm', 'cc_searchwcfm' );