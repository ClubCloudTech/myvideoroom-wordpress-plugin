<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

//Clubcloud-Product Archive Main Page Switchshortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the product archives to different templates
function cc_callproductpage() {
	$switchdata = xprofile_get_field_data( 730, 1 );//getting current page information to compare parent owners - using 730 which is Site setting for User 1
	switch ( $switchdata ) {
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="26439"]' );
			break;
	}

	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'ccproductpage', 'cc_callproductpage' );
//Clubcloud-Category Login Page Switch shortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Login Page archives to different templates
function cc_loginswitch() {
	$switchdata = xprofile_get_field_data( 730, 1 ); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if ( $switchdata == "" ) {
		$switchdata = "Sports_Club";
	}
	switch ( $switchdata ) {
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="25210"]' );
			break;
	}

	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'ccloginswitch', 'cc_loginswitch' );
//Clubcloud-Category Register Page Switch shortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Register Page archives to different templates
function cc_registerswitch() {
	$switchdata = xprofile_get_field_data( 730, 1 ); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if ( $switchdata == "" ) {
		$switchdata = "Sports_Club";
	}
	switch ( $switchdata ) {
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="25202"]' );
			break;
	}

	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'ccregisterswitch', 'cc_registerswitch' );
//Clubcloud-Category Change Password Page Switch shortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Change Password Page archives to different templates
function cc_chgpwdswitch() {
	$switchdata = xprofile_get_field_data( 730, 1 ); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if ( $switchdata == "" ) {
		$switchdata = "Sports_Club";
	}
	switch ( $switchdata ) {
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="25215"]' );
			break;
	}

	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'ccchgpwdswitch', 'cc_chgpwdswitch' );
//Clubcloud-Category External Press Page Switchshortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Press External Page archives to different templates
function cc_callpressexternalpage() {
	$switchdata = xprofile_get_field_data( 730, 1 ); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if ( $switchdata == "" ) {
		$switchdata = "Sports_Club";
	}
	switch ( $switchdata ) {
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="26351"]' );
			break;
	}

	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'ccpressexternalpage', 'cc_callpressexternalpage' );
//Clubcloud-Category External Press Page Switchshortcode ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the product archives to different templates
function cc_callcategorypage() {
	$switchdata = xprofile_get_field_data( 730, 1 );
//Handling a Blank Setting
	if ( $switchdata == "" ) {
		$switchdata = "Law_firm";
	}
	switch ( $switchdata ) {
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="23430"]' );
			break;
	}

	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'cccategorypage', 'cc_callcategorypage' );

/* ClubCloud - Display Storefront Layout - Change the look of each individual store	//////////////////////////////////////////////////////////////////////////////////////////
*Usage: In all front end storefront locations where seamless permissions video is needed. */
function cc_posttemplateswitch() {
	global $WCFM, $WCFMmp;
	$post = get_post();//getting current page information to compare parent owners
	//Get variables from Current Post
	$currentpost_store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$user_id              = $currentpost_store_id;
	$field_name_or_id     = 2073;//use Current Parent Page to get XProfile Setting for that merchant
	$xprofile_setting     = xprofile_get_field_data( $field_name_or_id, $user_id );
	//set default for switch case (for backup case)
	switch ( $xprofile_setting ) {
		case "Classic":
			return do_shortcode( '[elementor-template id="28010"]' );
			break;
		case "Simple":
			return do_shortcode( '[elementor-template id="27998"]' );
			break;
		case "E_Commerce_Marketplace":
			return do_shortcode( '[elementor-template id="16348"]' );
			break;
		case "Trade_Show":
			return do_shortcode( '[elementor-template id="21952"]' );
			break;
		case "Splat":
			return do_shortcode( '[elementor-template id="22197"]' );
			break;
		case "High_Energy":
			return do_shortcode( '[elementor-template id="22217"]' );
			break;
		case "Sports_Shop":
			return do_shortcode( '[elementor-template id="26454"]' );
			break;
		case "Shop_Window":
			return do_shortcode( '[elementor-template id="22199"]' );
			break;
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="21953"]' );
			break;
		case "Complete":
			return do_shortcode( '[elementor-template id="22201"]' );
			break;
		case "Sports_Club":
			return do_shortcode( '[elementor-template id="26454"]' );
			break;
		case "Professional":
			return do_shortcode( '[elementor-template id="22444"]' );
			break;
		case "Spa":
			return do_shortcode( '[elementor-template id="22734"]' );
			break;
	}
	//sets default case in case no selection by merchant
	if ( $xprofile_setting == "" ) {
		return do_shortcode( '[elementor-template id="27998"]' );
	}

}

add_shortcode( 'cctemplateswitch', 'cc_posttemplateswitch' );
/*Clubcloud - a shortcode to switch Join Pages to different club levels////////////////////////////////////////////////////////////////////////////////////////////////////////
*Switches Join1..n pages by Club wide selection to different join page templates
*Attributes - Join Page Number1..3*/
function cc_joinswitch( $atts = array() ) {
	extract( shortcode_atts( array(
		'joinid' => '1',
		'mode'   => 'shortcode',
		'name'   => '1'
	), $atts ) );
	$xprofile_setting = xprofile_get_field_data( 730, 1 );    //X-profile setting for user 1's 730 field (main site setting mode)
	//return "Join ID: ".$joinid." X-Profile ".$xprofile_setting."<br>";
	switch ( $xprofile_setting ) {
		case "E_Commerce_Marketplace":
			switch ( $name ) {
				case "marketplace":
					return "Marketplace";
					break;
				case "news":
					return "Community News";
					break;
				case "clubhub":
					return "Market Hub";
					break;
				case "storefront":
					return "My Store";
					break;
				case "marketplacesingular":
					return "Marketplace";
					break;
				case "lounge":
					return "Lounge";
					break;
				case "lobby":
					return "Lobby";
					break;
				case "clubtype":
					return "Marketplace";
					break;
			}
			switch ( $joinid ) {
				case "1":
					if ( $mode == "menu" ) {
						return "How It Works";
					}

					return do_shortcode( '[elementor-template id="24727"]' );
					break;
				case "2":
					if ( $mode == "menu" ) {
						return "Open Your Store";
					}

					return do_shortcode( '[elementor-template id="24818"]' );
					break;
			}
			break;
		case "Sports_Club":
			switch ( $name ) {
				case "marketplace":
					return "Coaching Marketplace";
					break;
				case "news":
					return "News Feed";
					break;
				case "clubhub":
					return "The Clubhouse";
					break;
				case "storefront":
					return "My Storefront";
					break;
				case "marketplacesingular":
					return "Coaching Marketplace";
					break;
				case "lounge":
					return "Lounge";
					break;
				case "lobby":
					return "Lobby";
					break;
				case "clubtype":
					return "Club";
					break;
			}
			switch ( $joinid ) {
				case "1":
					if ( $mode == "menu" ) {
						return "Find a Coach";
					}

					return do_shortcode( '[elementor-template id="24703"]' );
					break;
				case "2":
					if ( $mode == "menu" ) {
						return "Register as a Coach";
					}

					return do_shortcode( '[elementor-template id="24674"]' );
					break;
				case "3":
					if ( $mode == "menu" ) {
						return "Coaching CPD";
					}

					return do_shortcode( '[elementor-template id="24718"]' );
					break;
			}
			break;

	}//end main switch case
	return " The switch found no template for this selection type<br>";
}

add_shortcode( 'ccjoinswitch', 'cc_joinswitch' );
/*Clubcloud - a shortcode to switch Club Main Lounge to different Subscription Levels////////////////////////////////////////////////////////////////////////////////////////////////////////
*This code switches to the correct subscription template based on subscriber, and handles Admin or Special WP roles.
*This is needed as different subscription levels and wordpress roles need different dashboards.*/
function cc_loungeswitch() {
	$post  = get_post();//getting current page information to compare parent owners
	$user  = wp_get_current_user();//Fetch User Parameters and Roles
	$roles = ( array ) $user->roles;
	//Handling Admin Roles - sending them to Admin Lounge
	if ( $user->roles[0] == 'administrator' ) {
		return do_shortcode( '[elementor-template id="20006"]' );
	} //admin lounge
	//If user is non-admin Then get membership level and Re-create Array from Wordpress text input
	$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
				return do_shortcode( '[elementor-template id="26220"]' );
				break;
			case "8"://Coach silver
				return do_shortcode( '[elementor-template id="26234"]' );
				break;
			case "13"://Coach bronze
				return do_shortcode( '[elementor-template id="17081"]' );
				break;
			case "9"://platinum
				return do_shortcode( '[elementor-template id="17230"]' );
				break;
			case "10"://diamond
				return do_shortcode( '[elementor-template id="17225"]' );
				break;
			case "11"://Site Admin
				return do_shortcode( '[elementor-template id="20006"]' );
				break;
			case "12"://Ambassador
				return do_shortcode( '[elementor-template id="17502"]' );
				break;
			case "16"://Vendor Staff
				return do_shortcode( '[elementor-template id="22906"]' );
				break;
		}
	}    //sets default case in case no selection by merchant
	if ( $switchdef == true ) {
		return do_shortcode( '[elementor-template id="17081"]' );
	}
}

add_shortcode( 'ccloungeswitch', 'cc_loungeswitch' );
/*Clubcloud - a shortcode to switch The GO Dashboard to different Subscription Levels and experiences////////////////////////////////////////////////////////////////////////////////////////////////////////

*This is needed as different subscription levels and wordpress roles need different dashboards.*/
function cc_goswitch() {
	//signed out user
	if ( is_user_logged_in() == false ) {
		return do_shortcode( '[elementor-template id="28648"]' );}
	
	$post  = get_post();//getting current page information to compare parent owners
	$user  = wp_get_current_user();//Fetch User Parameters and Roles
	$roles = ( array ) $user->roles;
	//Handling Admin Roles - sending them to Admin Template
	if ( $user->roles[0] == 'administrator' ) {
		return do_shortcode( '[elementor-template id="28653"]' );}
	elseif ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'shop_staff' ) {
			return do_shortcode( '[elementor-template id="28637"]' );
		} 
	

	//If user is non-admin Then get membership level and Re-create Array from Wordpress text input
	$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "8"://Coach silver
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "13"://Coach bronze
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "9"://platinum
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "10"://diamond
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "11"://Site Admin
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "12"://Ambassador
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "16"://Vendor Staff
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
		}
	}
	//normal user 
	if ( is_user_logged_in() ) {
		return do_shortcode( '[elementor-template id="28651"]' );
	} 

}

add_shortcode( 'ccgoswitch', 'cc_goswitch' );

/*Clubcloud - a shortcode to switch The FIND A COACH pages to different Subscription Levels and login levels////////////////////////////////////////////////////////////////////////////////////////////////////////
Used by the /find main page to use correct template in elementor/templates
*/
function cc_findswitch() {
	//signed out user
	if ( is_user_logged_in()) {
		return do_shortcode( '[elementor-template id="29134"]' );}
	else 
		return do_shortcode( '[elementor-template id="31200"]' );
	
}

add_shortcode( 'ccfindswitch', 'cc_findswitch' );




/*Clubcloud - a shortcode to switch The  Meet Centre ////////////////////////////////////////////////////////////////////////////////////////////////////////
the /meet room works from this logic */

function cc_meetswitch() {
	//handle signed out users and return signed out templates
	if ( is_user_logged_in() == false ) {
		return do_shortcode( '[elementor-template id="29492"]' );
	}
	$user  = wp_get_current_user();//Fetch User Parameters and Roles
	$roles = ( array ) $user->roles;
	//Handling Store Owner Roles - sending them to Store Owner Template
	if ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'administrator' or $user->roles[0] == 'shop_staff' ) {
		return do_shortcode( '[elementor-template id="29498"]' );
	} 
	//If User Not Store owner - check for active subscription - send to Subscriber Template
	$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array. Subsciber Template 30955
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "8"://Coach silver
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "13"://Coach bronze
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "9"://platinum
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "10"://diamond
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "11"://Site Admin
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "12"://Ambassador
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
			case "16"://Vendor Staff
				return do_shortcode( '[elementor-template id="30955"]' );
				break;
		}
	}

	
	   //return just the logged normal user template
		return do_shortcode( '[elementor-template id="29495"]' );
	
}

add_shortcode( 'ccmeetswitch', 'cc_meetswitch' );

/*Clubcloud - a shortcode to switch The Account Center tab in dashboard to normal users or store owners////////////////////////////////////////////////////////////////////////////////////////////////////////

*This is needed as different subscription levels and wordpress roles need different dashboards.*/
function cc_accountctrswitch() {
	$post  = get_post();//getting current page information to compare parent owners
	$user  = wp_get_current_user();//Fetch User Parameters and Roles
	$roles = ( array ) $user->roles;
	//Handling Admin Roles - sending them to Admin Lounge
	if ( $user->roles[0] == 'administrator' ) {
		return do_shortcode( '[elementor-template id="27468"]' );
	} //admin lounge
	//If user is non-admin Then get membership level and Re-create Array from Wordpress text input
	$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "8"://Coach silver
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "13"://Coach bronze
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "9"://platinum
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "10"://diamond
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "11"://Site Admin
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "12"://Ambassador
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
			case "16"://Vendor Staff
				return do_shortcode( '[elementor-template id="27468"]' );
				break;
		}
	}    //sets default case in case no selection by merchant

	return do_shortcode( '[elementor-template id="29160"]' );
}

add_shortcode( 'ccaccountctrswitch', 'cc_accountctrswitch' );

/*Clubcloud - a shortcode to switch The Management Meeting Room to Host and Guests////////////////////////////////////////////////////////////////////////////////////////////////////////

*This is needed as different subscription levels and wordpress roles need different dashboards.*/
function cc_managementswitch() {
	$post  = get_post();//getting current page information to compare parent owners
	$user  = wp_get_current_user();//Fetch User Parameters and Roles
	$roles = ( array ) $user->roles;
	//Handling Admin Roles - sending them to Admin Lounge
	if ( $user->roles[0] == 'administrator' ) {
		return do_shortcode( '[elementor-template id="27972"]' );
	} //admin lounge
	//If user is non-admin Then get membership level and Re-create Array from Wordpress text input
	$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
			case "8"://Coach silver
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
			case "13"://Coach bronze
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
			case "9"://platinum
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
			case "10"://diamond
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
			case "11"://Site Admin
				return do_shortcode( '[elementor-template id="27972"]' );
				break;
			case "12"://Ambassador
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
			case "16"://Vendor Staff
				return do_shortcode( '[elementor-template id="27975"]' );
				break;
		}
	}    //sets default case in case no selection by merchant

	return do_shortcode( '[elementor-template id="27975"]' );
}

add_shortcode( 'ccmanagementswitch', 'cc_managementswitch' );

/*Clubcloud - a shortcode to switch entry Lobby ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*This code switches to the correct subscription template based on subscriber, and handles Admin or Special WP roles.*/
function cc_lobbyswitch() {
	$post   = get_post();//getting current page information to compare parent owners
	$user   = wp_get_current_user();  //Fetch User Parameters and Roles To make Routing Decisions
	$userid = $user->id;
	$roles  = ( array ) $user->roles;
	//Handling Any Role Type that was registered - add new role cases here.
	switch ( $user->roles[0] ) {
		case "shop_staff":
			return do_shortcode( '[elementor-template id="22918"]' );
			break;
	}
	//Get Call for the Lobby Registration type from Xprofile field 1268 which is the Pre-Reg type field
	$switchdata = xprofile_get_field_data( 1268, $userid );
	if ( $switchdata == "" ) {
		return "Xprofile return failure - no entry found<br>";
	}//trapping blank return
	switch ( $switchdata ) {
		case "Club-School":
			return do_shortcode( '[elementor-template id="24849"]' );
			break;
		case "Player":
			return do_shortcode( '[elementor-template id="24849"]' );
			break;
		case "Coach":
			return do_shortcode( '[elementor-template id="24896"]' );
			break;
		case "Team":
			return do_shortcode( '[elementor-template id="24849"]' );
			break;
		case "CPD":
			return do_shortcode( '[elementor-template id="24937"]' );
			break;
	}

	//Otherwise - Return default registered user - Sales Template
	return do_shortcode( '[elementor-template id="22921"]' );
}

add_shortcode( 'cclobbyswitch', 'cc_lobbyswitch' );
