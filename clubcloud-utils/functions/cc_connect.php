<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/* ClubCloud - Main Connect Centre Switching Shortcode///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
* This shortcode does the main switching and returns the right room for the video fulfilment depending on several parameters
*Arguments: Takes the order number, or Booking Number (same input field)*/
function cc_connect( $atts = array() ) {
	extract( shortcode_atts( array(
		'order'      => $headerordernum = htmlspecialchars( $_GET["order"] ),
		'booking'    => $headerbookingnum = htmlspecialchars( $_GET["booking"] ),
		'productnum' => $headerproductnum = htmlspecialchars( $_GET["productnum"] ),
		'vendor'     => $headervendorid = htmlspecialchars( $_GET["vendor"] )
	), $atts ) );

	$merchantBookingsString = '';
	$outputCustomerString   = '';

	//Covering against Bad Input (converting to INT)
	$order = $order + 0;
//Validate Booking
	if ( $booking != "" ) {
		$bookingisvalid = cc_validate_booking( $booking );
		//echo "1180 Booking is Valid - ". $bookingisvalid."- Booking Number: ".$booking."-<br>";
		if ( $bookingisvalid == false ) {        //trapping blank entry
			return '<div style="font-size:2.5em;color:black"><br>Invalid Booking Number entered or the Booking has been deleted<br></div>' . do_shortcode( '[elementor-template id="30831"]' );
		}
	}
	global $bp, $WCFM, $WCFMmp;//add Global BuddyPress and WCFM Functions
//  set time offset in minutes this is used for how long in future we want meeting filter to deny access if you come too soon to room;
	$xprofile_room   = 649; //this is the field in xprofile that matches the fulfilment room setting
	$min_offset      = 3000;
	$time_offset     = $min_offset * 60;
	$display_default = true;
	//Get User Logged In Users- Merchants - and Staff Tree - get information on roles and xprofile
	$isloggedin = is_user_logged_in();
	if ( $isloggedin == true ) {
		$user_id = get_current_user_id();
		$user    = wp_get_current_user();

		// @TODO - unused variables - check to remove!
		$roles     = ( array ) $user->roles;
		$role_type = cc_xprofile_build( 1135, 0, $user_id );    //1135 is Xprofile Value for setting
	}

//Set up Merchant Status
	if ( ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'shop_staff' or $user->roles[0] == 'administrator' ) ) {
		$ismerchant = true;
	}
//Begin Signed in User section - first deal with merchants w/ bookingnum, merchants w/o bookingnum, customers without order numbers.
	if ( $isloggedin == true ) {        //deal with Merchants with booking number -1 Check for Security - then Launch Valid Booking Check and Construct Code
		//echo "Booking Status - ".$booking." Is Merchant Status:".$ismerchant." <br>";
		if ( $booking != "" and $ismerchant == true ) {//security check - validate user store has this booking
			$display_default        = false;
			$currentbookingchecksum = cc_orderinfo_by_booking( $booking, "store_slug", 0 ); //our booking
			$mystoreid              = cc_orderinfo_by_booking( "", "store_slug", $user_id );                //the identified store booking
			$customer_id            = cc_get_bookingheader( $booking, "customerid", 0 );                    //identified customer
			if ( $currentbookingchecksum != $mystoreid and $customer_id != $user_id )            //either the store matches or the merchant identified or the merchant user is the customer
			{
				return '<div style="font-size:2.0em;color:black"><br>Security Error - You have tried to access a Booking that is not yours<br></div>' .
				       "Security Check Failed - Current Booking Checksum - " . $currentbookingchecksum . " and Returned Store ID Lookup from My ID" . $mystoreid . " Purchasing Customer ID " . $customer_id . " User ID is " . $user_id . "<br>";
			}
			//Once Security Check passed - get message for Screen, and get meeting
			$multibook_call = cc_validate_booking_time( $booking, $time_offset, "merchantbook", $xprofile_room );
			$messagecall    = cc_validate_booking_time( $booking, $time_offset, "message", $xprofile_room );

			return $messagecall . "<br>" . $multibook_call;    //echo for debug only "Future Items - ".$multibook_call['futurecount']." Present Items ".$multibook_call['validcount']. " Past Items ".$multibook_call['pastcount']. "<br>";
		}// if booking number is blank then deal gather all merchant bookings and call up orders
//end merchant with booking number section

		$display_running_count = 0;
		if ( $ismerchant == true ) //separate merchants from non-merchants
		{
			$vendor_bookings = apply_filters( 'wcfm_wcb_include_bookings', '' ); //get all bookings for vendor
			$storeid         = cc_orderinfo_by_booking( $vendor_bookings[0], "vendorid", 0 ); //In case of Employee of Store need to inject Store owner ID into constructor to get correct shop settings
			$outputmerchant  = array();
			//Main Constructor Merchant Get Booking Call
			$multibook_call = cc_shortcode_build( "merchant", 0, $storeid, $xprofile_room, $vendor_bookings, $time_offset, "false" );
			//print_r($multibook_call);
			// Merchant Display Booking Logic
			if ( $multibook_call['validcount'] >= 1 or $multibook_call['futurecount'] >= 1 ) {
				$display_default = false;
				if ( $multibook_call['validcount'] >= 1 ) {
					$display_default       = false;
					$display_running_count = $display_running_count + $multibook_call['validcount'];
				}
			}
			//in case of no future or present bookings
			if ( $multibook_call['validcount'] == 0 and $multibook_call['futurecount'] == 0 ) {
				$nothing_available = '<div style="font-size:1.75em;color:black"><br>No current orders available for merchant fulfilment</div>';
				array_push( $outputmerchant, $nothing_available );
			} //case where only one valid merchant booking exists
			elseif ( $multibook_call['validcount'] == 1 ) {
				$display_default = false;
				array_push( $outputmerchant, $multibook_call['message'] );
				array_push( $outputmerchant, $multibook_call['shortcode'] );
			} //cases where more than one valid option exists
			elseif ( $multibook_call['validcount'] > 1 or $multibook_call['futurecount'] >= 1 ) {
				$display_default = false;
				array_push( $outputmerchant, '<div style="font-size:2.00em;color:black"><br>Your Store Bookings <br><br></div>' );
				foreach ( $multibook_call['shortcode'] as $value ) {
					array_push( $outputmerchant, $value );
				}
				if ( $multibook_call['futurecount'] >= 1 ) {
					array_push( $outputmerchant, '<div style="font-size:2.00em;color:black"><br><br>Future Bookings<br><br></div>' );
					foreach ( $multibook_call['future'] as $value ) {
						array_push( $outputmerchant, $value );
					}
				}
			}

			foreach ( $outputmerchant as $value )//display the output of Merchant Bookings Array
			{
				$merchantBookingsString .= $value;
			}
			//return;
		}//end Merchant		//echo "1241 Post Early Logged in - Order is: ".$order." Booking is ".$booking." <br>";

// In the Customer Personality - We Return this User's Customer Perspective Orders
		if ( $order == "" and $booking == "" ) {
			$infototalorders = cc_orders_by_user( $user_id );
			//print_r($infototalorders);
			$validoutputcount    = 0;
			$outputcustomerarray = array();
			foreach ( $infototalorders as $ordernumber ) {
				$infoordersmerchant = cc_bookings_by_order( $ordernumber, $time_offset, "false" );
				//echo $infoordersmerchant['validcount']." Valid Count <br>";
				if ( $infoordersmerchant['validcount'] >= 1 or $infoordersmerchant['futurecount'] >= 1 ) {
					$ordermessage = "For Order " . $ordernumber . ":<br>";
					array_push( $outputcustomerarray, $ordermessage );
					$validoutputcount ++;
					foreach ( $infoordersmerchant as $value ) {
						foreach ( $value as $subvalue ) {
							array_push( $outputcustomerarray, $subvalue );
						}
					}
				}
			}
			if ( $validoutputcount == 0 ) {
			} else {
				$display_default = false;
				array_unshift( $outputcustomerarray, '<div style="font-size:2.00em;color:black"><br>Personal Purchased Bookings<br><br></div>' );
				foreach ( $outputcustomerarray as $subvalue ) {
					$outputCustomerString .= $subvalue;
				}
			}
		}//end Customer Personality Section

	}//end of if logged in section
	$booking_was_entered_check = $_GET["booking"];   //Get Booking Number Status;
	if ( $booking_was_entered_check == "" ) {
		$booking_was_entered_check = "blank";
	}
//Check Order Num Exists
	if ( $order != "" )   //first Validate Order
	{
		$order_val_check = cc_validate_order( $order );
		if ( $order_val_check == false ) {
			$display_default = false;

			return $outputCustomerString . $merchantBookingsString .
			       '<div style="font-size:2.5em;color:black"><br><br><br><br>' . $order . ' is not a valid Order Number. Please check your number and try again<br><br><br><br><br></div>';
		}
		//Now retrieve Bookings Count for Order
		$booking_ids  = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order );//check the validity of bookings made
		$bookingcount = count( $booking_ids );
		// echo $bookingcount." Booking Count <br>";
		if ( $bookingcount == 1 ) {
			$booking            = $booking_ids[0];
			$bookingupdatedflag = true;
		}
	}
	//Deal with Bookings Case for non signed in users (guests) - Validate Booking and Time- and trigger Constructor
	//echo "875 Booking was Entered is:".$booking_was_entered_check." and Booking was Updated Flag ".$bookingupdatedflag."<br>";
	if ( $booking_was_entered_check != "blank" or $bookingupdatedflag == true )        //bypass filter if no booking ID entered
	{
		$validbookingcheck = cc_validate_booking( $booking );
		//echo "1326 - inside booking check tree<br>";
		if ( $validbookingcheck == false ) {
			$display_default = false;

			return $outputCustomerString . $merchantBookingsString .
			       '<div style="font-size:2.50em;color:black"><br><br><br>Booking Number ' . $booking . ' is invalid. Please check and try again<br><br><br><br><br></div>';
		}
		//echo "1331 got to timecheck<br>";
		$timecheck_status = cc_validate_booking_time( $booking, $time_offset, "checkonly", 0 );
		//print_r($timecheck_status)." 1332 Time Check Status <br>";
		if ( $timecheck_status != "true" ) {
			$display_default = false;

			return $outputCustomerString . $merchantBookingsString . $timecheck_status;
		} else {
			$messagevalue = cc_validate_booking_time( $booking, $time_offset, "messagecustomer", $xprofile_room );
			$returnitem   = cc_validate_booking_time( $booking, $time_offset, "singlebook", $xprofile_room );

			return $outputCustomerString . $merchantBookingsString . $messagevalue . $returnitem;
		}
	}
	//deal with order numbers for non-signed in users
	//echo "1341 Pre-Constructor Fire - Booking is ".$booking." and Order is ".$order."<br>";
	if ( $booking != "" or $order != "" )//exclude from the Constructor function a case where decisions still need to be made
	{    //echo "1347 Got to the Constructor <br>";
		$outputbooking = array();
		if ( $booking != "" ) {  //echo "inside simple debug loop at 1346<br>";
			$multibook_call = cc_shortcode_build( "simple", $order, 0, $xprofile_room, $booking, $time_offset, "false" );
		} else if ( $ismerchant == true or is_admin() == true ) {    //echo "inside 1322-isadmin<br>";
			$multibook_call = cc_shortcode_build( "merchant", $order, 0, $xprofile_room, $booking, $time_offset, "false" );
		} else {//	echo "1356 pre fire<br>";
			$multibook_call = cc_shortcode_build( "multibooking", $order, 0, $xprofile_room, $booking, $time_offset, "false" );
		}
		//Display the Function in case of Array or Single Value
		//echo "1359 -".$multibook_call['validcount']. " Valid Count and ".$multibook_call['futurecount']." Future Count - ".$multibook_call['pastcount']." Past Count<br>";
		if ( $multibook_call['validcount'] >= 1 or $multibook_call['futurecount'] >= 1 ) {
			if ( $multibook_call['validcount'] == 1 )//straight to shortcode if there is only one booking
			{
				$display_default = false;

				return $outputCustomerString . $merchantBookingsString . $multibook_call['message'] . $multibook_call['shortcode'];
			}
			array_unshift( $outputbooking, '<div style="font-size:2.00em;color:black"><br>Purchased Bookings<br><br></div>' );
			foreach ( $multibook_call['shortcode'] as $value )//push all other bookings into array if there are more than one option
			{
				array_push( $outputbooking, $value );
			}
			if ( $multibook_call['futurecount'] >= 1 ) {
				$display_default = false;
				array_push( $outputbooking, '<div style="font-size:2.00em;color:black"><br>Your Future Bookings<br><br></div>' );
				foreach ( $multibook_call['future'] as $value ) {
					array_push( $outputbooking, $value );
				}
			}
			$output22 = '';
			foreach ( $outputbooking as $returnvalue )//display the output of Merchant Bookings Array
			{
				$output22 .= $returnvalue;
			}
			$display_default = false;

			return $outputCustomerString . $merchantBookingsString . $output22 . "<br>";
		} else {
			return $outputCustomerString . $merchantBookingsString . '<div style="font-size:1.75em;color:black"><br>No current or future bookings exist to enter under this number</div>';
		}
	}
	if ( $display_default == true and $isloggedin == true ) {
		return $outputCustomerString . $merchantBookingsString . do_shortcode( '[elementor-template id="24508"]' );
	} else if ( $display_default == true and $isloggedin == false ) {
		return $outputCustomerString . $merchantBookingsString . do_shortcode( '[elementor-template id="30831"]' );
	}

	return $outputCustomerString . $merchantBookingsString;
}

add_shortcode( 'ccconnect', 'cc_connect' );
