<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}


//ClubCloud - A Shortcode to Return the Correctly Formatted Username in Menus dealing with Merchants///////////////////////////////////////////////////////////////////////////////////
//This is meant to be the new universal formatting invite list
function cc_getname() {
	global $WCFM, $WCFMmp;//Check if User is a Vendor and if so, return their slug (as menu's render on Slugs)
	$user  = wp_get_current_user();
	$roles = ( array ) $user->roles;
//	return $roles[0];
	if ( $user->roles[0] == 'wcfm_vendor' ) {
		return $user->user_nicename;
		
		//return print_r($store_info);
	} else if ( $user->roles[0] == 'shop_staff' ) {
		$parentID   = $user->_wcfm_vendor;
		$store_user = wcfmmp_get_store( $parentID );
		$store_info = $store_user->get_shop_info();

		return $store_info['store_slug'];
	}

	//If they aren't a vendor then we simply return User Login
	return $user->user_nicename;
}

add_shortcode( 'ccname', 'cc_getname' );

//ClubCloud - A Shortcode to Return URL Sequences for Menus///////////////////////////////////////////////////////////////////////////////////
//This is meant to be the new universal formatting invite list
function cc_getnameurl( $atts = array() ) {
	extract( shortcode_atts( array(
		'pre'  => '',
		'type' => "1",
		'post' => ''
	), $atts ) );
	global $WCFM, $WCFMmp;//Check if User is a Vendor and if so, return their slug (as menu's render on Slugs)
	$user  = wp_get_current_user();
	$roles = ( array ) $user->roles;
//Switch Types
?>
	
<script type="text/javascript">
    var removeHeader = function() {
	$iframe = jQuery("#iframe1");

        $iframe.css('visibility', 'visible');

	var $head = $iframe.contents().find("head");
        var css = '<style type="text/css">' +
            '.yz-account-header{margin-bottom: 35px; background-color: #fff; display: none!important;}' +
            '</style>';
  	jQuery($head).append(css);
    };
</script>

<?php 


switch ( $type ) {
		case "1":
			//	return $roles[0];
			if ( $user->roles[0] == 'wcfm_vendor' ) {
				$store_user = wcfmmp_get_store( $user->ID );
				$store_info = $store_user->get_shop_info();

				return $pre . $store_info['store_slug'] . $post;
				//return print_r($store_info);
			} else if ( $user->roles[0] == 'shop_staff' ) {
				$parentID   = $user->_wcfm_vendor;
				$store_user = wcfmmp_get_store( $parentID );
				$store_info = $store_user->get_shop_info();

				return $pre . $store_info['store_slug'] . $post;
			}

			//If they aren't a vendor then we simply return User Login
			return $pre . $user->user_login . $post;

			break;
		case "2":
			//	return $roles[0];
			if ( $user->roles[0] == 'wcfm_vendor' ) {
				$store_user = wcfmmp_get_store( $user->ID );
				$store_info = $store_user->get_shop_info();

				return "<iframe style=\"visibility: hidden;\" onload=\"removeHeader()\" name=\"iframe1\" id=\"iframe1 \" src=\"/users/" . $store_info['store_slug'] . "/profile/edit/group/4/\" width=\"1600px\" height=\"900px\" frameborder=\"0\" scrolling=\"no\" align=\"left\"> </iframe>";
				//return print_r($store_info);
			} else if ( $user->roles[0] == 'shop_staff' ) {
				$parentID   = $user->_wcfm_vendor;
				$store_user = wcfmmp_get_store( $parentID );
				$store_info = $store_user->get_shop_info();

				return "<iframe style=\"visibility: hidden;\" onload=\"removeHeader()\" name=\"iframe1\" id=\"iframe1\" src=\"/users/" . $store_info['store_slug'] . "/profile/edit/group/4/\" width=\"1600px\" height=\"900px\" frameborder=\"0\" scrolling=\"no\" align=\"left\"> </iframe>";
			}

			//If they aren't a vendor then we simply return User Login
			return "<iframe style=\"visibility: hidden;\" onload=\"removeHeader()\" name=\"iframe1\" id=\"iframe1\" src=\"/users/" . $user->user_login . "/profile/edit/group/4/\" width=\"1600px\" height=\"900px\" frameborder=\"0\" scrolling=\"no\" align=\"left\"> </iframe>";

			break;
		case "3":
			//	return $roles[0];
			if ( $user->roles[0] == 'wcfm_vendor' ) {
				$store_user = wcfmmp_get_store( $user->ID );
				$store_info = $store_user->get_shop_info();
			/*	?>
				<script type="text/javascript">

        // Show/Hide Primary Nav Message
        jQuery('.yz-primary-nav-settings').click(function (e) {
            // e.preventDefault();
            // Get Parent Box.
            var settings_box = jQuery(this).closest('.yz-primary-nav-area');
            // Toggle Menu.
            settings_box.toggleClass('open-settings-menu');
            // Display or Hide Box.
            settings_box.find('.yz-settings-menu').fadeToggle(400);
        });

    </script><?php 

				var cssLink = document.createElement("link");
				cssLink.href = "style.css"; 
				cssLink.rel = "stylesheet"; 
				cssLink.type = "text/css"; 
				frames['iframe1'].document.head.appendChild(cssLink);
*/
				return "<iframe style=\"visibility: hidden;\" onload=\"removeHeader()\" name=\"iframe1\" id=\"iframe1\" src=\"/users/" . $store_info['store_slug'] . "/profile/edit/group/2/\" width=\"1600px\" height=\"900px\" frameborder=\"0\" scrolling=\"yes\" align=\"left\"> </iframe>";
				//return print_r($store_info);
			} else if ( $user->roles[0] == 'shop_staff' ) {
				$parentID   = $user->_wcfm_vendor;
				$store_user = wcfmmp_get_store( $parentID );
				$store_info = $store_user->get_shop_info();

				return "<iframe style=\"visibility: hidden;\" onload=\"removeHeader()\" name=\"iframe1\" id=\"iframe1\" src=\"/users/" . $store_info['store_slug'] . "/profile/edit/group/2/\" width=\"1600px\" height=\"900px\" frameborder=\"0\" scrolling=\"yes\" align=\"left\"> </iframe>";
			}

			//If they aren't a vendor then we simply return User Login
			return "<iframe style=\"visibility: hidden;\" onload=\"removeHeader()\" name=\"iframe1\" id=\"iframe1\" src=\"/users/" . $user->user_login . "/profile/edit/group/2/\" width=\"1600px\" height=\"900px\" frameborder=\"0\" scrolling=\"yes\" align=\"left\"></iframe>";

			break;
		case "4":
			return do_shortcode( '[elementor-template id="24849"]' );
			break;
		case "5":
			return do_shortcode( '[elementor-template id="24937"]' );
			break;
	}

}


add_shortcode( 'ccnameurl', 'cc_getnameurl' );




//ClubCloud - Shortcode to createImage of Anything as Menu Item
/**
 * Create Menu Shortcode.
 */
function cc_menulink( $url ) {

	// Get Logged-IN User ID.


	if ( $url == "" ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		$url            = $image[0];
	}

	ob_start();

	?>

    <div class="yz-primary-nav-area">

        <div class="yz-primary-nav-settings">
            <div class="yz-primary-nav-img" style="background-image: url(<?php echo $url; ?>)"></div>
            <span><?php
				echo get_bloginfo( 'name' ); ?></span>
			<?// backup of apply filters line above to enter within span tags php echo apply_filters( 'yz_account_avatar_shortcode_username', bp_core_get_username( $user_id ), $user_id ); ?>
			<?php if ( 'on' == yz_option( 'yz_disable_wp_menu_avatar_icon', 'on' ) ) : ?>
                <i class="fas fa-angle-down yz-settings-icon"></i><?php endif; ?>
        </div>

    </div>

    <script type="text/javascript">

        // Show/Hide Primary Nav Message
        jQuery('.yz-primary-nav-settings').click(function (e) {
            // e.preventDefault();
            // Get Parent Box.
            var settings_box = jQuery(this).closest('.yz-primary-nav-area');
            // Toggle Menu.
            settings_box.toggleClass('open-settings-menu');
            // Display or Hide Box.
            settings_box.find('.yz-settings-menu').fadeToggle(400);
        });

    </script>

	<?php

	return ob_get_clean();;
}

add_shortcode( 'ccmenulink', 'cc_menulink' );

//ClubCloud - Shortcode to create Store Details for Menu Item
/**
 * Create Store Shortcode.
 */
function cc_storelink( $url ) {

	// Get Logged-IN User ID.
	$user           = wp_get_current_user();
	$roles          = ( array ) $user->roles;
	$parentID       = $user->_wcfm_vendor;
	$store_user     = wcfmmp_get_store( $parentID );
	$store_info     = $store_user->get_shop_info();
	$store_gravatar = $store_user->get_avatar();


	if ( $url == "" ) {
		$url = $store_gravatar;
	}

	ob_start();

	?>

    <div class="yz-primary-nav-area">

        <div class="yz-primary-nav-settings">
            <div class="yz-primary-nav-img" style="background-image: url(<?php echo $url; ?>)"></div>
            <span><?php
				echo $store_info['store_name']; ?></span>
			<?// backup of apply filters line above to enter within span tags php echo apply_filters( 'yz_account_avatar_shortcode_username', bp_core_get_username( $user_id ), $user_id ); ?>
			<?php if ( 'on' == yz_option( 'yz_disable_wp_menu_avatar_icon', 'on' ) ) : ?>
                <i class="fas fa-angle-down yz-settings-icon"></i><?php endif; ?>
        </div>

    </div>

    <script type="text/javascript">

        // Show/Hide Primary Nav Message
        jQuery('.yz-primary-nav-settings').click(function (e) {
            // e.preventDefault();
            // Get Parent Box.
            var settings_box = jQuery(this).closest('.yz-primary-nav-area');
            // Toggle Menu.
            settings_box.toggleClass('open-settings-menu');
            // Display or Hide Box.
            settings_box.find('.yz-settings-menu').fadeToggle(400);
        });

    </script>

	<?php

	return ob_get_clean();;
}

add_shortcode( 'ccstorelink', 'cc_storelink' );

//Club Cloud - a function to reutn the URL of Site image or another picture

function cc_logo( $url ) {
	$url = "/wp-content/uploads/2021/01/cropped-Silver.png";

	return '<div class="yz-primary-nav-img" style="background-image: url(' . $url . ')"></div>';
}

add_shortcode( 'cclogo', 'cc_logo' );

//ClubCloud a Function to Get Slug from a Store////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_namedump( $atts = array() ) {
	extract( shortcode_atts( array(
		'type' => $type = 'store'
	), $atts ) );
	
	$user  = wp_get_current_user();
	return print_r($user);
}

add_shortcode( 'ccnamedump', 'cc_namedump' );
