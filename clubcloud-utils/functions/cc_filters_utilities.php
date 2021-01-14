<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}
//Security functions first


//ClubCloud - Function to Disable Group 3  (Store Settings) visibility///////////////////////////////////////////////////////////////////////////////////////////////////////
function g3bpfr_hide_profile_field_group( $groups ) {
	$user                = wp_get_current_user();
	$roles               = ( array ) $user->roles;
	$blockviewpermission = true;
	if ( $user->id == 1 ) {
		$blockviewpermission = false;
	}
	if ( $blockviewpermission == true ) {
		$remove_groups = array( 3 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj ) {
			if ( in_array( $group_obj->id, $remove_groups ) ) {
				unset( $groups[ $key ] );
			}
		}
		$groups = array_values( $groups );
	}

	return $groups;
}

add_filter( 'bp_profile_get_field_groups', 'g3bpfr_hide_profile_field_group' );
//ClubCloud - Function to Disable Group 4  Personal Video visibility///////////////////////////////////////////////////////////////////////////////////////////////////////
function g4bpfr_hide_profile_field_group( $groups ) {
	$user                = wp_get_current_user();
	$roles               = ( array ) $user->roles;
	$blockviewpermission = true;
	if ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'administrator' or $user->roles[0] == 'shop_staff' ) {
		$blockviewpermission = false;
    }
    //get all membership levels and enable visibility for subscription levels
    $membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
	$memlev           = explode( ',', $membership_level[0] );
	$array_count      = count( $memlev );
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
		switch ( $memlev[ $x ] ) {
			case "15": //Coach gold
                $blockviewpermission = false;
				break;
			case "8"://Coach silver
                $blockviewpermission = false;
				break;
			case "13"://Coach bronze
                $blockviewpermission = false;
				break;
			case "9"://platinum
                $blockviewpermission = false;
				break;
			case "10"://diamond
                $blockviewpermission = false;
				break;
			case "11"://Site Admin
				$blockviewpermission = false;
				break;
			case "12"://Ambassador
                $blockviewpermission = false;
				break;
			case "16"://Vendor Staff
                $blockviewpermission = false;
				break;
		}
	}    //sets default case in case no selection by merchant

	if ( $blockviewpermission == true ) {
		$remove_groups = array( 4, 5 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj ) {
			if ( in_array( $group_obj->id, $remove_groups ) ) {
				unset( $groups[ $key ] );
			}
		}
		$groups = array_values( $groups );
	}

	return $groups;
}


add_filter( 'bp_profile_get_field_groups', 'g4bpfr_hide_profile_field_group' );
//ClubCloud - Add Menu Deletions for Items for Non Storevendors///////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Blocking the tabs (in other functions) doesnt delete visibility in case of direct URL access, so good security to delete the fields
//Use the Xpprofile Field ID to remove any menu item that non-admins or storeowners shouldnt see.
function mnbpfr_hide_profile_field_group( $retval ) {
	if ( bp_is_active( 'xprofile' ) ) :
		// hide profile group/field to all except admin
		$user                = wp_get_current_user();
		$roles               = ( array ) $user->roles;
		$blockviewpermission = true;
		if ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'administrator' ) {
			$blockviewpermission = false;
		} else if ( is_super_admin() ) {
			$blockviewpermission = false;
		}
		if ( $blockviewpermission == true ) {
			$retval['exclude_fields'] = '673,667,669,582,569,649,730,2073';  //exclude fields, separated by comma
			$retval['exclude_groups'] = '2';
		}

		return $retval;
	endif;
}

add_filter( 'bp_after_has_profile_parse_args', 'mnbpfr_hide_profile_field_group' );
//ClubCloud - Function to Disable Non Admin Views of Site Control //////////////////////////////////////////////////////////////////////////////////////////////////////////////
//We dont want to show Store customisation settings to Non-Merchants
function bpfr_hide_profile_field_group( $groups ) {
	$user  = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$auth  = false;
	//case moderate own profile
	if ( bp_is_user_profile_edit() && ! current_user_can( 'bp_moderate' ) ) {
		$admin = true;
	}
	//case admin
	if ( $user->roles[0] == 'wcfm_vendor' or $user->roles[0] == 'administrator' ) {
		$auth = true;
	}
	if ( $auth == false ) {
		$remove_groups = array( 3 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj ) {
			if ( in_array( $group_obj->id, $remove_groups ) ) {
				unset( $groups[ $key ] );
			}
		}
		$groups = array_values( $groups );
	}

	return $groups;
}

add_filter( 'bp_profile_get_field_groups', 'bpfr_hide_profile_field_group' );
//ClubCloud - Function to Disable Group 2 Non Merchant visibility ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//We dont want to show Store customisation settings to Non-Merchants
function mbpfr_hide_profile_field_group( $groups ) {
	$user                = wp_get_current_user();
	$roles               = ( array ) $user->roles;
	$blockviewpermission = true;
	if ( $user->roles[0] == 'wcfm_vendor' ) {
		$blockviewpermission = false;
	}
	if ( $user->roles[0] == 'administrator' ) {
		$blockviewpermission = false;
	}

	if ( $blockviewpermission == true ) {
		$remove_groups = array( 2 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj ) {
			if ( in_array( $group_obj->id, $remove_groups ) ) {
				unset( $groups[ $key ] );
			}
		}
		$groups = array_values( $groups );
	}

	return $groups;
}

add_filter( 'bp_profile_get_field_groups', 'mbpfr_hide_profile_field_group' );
/*ClubCloud  - Customise My Account Page. /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
* This function modifies the default MyAccount page.  */
add_filter( 'woocommerce_account_menu_items', 'cc_myaccountchange', 40 );
function cc_myaccountchange( $menu_links ) {
	$menu_links = array_slice( $menu_links, 0, 1, true )
	              + array( 'cc_mysubs' => 'My Subscriptions' )
	              + array_slice( $menu_links, 1, null, true );

	return $menu_links;
}

add_action( 'init', 'cc_add_menu_endpoint' );    /* Register Permalink Endpoint  */
function cc_add_menu_endpoint() {
	add_rewrite_endpoint( 'cc_mysubs', EP_PAGES );
}

/* Content for the new page in My Account endpoint */
add_action( 'woocommerce_account_cc_mysubs_endpoint', 'cc_subscriptions_endpoint' );
function cc_subscriptions_endpoint() {
	return do_shortcode( '[elementor-template id="24402"]' );
}

/*ClubCloud  - Add to Cart Redirect Function. /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
* In order to use product straight to check out use ?add-to-cart=%%ProductPOSTID%% eg ?add-to-cart=34553
* Used to be able to send Subscriptions Straight to Checkout so one Click Buy Works*/
add_filter( 'woocommerce_add_to_cart_redirect', 'straight_to_checkout' );
function straight_to_checkout() {
	$checkouturl = WC()->cart->get_checkout_url();

	return $checkouturl;
}

$cc_clubcloud_directory = content_url( '/clubcloud' );
//ClubCloud  - Add Order Action to My Order Page////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_add_my_account_order_actions( $actions, $order ) {
	$actions['video'] = array(
		'url'  => '/go/?&order=' . $order->get_order_number(),
		'name' => __( 'Video Room', 'my-textdomain' ),
	);

	return $actions;
}

add_filter( 'woocommerce_my_account_my_orders_actions', 'cc_add_my_account_order_actions', 10, 2 );
//Club Cloud Menu Name - may be deprecated in favour of ccname - which handles the edge case of store owners////////////////////////////////////////////////////////////////////////
function cc_menu() {
	global $WCFM, $WCFMmp;
	$user  = wp_get_current_user();
	$roles = ( array ) $user->roles;
	if ( $user->roles[0] == 'wcfm_vendor' ) {
		$store_user = wcfmmp_get_store( $user->ID );
		$store_info = $store_user->get_shop_info();
		$store_data = $store_info['store_slug'];

		return $store_data;
	}

	//If they aren't a vendor then we simply return User Login- if you need to handle Staff use ccgetname
	return $user->user_login;
}

add_shortcode( 'ccmenu', 'cc_menu' );

//ClubCloud - Action Filters to Implement a Video Hub system in WCFM Elementor to deploy a video consult room/////////////////////////////////////////////////////////////////////////
//WCFM Template to Implement Video Hub Tabbed menu
add_action( 'wcfmmp_rewrite_rules_loaded', function ( $wcfm_store_url ) {
	global $tab;
	global $template;
	add_rewrite_rule( $wcfm_store_url . '/([^/]+)/video_storefront?$', 'index.php?' . $wcfm_store_url . '=$matches[1]&video_storefront=true', 'top' );
	add_rewrite_rule( $wcfm_store_url . '/([^/]+)/video_storefront/page/?([0-9]{1,})/?$', 'index.php?' . $wcfm_store_url . '=$matches[1]&paged=$matches[2]&video_storefront=true', 'top' );
}, 50 );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'video_storefront';

	return $vars;
}, 50 );
add_filter( 'wcfmmp_store_tabs', function ( $store_tabs, $store_id ) {
	$store_tabs['video_storefront'] = 'Video Hub';

	return $store_tabs;
}, 50, 2 );
add_filter( 'wcfmp_store_tabs_url', function ( $store_tab_url, $tab ) {
	if ( $tab == 'video_storefront' ) {
		$store_tab_url .= 'video_storefront';
	}

	return $store_tab_url;
}, 50, 2 );
add_filter( 'wcfmp_store_default_query_vars', function ( $query_var ) {
	global $WCFM, $WCFMmp;
	if ( get_query_var( 'video_storefront' ) ) {
		$query_var = 'video_storefront';
	}

	return $query_var;
}, 50 );

//ClubCloud A Function to do logout//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_logout() {
	return wp_logout_url( home_url() );
}

add_shortcode( 'cclogout', 'cc_logout' );


function cc_debughook() {
	
 $debug= array();
add_action( 'all', function ( $tags ) {
    global $debug;
    if ( in_array( $tags, $debug ) ) {
        return;
    }
    echo $tags . "<br>" ;
    $debug[] = $tags;
} );

}
add_shortcode( 'ccdebughook', 'cc_debughook' );


/**
* ClubCloud - Redirect non-admin users to home page if they are logged in
*
* This function is attached to the 'admin_init' action hook.
*/
function cc_redirect_non_admin_users() {
	if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	wp_redirect( home_url() );
	exit;
	}
}
add_action( 'admin_init', 'cc_redirect_non_admin_users' );

add_filter( 'big_image_size_threshold', '__return_false' );

/*
function allow_vendors_media_uploads() {
	$vendor_role = get_role('seller');
	
	// Ensure Vendors Media Upload Capability
	$vendor_role->add_cap('edit_posts');
	$vendor_role->add_cap('edit_post');
	$vendor_role->add_cap('edit_others_posts');
	$vendor_role->add_cap('edit_others_pages');
	$vendor_role->add_cap('edit_published_posts');
	$vendor_role->add_cap('edit_published_pages');
	$vendor_role->add_cap( 'upload_files' );
	}
	add_action( 'init', 'allow_vendors_media_uploads' );*/
