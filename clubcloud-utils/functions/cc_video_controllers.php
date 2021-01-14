<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/*Clubcloud - A function to Switch Video Consultation Room Templates by Merchant Selection/////////////////////////////////////////////////////////////////////////////////////////////////////
# A Merchant selects their Consult room preference and the Shortcode switches template */
function cc_callconsult() {
	global $WCFM, $WCFMmp, $bp;    //calling in WCFM globals so we can get their merchant variables
	$post = get_post(); //getting current page information to compare parent owners
	//Fetch User Parameters and Roles
	if ( is_user_logged_in() ) {
		$user  = wp_get_current_user();
		$roles = ( array ) $user->roles;
	}
	/*get meta data from currently logged in user and return the parent vendor id
	We use this to know if a user is a child merchant/staff etc*/
	$my_vendor_id = get_user_meta( $user->id, '_wcfm_vendor', true );
	$my_owner_id  = get_current_user_id();
	$shop_name    = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $my_vendor_id );
//get parent post ID to understand if we are in our home store or someone elses
	$currentpost_store_id   = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$currentpost_store_user = wcfmmp_get_store( $currentpost_store_id );
	$currentpost_store_info = $currentpost_store_user->get_shop_info();
	$user_id                = $currentpost_store_id;
	$field_name_or_id       = 569;    //569 is the field number of the Xprofile setting of Control Video Storefront, 649 is Private Consultation Room
//Get xprofile setting from Merchant Settings
	$xprofile_setting = xprofile_get_field_data( $field_name_or_id, $user_id );
//Set Display template to Boardroom in case Profile setting is blank
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "office2";
	}
}

add_shortcode( 'ccconsult', 'cc_callconsult' );
/* ClubCloud - Display Storefront Video based on Buddypress XProfile Parameters for Merchants and Visitors//////////////////////////////////////////////////////////////////////////////////////
*	Usage: In all front end storefront locations where seamless permissions video is needed. */
function cc_callstorefront() {
	//calling in WCFM globals so we can get their merchant variables
	global $WCFM, $WCFMmp, $bp;
	$post = get_post();    //getting current page information to compare parent owners
	//Fetch User Parameters and Roles
	if ( is_user_logged_in() ) {
		$user  = wp_get_current_user();
		$roles = ( array ) $user->roles;
	}
	/*get meta data from currently logged in user and return the parent vendor id
	We use this to know if a user is a child merchant/staff etc*/
	$my_vendor_id = get_user_meta( $user->id, '_wcfm_vendor', true );
	$my_owner_id  = get_current_user_id();
	$shop_name    = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $my_vendor_id );
	//return "Shop Name".$shop_name."<br>"."My-Vendor-ID".$my_vendor_id."<br>"."My Owner ID- ".$my_owner_id."<br>"."Roles<br>".print_r($roles);
	//get parent post ID to understand if we are in our home store or someone elses
	$currentpost_store_id   = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$currentpost_store_user = wcfmmp_get_store( $currentpost_store_id );
	$currentpost_store_info = $currentpost_store_user->get_shop_info();
	$user_id                = $currentpost_store_id;
	$field_name_or_id       = 569;//569 is the field number of the Xprofile setting of Control Video Storefront, 649 is Private Consultation Room
	$field_quarantine       = 667;//667 is the quarantine field number from XProfile
	//Get xprofile setting from Merchant Settings for Main Video
	// debug return $user_id."User ID </n>";
	$xprofile_setting = xprofile_get_field_data( $field_name_or_id, $user_id );
	//debug - return $xprofile_setting."X-prof</n>";
	$xprofileq_setting = xprofile_get_field_data( $field_quarantine, $user_id );
	//Set Display template to Boardroom in case Profile setting is blank
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "Yoga-studio-4seats";
	}
	if ( $xprofile_setting == "hall1" )//intercept event templates by merchants and remove auth
	{
		$bypass_security = true;
	}
	//Give Administrators or Store Manager Full Rights

	if ( $user->roles[0] == 'administrator' or $user->roles[0] == 'store_manager' ) {    //in case Admin has their own store
		if ( $my_owner_id == $currentpost_store_id ) {
			$store_id   = $currentpost_store_id;
			$store_user = wcfmmp_get_store( $store_id );
			$store_info = $store_user->get_shop_info();
			$store_name = $store_info['store_slug'];

			return do_shortcode( '[clubvideo name="store-' . $store_name . '" map="' . $xprofile_setting . '" admin=true]' );
		} else { //admin gets store owner view
			$store_id   = $currentpost_store_id;
			$store_user = wcfmmp_get_store( $store_id );
			$store_info = $store_user->get_shop_info();
			$store_name = $store_info['store_slug'];

			return do_shortcode( '[clubvideo name="store-' . $store_name . '" map="' . $xprofile_setting . '" admin=true]' );
		}
	}
	//Switch Store Owner from Staff and Othe
	//echo Check is a WCFM vendor, or store staff
	if ( $user->roles[0] == 'wcfm_vendor' and $my_owner_id == $currentpost_store_id ) {
		//case of an Owner in their own store
		$store_id   = $my_vendor_id;
		$store_user = wcfmmp_get_store( $store_id );
		$store_info = $store_user->get_shop_info();
		$store_name = $store_info['store_slug'];

		return do_shortcode( '[clubvideo name="store-' . $store_name . '" map="' . $xprofile_setting . '" admin=true]' );
	} //case of a Staff Member in their own store
    elseif ( $user->roles[0] == 'shop_staff' and $my_vendor_id == $currentpost_store_id ) {
		$store_id   = $my_vendor_id;
		$store_user = wcfmmp_get_store( $store_id );
		$store_info = $store_user->get_shop_info();
		$store_name = $store_info['store_slug'];

		return do_shortcode( '[clubvideo name="store-' . $store_name . '" map="' . $xprofile_setting . '" admin=true]' );
	}
	//now we have removed Storeowner/Staff in their own store case we deal with Storeowners and Staff in other people stores
	/*elseif($user->roles[0]=='shop_staff'OR $user->roles[0]=='wcfm_vendor')
		{	$store_id		 = $currentpost_store_id;
			$store_user      = wcfmmp_get_store( $store_id );
			$store_info      = $store_user->get_shop_info();
			$store_name      = $store_info['store_slug'];
			//echo "this means Competitor Role code ran";
			if ($bypass_security==true)
			{return do_shortcode('[clubvideo name="store-' . $store_name . '" map="'.$xprofile_setting.'"cc_is_event=true]');		}
			else {return do_shortcode('[clubvideo name="store-'.$store_name.'-vendorvisitor" map="'.$xprofileq_setting.'"]'); }
		}*/
	//this person is not a merchant, admin, or Storemanager - so give them the normal store experience
	else {
		$store_id   = $currentpost_store_id;
		$store_user = wcfmmp_get_store( $store_id );
		$store_info = $store_user->get_shop_info();
		$store_name = $store_info['store_slug'];

		return do_shortcode( '[clubvideo name="store-' . $store_name . '" map="' . $xprofile_setting . '" lobby=false]' );
	}
}

add_shortcode( 'ccstorefrontvideo', 'cc_callstorefront' );

/* ClubCloud - Display Merchant Control Centre Video based on Buddypress XProfile Parameters for Merchants//////////////////////////////////////////////////////////////////////////////////////
*  Usage: In all front end Merchant Control Centre locations where Access to Storefront is needed*/
function cc_merchantvideo() {
	//calling in WCFM and BP globals so we can get their merchant variables
	global $WCFM, $WCFMmp;
	//getting current page information to compare parent owners
	$post = get_post();
	if ( is_user_logged_in() ) {
		$user  = wp_get_current_user();
		$roles = ( array ) $user->roles;
	}
	//Extract Correct Shop Parent ID from Logged in User
	$xprofile_field_num = 569;    //569 is the field number of the Xprofile setting of Control Video Storefront, 649 is Private Consultation Room
	$my_vendor_id       = get_user_meta( $user->id, '_wcfm_vendor', true );//this filter returns staff - if not staff we add owner ID.
	if ( $my_vendor_id == "" ) {
		$my_vendor_id = get_current_user_id();
	}
	$xprofile_setting = cc_xprofile_build( $xprofile_field_num, 0, $my_vendor_id );
	$store_name       = cc_orderinfo_by_booking( 0, "store_slug", $my_vendor_id );
	//Set Display template to Boardroom in case Profile setting is blank
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "office2";
	}

	return do_shortcode( '[clubvideo name="store-' . $store_name . '" map="' . $xprofile_setting . '" admin=true cc_enable_cart=true]' );
}

add_shortcode( 'ccmerchantvideo', 'cc_merchantvideo' );
/*Clubcloud - A Shortcode for the Member Boardroom View to Switch by Xprofile Setting - Member////////////////////////////////////////////////////////////////////////////////////////////////////
*This is used for the Member Backend entry pages to access their preferred Video Layout - it is paired with the ccboardroomvideoguest function*/
function cc_boardroomvideomember() {
	global $bp;//add Global BuddyPress Functions
	if ( is_user_logged_in() ) {
		$user   = wp_get_current_user();
		$userid = $user->ID;
		$slug 	= preg_replace('/[^A-Za-z0-9\-]/', '', $user->user_login);
	}
	//get membership levels for filtering subs and rejecting non subscribed users

	if ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'shop_staff' or $user->roles[0] == 'administrator' ) {
		$membershipblock = false;
	} else {
		$membershipblock = true;
	}
	$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
				$membershipblock = false;
				break;
			case "8"://Coach silver
				$membershipblock = false;
				break;
			case "13"://Coach bronze
				$membershipblock = false;
				break;
			case "9"://platinum
				$membershipblock = false;
				break;
			case "10"://diamond
				$membershipblock = false;
				break;
			case "11"://Site Admin
				$membershipblock = false;
				break;
			case "12"://Ambassador
				$membershipblock = false;
				break;
			case "16"://Vendor Staff
				$membershipblock = false;
				break;
		}
	}    //sets default case in case no selection by merchant
	if ( $membershipblock == true )//Show Upgrade Template
	{
		return do_shortcode( '[elementor-template id="29585"]' );
	}

	$fieldnum = 801;//801 is the Setting in xprofile for Video Selection for VIP Boardroom
	//Get xprofile setting from Merchant Settings for Main Video
	$xprofile_setting = xprofile_get_field_data( $fieldnum, $userid );
	//Set Display template to Boardroom in case Profile setting is blank
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "boardroom";
	}
	
	$displayid = $user->display_name;
	$output = preg_replace('/[^A-Za-z0-9\-]/', '', $displayid); //remove special characters from username

	$outmeetingid = cc_invite($userid, "user", null);
	return do_shortcode( '[clubvideo name="meeting-' . $outmeetingid . '-' . $output . '" map="' . $xprofile_setting . '" admin=true]' );
}

add_shortcode( 'ccboardroomvideomember', 'cc_boardroomvideomember' );
/*Clubcloud - A Shortcode for the Boardroom View to Switch by Xprofile Setting - Guest/////////////////////////////////////////////////////////////////////////////////////////////////////
* This is used for the Guest entry pages to access the Member Selected Video Layout - it is paired with the ccboothvideomember function
* It accepts hostname as an argument which it gets from the Guest page URL get request parameter */
function cc_boardroomvideoguest( $atts = array() ) {
	extract( shortcode_atts( array(
		'host'     => $headerhostname = htmlspecialchars( $_GET["host"] ),
		'invite'     => $headerinvite = htmlspecialchars( $_GET["invite"] ),
		'urlcheck' => false
	), $atts ) );
	global $bp;//add Global BuddyPress Functions
	if ($invite != "" ){
		$userid= cc_invite($invite, "in", null);  
		
	}
	else{

		$comp = '@'; //Look for email
	//First Check for Email address
		if (strpos($host, $comp) == TRUE){
		$user = get_user_by( 'email', $host );
		}
		else $user = get_user_by( 'login', $host ); //next look for login in host name

		if ( $user == "" )//check by nicename if user name doesnt resolve
		{
			$user = get_user_by( 'slug', $host );
		}
		$userid = $user->ID;
	}
	
	
	//Filter out logging into same room and redirecting page
	$user_checksum = get_current_user_id();
	if ( is_user_logged_in() == true ) {
		if ( $user_checksum == $userid ) {  // @TODO Look for better way to redirect
			echo '<script type="text/javascript">';
			echo 'location.href = "https://justcoach.uk/meet"';
			echo '</script>';
		}
	}
	if ( $urlcheck == false && isset($host) == false && isset($invite) == false ) {   //blank and invite host must get host name again
		return do_shortcode( '[elementor-template id="29447"]' );
	}
	if ( $urlcheck == true && $userid == "" ) {
		return " Invalid Member or Host Name ";
	} else if ( $urlcheck == true && $userid != "" ) {
		return $host;
	}

	if ( $userid == "" ) { //this means the check for user has failed - and we must push back to user
		return do_shortcode( '[elementor-template id="29539"]' ) . do_shortcode( '[elementor-template id="29447"]' );
	}

	//prepare to call shortcode
	$user_field = get_user_by( 'ID', $userid );
	$displayid = $user_field->display_name;
	$output = preg_replace('/[^A-Za-z0-9\-]/', '', $displayid); //remove special characters from username

	$fieldnum = 801;//801 is the Setting in xprofile for Video Selection
	//Get xprofile setting from Merchant Settings for Main Video
	$xprofile_setting = xprofile_get_field_data( $fieldnum, $userid );
	//Set Display template to Boardroom in case Profile setting is blank
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "boardroom";
	}
	$outmeetingid = cc_invite($userid, "user", null);

	return do_shortcode( '[elementor-template id="29533"]' ) . do_shortcode( '[clubvideo name="meeting-' . $outmeetingid . '-' . $output . '" map="' . $xprofile_setting . '" reception=true ]' );

}

add_shortcode( 'ccboardroomvideoguest', 'cc_boardroomvideoguest' );

//Clubcloud - A Shortcode for the Management Boardroom View to Switch by Xprofile Setting - Member/////////////////////////////////////////////////////////////////////////////////////////
//This is used for the Member Backend entry pages to access their preferred Video Layout - it is paired with the ccmanagementroomvideoguest function
function cc_managementroomvideomember() {
	global $bp;//add Global BuddyPress Functions
	$fieldnum         = 778; //778 is the Setting in xprofile for Video Selection
	$xprofile_setting = xprofile_get_field_data( $fieldnum, 1 );  //Get xprofile setting from Merchant Settings for Main Video
	//Set Display template to Boardroom in case Profile setting is blank
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "boardroom";
	}

	// return do_shortcode('[clubvideo name="JC-management-meeting" map="'.$xprofile_setting.'" lobby=true admin=true]');
	return do_shortcode( '[clubvideo name="JC-management-meeting" map="' . $xprofile_setting . '" admin=true]' );
}

add_shortcode( 'ccmanagementroomvideomember', 'cc_managementroomvideomember' );
/*Clubcloud - A Shortcode for the Management View to Switch by Xprofile Setting - Guest/////////////////////////////////////////////////////////////////////////////////////////////////////////
* This is used for the Guest entry pages to access the Management Meeting Room - it is paired with the ccmanagementroomvideomember function
* It accepts hostname as an argument which it gets from the Guest page URL get request parameter */
function cc_managementroomvideoguest() {
	global $bp;//add Global BuddyPress Functions
	$fieldnum         = 778;//778 is the Setting in xprofile for Video Selection
	$xprofile_setting = xprofile_get_field_data( $fieldnum, 1 ); //Get xprofile setting from Merchant Settings for Main Video
	//Set Display template to Boardroom in case Profile setting is blank default boardroom1
	if ( $xprofile_setting == "" ) {
		$xprofile_setting = "boardroom";
	}
	// return do_shortcode('[clubvideo name="JC-management-meeting" map="'.$xprofile_setting.'"]');
	// return do_shortcode('[clubvideo name="JC-management-meeting" map="'.$xprofile_setting.'"]');
	return do_shortcode( '[clubvideo name="JC-management-meeting" map="' . $xprofile_setting . '" reception=true]' );
}

add_shortcode( 'ccmanagementroomvideoguest', 'cc_managementroomvideoguest' );