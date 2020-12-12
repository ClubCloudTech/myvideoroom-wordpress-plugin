<?php
//Clubcloud-Front Page Switchshortcode  ccfrontpage/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// This shortcode changes the Front Page of the site to different templates depending on Xprofile setting of user 1 of site
function cc_callfrontpage()
{	$switchdata = xprofile_get_field_data(730,1); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
	//Handling a Blank Setting
	if($switchdata=="")
	{ $switchdata="Legal";		}
	switch ($switchdata) 												{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="16348"]');			 	    break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="21952"]');				break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="23126"]');				break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="22197"]');				break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="22199"]');		        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="22215"]');		        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="21953"]');		        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="22201"]');		        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="23717"]');		        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="22239"]');		        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="24421"]');		        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="23059"]');		        break;
		case "Spa":
			return do_shortcode ('[elementor-template id="22734"]');		        break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="22727"]');				break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="23241"]');				break;															}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccfrontpage', 'cc_callfrontpage');
//Clubcloud-Product Archive Main Page Switchshortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the product archives to different templates
function cc_callproductpage()
{	$switchdata = xprofile_get_field_data(730,1);//getting current page information to compare parent owners - using 730 which is Site setting for User 1
	//Handling a Blank Setting
	if($switchdata==""){
		$switchdata="Legal";		}
	switch ($switchdata) 						{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="23430"]');			   		break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="23146"]');				break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="23947"]');		        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="23430"]');			    break;
		case "Spa":
			return do_shortcode ('[elementor-template id="23430"]');			    break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="23390"]');				break;						}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccproductpage', 'cc_callproductpage');
//Clubcloud-Category Login Page Switch shortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Login Page archives to different templates
function cc_loginswitch()
{	$switchdata = xprofile_get_field_data(730,1); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if($switchdata=="")
	{ $switchdata="Law_firm";		}
	switch ($switchdata) 												{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="24892"]');		   		break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="24912"]');			break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="24912"]');			break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="24912"]');			break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="24892"]');	        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="24892"]');	        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="24912"]');	        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="24892"]');	        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="24892"]');	        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="24912"]');	        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="24912"]');	        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="24892"]');	        break;
		case "Spa":
			return do_shortcode ('[elementor-template id="24892"]');	        break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="24912"]');			break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="24892"]');			break;															}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccloginswitch', 'cc_loginswitch');
//Clubcloud-Category Register Page Switch shortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Register Page archives to different templates
function cc_registerswitch()
{	$switchdata = xprofile_get_field_data(730,1); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if($switchdata=="")
	{ $switchdata="Law_firm";		}
	switch ($switchdata) 												{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="24974"]');		   		break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="24974"]');			break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="24974"]');			break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="24974"]');			break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Spa":
			return do_shortcode ('[elementor-template id="24974"]');	        break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="24974"]');			break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="24974"]');			break;															}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccregisterswitch', 'cc_registerswitch');
//Clubcloud-Category Change Password Page Switch shortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Change Password Page archives to different templates
function cc_chgpwdswitch()
{	$switchdata = xprofile_get_field_data(730,1); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if($switchdata=="")
	{ $switchdata="Law_firm";		}
	switch ($switchdata) 												{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="25033"]');		   		break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="25033"]');			break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="25033"]');			break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="25033"]');			break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Spa":
			return do_shortcode ('[elementor-template id="25033"]');	        break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="25033"]');			break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="25033"]');			break;															}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccchgpwdswitch', 'cc_chgpwdswitch');
//Clubcloud-Category External Press Page Switchshortcode /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the Press External Page archives to different templates
function cc_callpressexternalpage()
{	$switchdata = xprofile_get_field_data(730,1); //getting current page information to compare parent owners - using 730 which is Site setting for User 1
//Handling a Blank Setting
	if($switchdata=="")
	{ $switchdata="Law_firm";		}
	switch ($switchdata) 												{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="23430"]');			 	    break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="24425"]');		        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Spa":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="23454"]');				break;															}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccpressexternalpage', 'cc_callpressexternalpage');
//Clubcloud-Category External Press Page Switchshortcode ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//This shortcode is used to switch the product archives to different templates
function cc_callcategorypage()
{	$switchdata = xprofile_get_field_data(730,1);
//Handling a Blank Setting
	if($switchdata==""){
		$switchdata="Law_firm";		}
	switch ($switchdata) 														{
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="23430"]');			   		break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Adult_Club":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Spa":
			return do_shortcode ('[elementor-template id="23430"]');		        break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="23430"]');				break;
		case "Magazine":
			return do_shortcode ('[elementor-template id="23386"]');				break;															}
	return " The switch found no template for this selection type<br>";
}
add_shortcode('cccategorypage', 'cc_callcategorypage');
/* ClubCloud - Display Configuration of Admin Panel based on Buddypress XProfile Parameters for Site////////////////////////////////////////////////////////////////////////////////
*Usage: To change the configuration and look and feel of back end site configuration */
function cc_adminswitch()
{	   global $WCFM, $WCFMmp;
	$xprofile_setting = xprofile_get_field_data( 1291,1);   //1291 is the field selection type of Buddypress Group 3 Admin Control setting, and User 1 is the control user
	//set default for switch case (for backup case)
	switch ($xprofile_setting) 	   {
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="16348"]');		   break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="21952"]');		   break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="22217"]');		   break;
		case "Grocery_Store":
			return do_shortcode ('[elementor-template id="22197"]');		   break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="22199"]');		   break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="22215"]');		   break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="21953"]');	       break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="22201"]');		   break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="22205"]');		   break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="22239"]');		   break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="22444"]');		   break;
		case "Grocery_Store_Simple":
			return do_shortcode ('[elementor-template id="22731"]');		   break;
		case "Spa":
			return do_shortcode ('[elementor-template id="22734"]');		   break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="22727"]');		   break;					   }
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccadminswitch', 'cc_adminswitch');
/* ClubCloud - Display Storefront Layout - Change the look of each individual store	//////////////////////////////////////////////////////////////////////////////////////////
*Usage: In all front end storefront locations where seamless permissions video is needed. */
function cc_posttemplateswitch()
{	   global $WCFM, $WCFMmp;
	$post = get_post();//getting current page information to compare parent owners
	//Get variables from Current Post
	$currentpost_store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$user_id = $currentpost_store_id;
	$field_name_or_id=582;//use Current Parent Page to get XProfile Setting for that merchant
	$xprofile_setting = xprofile_get_field_data( $field_name_or_id, $user_id );
	//set default for switch case (for backup case)
	$switchdef=true;
	switch ($xprofile_setting) 	   {
		case "E_Commerce_Marketplace":
			return do_shortcode ('[elementor-template id="16348"]');		   break;
		case "Jet":
			return do_shortcode ('[elementor-template id="25741"]');		   break;
		case "Trade_Show":
			return do_shortcode ('[elementor-template id="21952"]');		   break;
		case "Law_Firm":
			return do_shortcode ('[elementor-template id="22217"]');		   break;
		case "Cake_Store":
			return do_shortcode ('[elementor-template id="22197"]');		   break;
		case "Clothing_Store":
			return do_shortcode ('[elementor-template id="22199"]');		   break;
		case "Medical_Practice":
			return do_shortcode ('[elementor-template id="22215"]');		   break;
		case "Press_Office":
			return do_shortcode ('[elementor-template id="21953"]');	       break;
		case "Night_Club":
			return do_shortcode ('[elementor-template id="22201"]');		   break;
		case "Sports_Club":
			return do_shortcode ('[elementor-template id="22205"]');		   break;
		case "Political_Profile":
			return do_shortcode ('[elementor-template id="22239"]');		   break;
		case "Professional_Services":
			return do_shortcode ('[elementor-template id="22444"]');		   break;
		case "Grocery_Store_Simple":
			return do_shortcode ('[elementor-template id="22731"]');		   break;
		case "Spa":
			return do_shortcode ('[elementor-template id="22734"]');		   break;
		case "Law_Firm_Modern":
			return do_shortcode ('[elementor-template id="22727"]');		   break;					   }
	//sets default case in case no selection by merchant
	if ($switchdef==true){
		echo do_shortcode ('[elementor-template id="16348"]');		   }
	return " The switch found no template for this selection type<br>";
}
add_shortcode('cctemplateswitch', 'cc_posttemplateswitch');
/*Clubcloud - a shortcode to switch Join Pages to different club levels////////////////////////////////////////////////////////////////////////////////////////////////////////
*Switches Join1..n pages by Club wide selection to different join page templates
*Attributes - Join Page Number1..3*/
function cc_joinswitch($atts = array())
{	extract(shortcode_atts(array(
	'joinid' =>	'1'	,
	'mode' =>	'shortcode',
	'name' =>	'1'		 ), $atts));
	$xprofile_setting = xprofile_get_field_data(730,1);	//X-profile setting for user 1's 730 field (main site setting mode)
	//return "Join ID: ".$joinid." X-Profile ".$xprofile_setting."<br>";
	switch ($xprofile_setting)
	{   case "E_Commerce_Marketplace":
		switch ($name)
		{ case "marketplace": return "Marketplace";					 break;
			case "news": return "Community News"; 					 break;
			case "clubhub": return  "Market Hub";						 break;
			case "storefront": return  "My Store"; 					 break;
			case "marketplacesingular": return  "Marketplace"; 		 break;
			case "lounge": return  "Lounge"; 							 break;
			case "lobby": return  "Lobby"; 							 break;
			case "clubtype": return  "Marketplace"; 					 break;		}
		switch ($joinid)
		{ case "1":
			if ($mode=="menu"){return "How It Works";}
			return do_shortcode ('[elementor-template id="24727"]'); break;
			case "2":
				if ($mode=="menu"){return "Open Your Store";}
				return do_shortcode ('[elementor-template id="24818"]'); break;			}
		break;
		case "Trade_Show":
			switch ($name)
			{ case "marketplace": return "Exhibition Hall"; 			break;
				case "news": return "Event News"; 					    break;
				case "clubhub": return  "Event Hub";					    break;
				case "storefront": return  "My Event Booth";			    break;
				case "marketplacesingular": return  "Hall"; 				break;
				case "lounge": return  "Lounge"; 							break;
				case "lobby": return  "Lobby"; 						    break;
				case "clubtype": return  "Event";							break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "Register to Attend";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "Exhibit";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "Sponsor";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Law_Firm":
			switch ($name)
			{ case "marketplace": return "Practice Services";			 break;
				case "news": return  "From the Firm"; 					 break;
				case "clubhub": return  "Firm Hub"; 						 break;
				case "storefront": return  "My Practice"; 				 break;
				case "marketplacesingular": return  "Services"; 			 break;
				case "lounge": return  "Office"; 							 break;
				case "lobby": return  "Reception"; 						 break;
				case "clubtype": return  "Practice"; 						 break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "Our Services";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Grocery_Store":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Clothing_Store":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Medical_Practice":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Press_Office":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Night_Club":
			switch ($name)
			{ case "marketplace": return "Club Marketplace"; 					break;
				case "news": return "Whats Hot"; 									break;
				case "clubhub": return "Club Hub";								break;
				case "storefront": return "My Storefront"; 						break;
				case "marketplacesingular": return  "Marketplace"; 				break;
				case "lounge": return  "Lounge"; 									break;
				case "lobby": return "Lobby"; 									break;
				case "clubtype": return "Club"; 									break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]');			 break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]');			 break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); 			 break;			}
			break;
		case "Sports_Club":
			switch ($name)
			{ case "marketplace": return "Coaching Marketplace";					 break;
				case "news": return "News Feed"; 										 break;
				case "clubhub": return  "The Clubhouse";								 break;
				case "storefront": return  "My Storefront"; 							 break;
				case "marketplacesingular": return  "Coaching Marketplace";			 break;
				case "lounge": return  "Lounge"; 										 break;
				case "lobby": return  "Lobby";										 break;
				case "clubtype": return  "Club";										 break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "Find a Coach";}
				return do_shortcode ('[elementor-template id="24703"]'); break;
				case "2":
					if ($mode=="menu"){return "Register as a Coach";}
					return do_shortcode ('[elementor-template id="24674"]'); break;
				case "3":
					if ($mode=="menu"){return "Coaching CPD";}
					return do_shortcode ('[elementor-template id="24718"]'); break;	}
			break;
		case "Political_Profile":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Professional_Services":
			switch ($name)
			{ case "marketplace": return "Virtual Offices"; break;
				case "news": return "Network"; break;
				case "clubhub": return "Innovation Hub"; break;
				case "storefront": return "My Virtual Office"; break;
				case "marketplacesingular": return  "Virtual Offices"; break;
				case "lounge": return "Lounge"; break;
				case "lobby": return "Lobby"; break;
				case "clubtype": return "Innovation"; break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "Become an Innovator";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "Get Your Virtual Office";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "Your Events Platform";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Grocery_Store_Simple":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Spa":
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Law_Firm_Modern":
			switch ($name)
			{ case "marketplace": return "Practice Services"; break;
				case "news": return  "From the Firm"; break;
				case "clubhub": return  "Firm Hub"; break;
				case "storefront": return  "My Practice"; break;
				case "marketplacesingular": return  "Services"; break;
				case "lounge": return  "Office"; break;
				case "lobby": return  "Reception"; break;
				case "clubtype": return  "Practice"; break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "How It Works";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
		case "Adult_Club":
			switch ($name)
			{ case "marketplace": return "Adult Marketplace"; 						break;
				case "news": return "Whats Hot";										break;
				case "clubhub": return "Club Hub";								    break;
				case "storefront": return "My Storefront";							break;
				case "marketplacesingular": return  "Adult Marketplace";				break;
				case "lounge": return  "Lounge"; 										break;
				case "lobby": return "Lobby"; 										break;
				case "clubtype": return "Club";										break;		}
			switch ($joinid)
			{ case "1":
				if ($mode=="menu"){return "Become a Member";}
				return do_shortcode ('[elementor-template id="24727"]'); break;
				case "2":
					if ($mode=="menu"){return "Open Your Store";}
					return do_shortcode ('[elementor-template id="24818"]'); break;
				case "3":
					if ($mode=="menu"){return "How It Works";}
					return do_shortcode ('[elementor-template id="24727"]'); break;			}
			break;
	}//end main switch case
	return " The switch found no template for this selection type<br>";
}
add_shortcode('ccjoinswitch', 'cc_joinswitch');
/*Clubcloud - a shortcode to switch Club Main Lounge to different Subscription Levels////////////////////////////////////////////////////////////////////////////////////////////////////////
*This code switches to the correct subscription template based on subscriber, and handles Admin or Special WP roles.
*This is needed as different subscription levels and wordpress roles need different dashboards.*/
function cc_loungeswitch()
{	$post = get_post();//getting current page information to compare parent owners
	$user = wp_get_current_user();//Fetch User Parameters and Roles
	$roles = ( array ) $user->roles;
	//Handling Admin Roles - sending them to Admin Lounge
	if($user->roles[0]=='administrator'){
		return do_shortcode ('[elementor-template id="20006"]');		}
	//If user is non-admin Then get membership level and Re-create Array from Wordpress text input
	$membership_level = get_user_meta ($user->id,'ihc_user_levels');
	$memlev = explode(',',$membership_level[0]);
	$array_count = count($memlev);
	//Template Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
	for ($x = 0; $x <= $array_count-1; $x++)
	{	switch ($memlev[$x]) 												{
		case "6": //gold
			return do_shortcode ('[elementor-template id="17076"]');				   break;
		case "5"://silver
			return do_shortcode ('[elementor-template id="17078"]');		       break;
		case "4"://bronze
			return do_shortcode ('[elementor-template id="17081"]');               break;
		case "9"://platinum
			return do_shortcode ('[elementor-template id="17230"]');   			   break;
		case "10"://diamond
			return do_shortcode ('[elementor-template id="17225"]');		       break;
		case "11"://Site Admin
			return do_shortcode ('[elementor-template id="20006"]');		       break;
		case "12"://Ambassador
			return do_shortcode ('[elementor-template id="17502"]');		       break;
		case "16"://Vendor Staff
			return do_shortcode ('[elementor-template id="22906"]');			   break;														}
	}	//sets default case in case no selection by merchant
	if ($switchdef==true){
		echo do_shortcode ('[elementor-template id="17081"]');			}
}
add_shortcode('ccloungeswitch', 'cc_loungeswitch');

//ClubCloud - Function to Disable Group 3  (Store Settings) visibility///////////////////////////////////////////////////////////////////////////////////////////////////////
function g3bpfr_hide_profile_field_group( $groups )
{	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$blockviewpermission=true;
	if($user->id==1)
	{	$blockviewpermission=false;			}
	if ( $blockviewpermission==true)
	{	$remove_groups = array( 3 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj ) 					{
			if ( in_array( $group_obj->id, $remove_groups )  ) {
				unset( $groups[$key] );}							}
		$groups = array_values( $groups );
	}
	return $groups;
}
add_filter( 'bp_profile_get_field_groups', 'g3bpfr_hide_profile_field_group' );
//ClubCloud - Add Menu Deletions for Items for Non Storevendors///////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Blocking the tabs (in other functions) doesnt delete visibility in case of direct URL access, so good security to delete the fields
//Use the Xpprofile Field ID to remove any menu item that non-admins or storeowners shouldnt see.
function mnbpfr_hide_profile_field_group( $retval ) {
	if ( bp_is_active( 'xprofile' ) ) :
		// hide profile group/field to all except admin
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;
		$blockviewpermission=true;
		if($user->roles[0]=='wcfm_vendor' or $user->roles[0]=='administrator')
		{	$blockviewpermission=false;			}
		else if(is_super_admin())
		{	$blockviewpermission=false;		}
		if($blockviewpermission==true)
		{	$retval['exclude_fields'] = '673,667,669,582,569,649,730';  //exclude fields, separated by comma
			$retval['exclude_groups'] = '2'; 			}
		return $retval;
	endif;
}
add_filter( 'bp_after_has_profile_parse_args', 'mnbpfr_hide_profile_field_group' );
//ClubCloud - Function to Disable Non Admin Views of Site Control //////////////////////////////////////////////////////////////////////////////////////////////////////////////
//We dont want to show Store customisation settings to Non-Merchants
function bpfr_hide_profile_field_group( $groups )
{	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$auth=false;
	//case moderate own profile
	if ( bp_is_user_profile_edit() && ! current_user_can( 'bp_moderate' ) ){
		$auth=true;	}
	//case admin
	if($user->roles[0]=='wcfm_vendor' OR $user->roles[0]=='administrator')
	{$auth=true;	}
	if ($auth==false )
	{    $remove_groups = array( 3 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj )
		{if ( in_array( $group_obj->id, $remove_groups )  )
		{unset( $groups[$key] );          }
		}
		$groups = array_values( $groups );
	}    return $groups;
}
add_filter( 'bp_profile_get_field_groups', 'bpfr_hide_profile_field_group' );
//ClubCloud - Function to Disable Group 2 Non Merchant visibility ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//We dont want to show Store customisation settings to Non-Merchants
function mbpfr_hide_profile_field_group( $groups )
{	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$blockviewpermission=true;
	if($user->roles[0]=='wcfm_vendor')
	{	$blockviewpermission=false;			}
	if($user->roles[0]=='administrator')
	{		$blockviewpermission=false;		}
	//echo $blockviewpermission."Block Status<br>";
	if ( $blockviewpermission==true)
	{	$remove_groups = array( 2 ); // Put the IDs of the groups to remove here, separated by commas.
		foreach ( $groups as $key => $group_obj )
		{	if ( in_array( $group_obj->id, $remove_groups )  ) {
			unset( $groups[$key] );				}
		}
		$groups = array_values( $groups );
	}
	return $groups;
}
add_filter( 'bp_profile_get_field_groups', 'mbpfr_hide_profile_field_group' );
/*ClubCloud  - Customise My Account Page. /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
* This function modifies the default MyAccount page.  */
add_filter ( 'woocommerce_account_menu_items', 'cc_myaccountchange', 40 );
function cc_myaccountchange( $menu_links )
{	$menu_links = array_slice( $menu_links, 0, 1, true )
                   + array( 'cc_mysubs' => 'My Subscriptions' )
                   + array_slice( $menu_links, 1, NULL, true );
	return $menu_links; }
add_action( 'init', 'cc_add_menu_endpoint' );    /* Register Permalink Endpoint  */
function cc_add_menu_endpoint()
{	add_rewrite_endpoint( 'cc_mysubs', EP_PAGES ); }
/* Content for the new page in My Account endpoint */
add_action( 'woocommerce_account_cc_mysubs_endpoint', 'cc_subscriptions_endpoint' );
function cc_subscriptions_endpoint() {
	echo do_shortcode ('[elementor-template id="24402"]');
}
/*ClubCloud  - Add to Cart Redirect Function. /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
* In order to use product straight to check out use ?add-to-cart=%%ProductPOSTID%% eg ?add-to-cart=34553
* Used to be able to send Subscriptions Straight to Checkout so one Click Buy Works*/
add_filter('woocommerce_add_to_cart_redirect','straight_to_checkout');
function straight_to_checkout()
{  $checkouturl = WC()->cart->get_checkout_url();
	return $checkouturl;
}
$cc_clubcloud_directory = content_url('/clubcloud');
//ClubCloud  - Add Order Action to My Order Page////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_add_my_account_order_actions( $actions, $order )
{ $actions['video'] = array(
	'url'  => '/connect/?&order=' . $order->get_order_number(),
	'name' => __( 'Video Room', 'my-textdomain' ),
);
	return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'cc_add_my_account_order_actions', 10, 2 );
//Club Cloud Menu Name - may be deprecated in favour of ccname - which handles the edge case of store owners////////////////////////////////////////////////////////////////////////
function cc_menu()
{ global $WCFM, $WCFMmp;
	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	if($user->roles[0]=='wcfm_vendor')	{
		$store_user      = wcfmmp_get_store( $user->ID);
		$store_info      = $store_user->get_shop_info();
		$store_data		 = $store_info['store_slug'];
		return $store_data;		}
	//If they aren't a vendor then we simply return User Login- if you need to handle Staff use ccgetname
	return $user->user_login;
}
add_shortcode('ccmenu', 'cc_menu');
//ClubCloud - Action Filters to Implement a Video Hub system in WCFM Elementor to deploy a video consult room/////////////////////////////////////////////////////////////////////////
//WCFM Template to Implement Video Hub Tabbed menu
add_action( 'wcfmmp_rewrite_rules_loaded', function( $wcfm_store_url ) {
	global $tab ;
	global $template;
	add_rewrite_rule( $wcfm_store_url.'/([^/]+)/video_storefront?$', 'index.php?'.$wcfm_store_url.'=$matches[1]&video_storefront=true', 'top' );
	add_rewrite_rule( $wcfm_store_url.'/([^/]+)/video_storefront/page/?([0-9]{1,})/?$', 'index.php?'.$wcfm_store_url.'=$matches[1]&paged=$matches[2]&video_storefront=true', 'top' );	}, 50 );
add_filter( 'query_vars', function( $vars ) {
	$vars[] = 'video_storefront';
	return $vars;	}, 50 );
add_filter( 'wcfmmp_store_tabs', function( $store_tabs, $store_id ) {
	$store_tabs['video_storefront'] = 'Video Hub';
	return $store_tabs;	}, 50, 2 );
add_filter( 'wcfmp_store_tabs_url', function( $store_tab_url, $tab ) {
	if( $tab == 'video_storefront' ) {
		$store_tab_url .= 'video_storefront';	}
	return $store_tab_url;	}, 50, 2 );
add_filter( 'wcfmp_store_default_query_vars', function( $query_var ) {
	global $WCFM, $WCFMmp;
	if ( get_query_var( 'video_storefront' ) ) {
		$query_var = 'video_storefront';	}
	return $query_var;	}, 50 );
//ClubCloud - A Shortcode to Return the Correctly Formatted Username in Menus dealing with Merchants///////////////////////////////////////////////////////////////////////////////////
//This is meant to be the new universal formatting invite list
function cc_getname()
{ global $WCFM, $WCFMmp;//Check if User is a Vendor and if so, return their slug (as menu's render on Slugs)
	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	if($user->roles[0]=='wcfm_vendor')	{
		$store_user      = wcfmmp_get_store( $user->ID);
		$store_info      = $store_user->get_shop_info();
		$store_data		 = $store_info['store_slug'];
		return $store_data;		}
	else if ($user->roles[0]=='shop_staff')	{
		$parentID= $user->_wcfm_vendor;
		$store_user      = wcfmmp_get_store($parentID);
		$store_info      = $store_user->get_shop_info();
		return $store_info['store_slug'];	}
	//If they aren't a vendor then we simply return User Login
	return $user->user_login;}
add_shortcode('ccname', 'cc_getname');
//ClubCloud A Function to do logout//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_logout() {
	return wp_logout_url( home_url() );
}
add_shortcode('cclogout', 'cc_logout');

//ClubCloud A Function to return Staff Store Parent Name//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_getmystore()
{	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$parentID= $user->_wcfm_vendor;
	$store_user      = wcfmmp_get_store($parentID);
	$store_info      = $store_user->get_shop_info();
	return $store_info['store_name'];
}
add_shortcode('ccgetmystore', 'cc_getmystore');
// ClubCloud - A Shortcode to extract the Current Store name and format it correctly///////////////////////////////////////////////////////////////////////////////////////////////////
// Used by Merchant Pages to generate Hyper-links for Video  - the WCFM shortcode doesnt format the Store Name Correctly
//May be deprecated by Use of CCname
function cc_getstore()
{	$post = get_post();
	global $WCFM, $WCFMmp;
	$store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$store_user      = wcfmmp_get_store( $store_id );
	$store_info      = $store_user->get_shop_info();
	$store_name      = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'wc-multivendor-marketplace' );
	$store_name      = apply_filters( 'wcfmmp_store_title', $store_name , $store_id );
	return $store_name;
}
add_shortcode('ccstore', 'cc_getstore');
//ClubCloud a Function to Get Slug from a Store////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cc_getslug()
{ $post = get_post();
	global $WCFM, $WCFMmp;
//get vendor from store
	$store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$store_user      = wcfmmp_get_store( $store_id );
	$store_info      = $store_user->get_shop_info();
	$store_data= $store_info['store_slug'];
	return $store_data;
}
add_shortcode('ccslug', 'cc_getslug');
/*Clubcloud - a shortcode to switch entry Lobby ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*This code switches to the correct subscription template based on subscriber, and handles Admin or Special WP roles.*/
function cc_lobbyswitch()
{	$post = get_post();//getting current page information to compare parent owners
	$user = wp_get_current_user();  //Fetch User Parameters and Roles To make Routing Decisions
	$userid= $user->id;
	$roles = ( array ) $user->roles;
	//Handling Any Role Type that was registered - add new role cases here.
	switch ($user->roles[0]) {
		case "shop_staff":
			return do_shortcode ('[elementor-template id="22918"]');
			break;		}
	//Get Call for the Lobby Registration type from Xprofile field 1268 which is the Pre-Reg type field
	$switchdata = xprofile_get_field_data(1268,$userid);
	if ($switchdata==""){ return "Xprofile return failure - no entry found<br>";}//trapping blank return
	switch ($switchdata)
	{case "Club-School":
		return do_shortcode ('[elementor-template id="24849"]');			 	    break;
		case "Player":
			return do_shortcode ('[elementor-template id="24849"]');				break;
		case "Coach":
			return do_shortcode ('[elementor-template id="24896"]');				break;
		case "Team":
			return do_shortcode ('[elementor-template id="24849"]');				break;
		case "CPD":
			return do_shortcode ('[elementor-template id="24937"]');				break;}
	//Otherwise - Return default registered user - Sales Template
	return do_shortcode ('[elementor-template id="22921"]');
}
add_shortcode('cclobbyswitch', 'cc_lobbyswitch');
/*Clubcloud - A function to Switch Video Consultation Room Templates by Merchant Selection/////////////////////////////////////////////////////////////////////////////////////////////////////
# A Merchant selects their Consult room preference and the Shortcode switches template */
function cc_callconsult()
{ global $WCFM, $WCFMmp, $bp;	//calling in WCFM globals so we can get their merchant variables
	$post = get_post(); //getting current page information to compare parent owners
	//Fetch User Parameters and Roles
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles; 	}
	/*get meta data from currently logged in user and return the parent vendor id
	We use this to know if a user is a child merchant/staff etc*/
	$my_vendor_id = get_user_meta ($user->id,'_wcfm_vendor',true);
	$my_owner_id = get_current_user_id();
	$shop_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $my_vendor_id );
//get parent post ID to understand if we are in our home store or someone elses
	$currentpost_store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$currentpost_store_user      = wcfmmp_get_store( $currentpost_store_id );
	$currentpost_store_info      = $currentpost_store_user->get_shop_info();
	$user_id = $currentpost_store_id;
	$field_name_or_id=569;	//569 is the field number of the Xprofile setting of Control Video Storefront, 649 is Private Consultation Room
//Get xprofile setting from Merchant Settings
	$xprofile_setting= xprofile_get_field_data( $field_name_or_id, $user_id );
//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="office2";}
}
add_shortcode('ccconsult', 'cc_callconsult');
/* ClubCloud - Display Storefront Video based on Buddypress XProfile Parameters for Merchants and Visitors//////////////////////////////////////////////////////////////////////////////////////
*	Usage: In all front end storefront locations where seamless permissions video is needed. */
function cc_callstorefront() {
	//calling in WCFM globals so we can get their merchant variables
	global $WCFM, $WCFMmp,$bp;
	$post = get_post();	//getting current page information to compare parent owners
	//Fetch User Parameters and Roles
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;  	}
	/*get meta data from currently logged in user and return the parent vendor id
	We use this to know if a user is a child merchant/staff etc*/
	$my_vendor_id = get_user_meta ($user->id,'_wcfm_vendor',true);
	$my_owner_id = get_current_user_id();
	$shop_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $my_vendor_id );
	//get parent post ID to understand if we are in our home store or someone elses
	$currentpost_store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
	$currentpost_store_user      = wcfmmp_get_store( $currentpost_store_id );
	$currentpost_store_info      = $currentpost_store_user->get_shop_info();
	$user_id = $currentpost_store_id;
	$field_name_or_id=569;//569 is the field number of the Xprofile setting of Control Video Storefront, 649 is Private Consultation Room
	$field_quarantine=667;//667 is the quarantine field number from XProfile
	//Get xprofile setting from Merchant Settings for Main Video
	$xprofile_setting= xprofile_get_field_data( $field_name_or_id, $user_id );
	$xprofileq_setting= xprofile_get_field_data( $field_quarantine, $user_id );
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="office2";		}
	if($xprofileq_setting==""){
		$xprofileq_setting="sofa1";		}
	if ($xprofile_setting=="hall1")//intercept event templates by merchants and remove auth
	{ $bypass_security=true;
	}
	//Give Administrators or Store Manager Full Rights

	if($user->roles[0]=='administrator' OR $user->roles[0]=='store_manager')
	{	//in case Admin has their own store
		if($my_owner_id==$currentpost_store_id)
		{	$store_id		 = $currentpost_store_id;
			$store_user      = wcfmmp_get_store( $store_id );
			$store_info      = $store_user->get_shop_info();
			$store_name      = $store_info['store_slug'];
			return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'" auth=true]');	}
		else { //admin gets store owner view
			$store_id = $currentpost_store_id;
			$store_user      = wcfmmp_get_store( $store_id );
			$store_info      = $store_user->get_shop_info();
			$store_name      = $store_info['store_slug'];
			return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'" auth=true]');	}
	}
	//Switch Store Owner from Staff and Others
	//echo Check is a WCFM vendor, or store staff
	if($user->roles[0]=='wcfm_vendor' AND $my_owner_id==$currentpost_store_id)	{
		//case of an Owner in their own store
		$store_id		 = $my_vendor_id;
		$store_user      = wcfmmp_get_store( $store_id );
		$store_info      = $store_user->get_shop_info();
		$store_name      = $store_info['store_slug'];
		return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'" auth=true]'); 		}
	//case of a Staff Member in their own store
	elseif($user->roles[0]=='shop_staff' AND $my_vendor_id==$currentpost_store_id)			{
		$store_id = $my_vendor_id;
		$store_user      = wcfmmp_get_store( $store_id );
		$store_info      = $store_user->get_shop_info();
		$store_name      = $store_info['store_slug'];
		return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'" auth=true]');		}
	//now we have removed Storeowner/Staff in their own store case we deal with Storeowners and Staff in other people stores
	elseif($user->roles[0]=='shop_staff'OR $user->roles[0]=='wcfm_vendor')
	{	$store_id		 = $currentpost_store_id;
		$store_user      = wcfmmp_get_store( $store_id );
		$store_info      = $store_user->get_shop_info();
		$store_name      = $store_info['store_slug'];
		//echo "this means Competitor Role code ran";
		if ($bypass_security==true)
		{return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'"cc_is_event=true]');		}
		else {return do_shortcode('[clubvideo cc_event_id="store-'.$store_name.'-vendorvisitor" cc_plan_id="'.$xprofileq_setting.'"]'); }
	}
	//this person is not a merchant, admin, or Storemanager - so give them the normal store experience
	else{	$store_id = $currentpost_store_id;
		$store_user      = wcfmmp_get_store( $store_id );
		$store_info      = $store_user->get_shop_info();
		$store_name      = $store_info['store_slug'];
		return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'" cc_enable_lobby=false]');		}
}
add_shortcode('ccstorefrontvideo', 'cc_callstorefront');

/* ClubCloud - Display Merchant Control Centre Video based on Buddypress XProfile Parameters for Merchants//////////////////////////////////////////////////////////////////////////////////////
*  Usage: In all front end Merchant Control Centre locations where Access to Storefront is needed*/
function cc_merchantvideo() {
	//calling in WCFM and BP globals so we can get their merchant variables
	global $WCFM, $WCFMmp;
	//getting current page information to compare parent owners
	$post = get_post();
	if( is_user_logged_in() )
	{	$user = wp_get_current_user();
		$roles = ( array ) $user->roles; 		}
	//Extract Correct Shop Parent ID from Logged in User
	$xprofile_field_num=569; 	//569 is the field number of the Xprofile setting of Control Video Storefront, 649 is Private Consultation Room
	$my_vendor_id = get_user_meta ($user->id,'_wcfm_vendor',true);//this filter returns staff - if not staff we add owner ID.
	if ($my_vendor_id==""){ $my_vendor_id=get_current_user_id();}
	$xprofile_setting= cc_xprofile_build($xprofile_field_num,0,$my_vendor_id);
	$store_name=cc_orderinfo_by_booking(0,"store_slug",$my_vendor_id);
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting=="")
	{	$xprofile_setting="office2";		}
	return do_shortcode('[clubvideo cc_event_id="store-' . $store_name . '" cc_plan_id="'.$xprofile_setting.'" auth=true cc_enable_cart=true]');
}
add_shortcode('ccmerchantvideo', 'cc_merchantvideo');
/*Clubcloud - A Shortcode for the VIP Boardroom View to Switch by Xprofile Setting - Member////////////////////////////////////////////////////////////////////////////////////////////////////
*This is used for the Member Backend entry pages to access their preferred Video Layout - it is paired with the ccboardroomvideoguest function*/
function cc_boardroomvideomember()
{ global $bp;//add Global BuddyPress Functions
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$userid = $user->ID;  		}
	$fieldnum=801;//801 is the Setting in xprofile for Video Selection for VIP Boardroom
	//Get xprofile setting from Merchant Settings for Main Video
	$xprofile_setting= xprofile_get_field_data( $fieldnum, $userid);
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="boardroom1";		}
	return do_shortcode('[clubvideo cc_event_id="username" cc_plan_id="'.$xprofile_setting.'" cc_enable_lobby=true auth=true]');
}
add_shortcode('ccboardroomvideomember', 'cc_boardroomvideomember');
/*Clubcloud - A Shortcode for the Boardroom VIP View to Switch by Xprofile Setting - Guest/////////////////////////////////////////////////////////////////////////////////////////////////////
* This is used for the Guest entry pages to access the Member Selected Video Layout - it is paired with the ccboothvideomember function
* It accepts hostname as an argument which it gets from the Guest page URL get request parameter */
function cc_boardroomvideoguest($atts = array())
{	 extract(shortcode_atts(array(
	'hostname' => 'defaultunknownhost',
	'urlcheck' => false
), $atts));
	global $bp;//add Global BuddyPress Functions
	$user= get_user_by('login',$hostname);
	$userid=$user->ID;
	if ($urlcheck==true && $userid=="")
	{	return " Invalid Member or Host Name ";	}
	else if ($urlcheck==true && $userid!=""){
		return $hostname;	}
	if ($userid=="")
	{	echo "<br>";
		echo '<div style="font-size:5.25em;color:black">Member Unknown</div>';
		echo '<div style="font-size:2.25em;color:black">This Hostname Has not been found in the Member Registry, Its unlikely anyone can join this room</div>';
		echo "<br><br>";
		echo '<div style="font-size:2.25em;color:black">Please Return to Meeting reception and Check your Member name again</div>';	   }
	$fieldnum=801;//801 is the Setting in xprofile for Video Selection
	//Get xprofile setting from Merchant Settings for Main Video
	$xprofile_setting= xprofile_get_field_data( $fieldnum, $userid);
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="boardroom1";		}
	return do_shortcode('[clubvideo cc_plan_id="'.$xprofile_setting.'"]');
}
add_shortcode('ccboardroomvideoguest', 'cc_boardroomvideoguest');
//Clubcloud - A Shortcode for the Booth View to Switch by Xprofile Setting - Member///////////////////////////////////////////////////////////////////////////////////////////////////////
//This is used for the Member Backend entry pages to access their preferred Video Layout - it is paired with the ccboothvideoguest function
function cc_boothvideomember()
{	global $bp; 	//add Global BuddyPress Functions
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$userid = $user->ID;  		}
	$fieldnum=738;	//738 is the Setting in xprofile for Video Selection
	$xprofile_setting= xprofile_get_field_data( $fieldnum, $userid);	//Get xprofile setting from Merchant Settings for Main Video
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="booth1";		}
	return do_shortcode('[clubvideo cc_event_id="username" cc_plan_id="'.$xprofile_setting.'" cc_enable_lobby=true auth=true]');
}
add_shortcode('ccboothvideomember', 'cc_boothvideomember');
/*Clubcloud - A Shortcode for the Booth View to Switch by Xprofile Setting - Guest/////////////////////////////////////////////////////////////////////////////////////////////////////////
* This is used for the Guest entry pages to access the Member Selected Video Layout - it is paired with the ccboothvideomember function
* It accepts hostname as an argument which it gets from the Guest page URL get request parameter */
function cc_boothvideoguest($atts = array())
{	 extract(shortcode_atts(array(
	'hostname' => 'defaultunknownhost',
	'urlcheck' => false
), $atts));
	global $bp;	//add Global BuddyPress Functions
	$user= get_user_by('login',$hostname);
	$userid=$user->ID;
	if ($urlcheck==true && $userid=="")			{
		return " Invalid Guest Name ";	}
	else if ($urlcheck==true && $userid!=""){
		return $hostname;	}
	if ($userid==""){
		echo "<br>";
		echo '<div style="font-size:5.25em;color:black">Member Unknown</div>';
		echo '<div style="font-size:2.25em;color:black">This Hostname Has not been found in the Member Registry, Its unlikely anyone can join this room</div>';
		echo "<br><br>";
		echo '<div style="font-size:2.25em;color:black">Please Return to Meeting reception and Check your Member name again</div>';	   }
	$fieldnum=738;//738 is the Setting in xprofile for Video Selection
	$xprofile_setting= xprofile_get_field_data( $fieldnum, $userid);//Get xprofile setting from Merchant Settings for Main Video
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="booth1";		}
	return do_shortcode('[clubvideo cc_plan_id="'.$xprofile_setting.'"]');
}
add_shortcode('ccboothvideoguest', 'cc_boothvideoguest');
//Clubcloud - A Shortcode for the Management Boardroom View to Switch by Xprofile Setting - Member/////////////////////////////////////////////////////////////////////////////////////////
//This is used for the Member Backend entry pages to access their preferred Video Layout - it is paired with the ccmanagementroomvideoguest function
function cc_managementroomvideomember()
{	global $bp;//add Global BuddyPress Functions
	$fieldnum=778; //778 is the Setting in xprofile for Video Selection
	$xprofile_setting= xprofile_get_field_data( $fieldnum, 1);  //Get xprofile setting from Merchant Settings for Main Video
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="boardroom1";		}
	return do_shortcode('[clubvideo cc_event_id="management-meeting" cc_plan_id="'.$xprofile_setting.'" cc_enable_lobby=true auth=true]');
}
add_shortcode('ccmanagementroomvideomember', 'cc_managementroomvideomember');
/*Clubcloud - A Shortcode for the Booth View to Switch by Xprofile Setting - Guest/////////////////////////////////////////////////////////////////////////////////////////////////////////
* This is used for the Guest entry pages to access the Management Meeting Room - it is paired with the ccmanagementroomvideomember function
* It accepts hostname as an argument which it gets from the Guest page URL get request parameter */
function cc_managementroomvideoguest($atts = array())
{ extract(shortcode_atts(array(
	'hostname' => 'defaultunknownhost'
), $atts));
	global $bp;//add Global BuddyPress Functions
	$fieldnum=778;//738 is the Setting in xprofile for Video Selection
	$xprofile_setting= xprofile_get_field_data( $fieldnum, 1); //Get xprofile setting from Merchant Settings for Main Video
	//Set Display template to Boardroom in case Profile setting is blank
	if($xprofile_setting==""){
		$xprofile_setting="boardroom1";		}
	return do_shortcode('[clubvideo cc_event_id="management-meeting" cc_plan_id="'.$xprofile_setting.'"]');
}
add_shortcode('ccmanagementroomvideoguest', 'cc_managementroomvideoguest');
/* ClubCloud - Main Connect Centre Switching Shortcode///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
* This shortcode does the main switching and returns the right room for the video fulfilment depending on several parameters
*Arguments: Takes the order number, or Booking Number (same input field)*/
function cc_connect($atts = array())
{	extract(shortcode_atts(array(
	'order' => 	$headerordernum=htmlspecialchars($_GET["order"]),
	'booking' =>	$headerbookingnum=htmlspecialchars($_GET["booking"]),
	'productnum' =>	$headerproductnum=htmlspecialchars($_GET["productnum"]),
	'vendor' =>	$headervendorid=htmlspecialchars($_GET["vendor"])
), $atts));
//Covering against Bad Input (converting to INT)
	$order=$order+0;
//Validate Booking
	if ($booking!="")
	{	$bookingisvalid=cc_validate_booking ($booking);
		//echo "1180 Booking is Valid - ". $bookingisvalid."- Booking Number: ".$booking."-<br>";
		if ($bookingisvalid==false){  		//trapping blank entry
			return '<div style="font-size:2.0em;color:black"><br>Invalid Booking Number entered or the Booking has been deleted<br></div>';}
	}
	global $bp, $WCFM, $WCFMmp;//add Global BuddyPress and WCFM Functions
//  set time offset in minutes this is used for how long in future we want meeting filter to deny access if you come too soon to room;
	$xprofile_room=649; //this is the field in xprofile that matches the fulfilment room setting
	$min_offset=3000;
	$time_offset=$min_offset*60;
	$display_default=true;
	//Get User Logged In Users- Merchants - and Staff Tree - get information on roles and xprofile
	$isloggedin = is_user_logged_in()  ;
	if ($isloggedin==true)
	{	 $user_id=get_current_user_id();
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;
		$role_type=cc_xprofile_build(1135,0,$user_id);	//1135 is Xprofile Value for setting
	}

//Set up Merchant Status
	if(($user->roles[0]=='wcfm_vendor' OR $user->roles[0]=='shop_staff' OR $user->roles[0]=='administrator'))
	{$ismerchant=true;}
//Begin Signed in User section - first deal with merchants w/ bookingnum, merchants w/o bookingnum, customers without order numbers.
	if ($isloggedin==true)
	{		//deal with Merchants with booking number -1 Check for Security - then Launch Valid Booking Check and Construct Code
		//echo "Booking Status - ".$booking." Is Merchant Status:".$ismerchant." <br>";
		if ($booking!="" and $ismerchant==true)
		{//security check - validate user store has this booking
			$display_default=false;
			$currentbookingchecksum= cc_orderinfo_by_booking($booking,"store_slug",0); //our booking
			$mystoreid=cc_orderinfo_by_booking("","store_slug",$user_id);				//the identified store booking
			$customer_id=cc_get_bookingheader($booking,"customerid",0);					//identified customer
			if ($currentbookingchecksum!=$mystoreid AND $customer_id!=$user_id)			//either the store matches or the merchant identified or the merchant user is the customer
			{	echo '<div style="font-size:2.0em;color:black"><br>Security Error - You have tried to access a Booking that is not yours<br></div>';
				return "Security Check Failed - Current Booking Checksum - ".$currentbookingchecksum." and Returned Store ID Lookup from My ID".$mystoreid. " Purchasing Customer ID ".$customer_id." User ID is ".$user_id."<br>";	}
			//Once Security Check passed - get message for Screen, and get meeting
			$multibook_call= cc_validate_booking_time ($booking, $time_offset,"singlebook",$xprofile_room);
			$messagecall= cc_validate_booking_time ($booking, $time_offset,"message",$xprofile_room);
			echo $messagecall;
			return $multibook_call;    //echo for debug only "Future Items - ".$multibook_call['futurecount']." Present Items ".$multibook_call['validcount']. " Past Items ".$multibook_call['pastcount']. "<br>";
		}// if booking number is blank then deal gather all merchant bookings and call up orders
//end merchant with booking number section

		$display_running_count=0;
		if($ismerchant==true) //separate merchants from non-merchants
		{	$vendor_bookings = apply_filters( 'wcfm_wcb_include_bookings', '' ); //get all bookings for vendor
			$storeid=cc_orderinfo_by_booking($vendor_bookings[0],"vendorid",0); //In case of Employee of Store need to inject Store owner ID into constructor to get correct shop settings
			$outputmerchant= array();
			//Main Constructor Merchant Get Booking Call
			$multibook_call= cc_shortcode_build("merchant",0,$storeid,$xprofile_room,$vendor_bookings,$time_offset, "false");
			//print_r($multibook_call);
			// Merchant Display Booking Logic
			if ($multibook_call['validcount']>=1 OR $multibook_call['futurecount']>=1)
			{ 	$display_default=false;
				if ($multibook_call['validcount']>=1)
				{	$display_default=false;
					$display_running_count=$display_running_count+$multibook_call['validcount'];}
			}
			//in case of no future or present bookings
			if ($multibook_call['validcount']==0 AND $multibook_call['futurecount']==0)
			{	$nothing_available= '<div style="font-size:1.75em;color:black"><br>No current orders available for merchant fulfilment</div>';
				array_push ($outputmerchant, $nothing_available);						}
			//case where only one valid merchant booking exists
			elseif ($multibook_call['validcount']==1)
			{	$display_default=false;
				array_push($outputmerchant, $multibook_call['message']);
				array_push($outputmerchant, $multibook_call['shortcode']);
			}
			//cases where more than one valid option exists
			elseif ($multibook_call['validcount']>1 OR $multibook_call['futurecount']>=1)
			{	$display_default=false;
				array_push ($outputmerchant, '<div style="font-size:2.50em;color:black"><br>Your Store Bookings <br></div>');
				foreach( $multibook_call['shortcode'] as $value )
				{array_push ($outputmerchant, $value);			}
				if ($multibook_call['futurecount']>=1)
				{	array_push ($outputmerchant,'<div style="font-size:1.50em;color:black"><br>Your Future Bookings<br></div>');
					foreach( $multibook_call['future'] as $value )
					{array_push ($outputmerchant, $value);			}
				}
			}
			foreach( $outputmerchant as $value )//display the output of Merchant Bookings Array
			{echo $value;		}
			//return;
		}//end Merchant		//echo "1241 Post Early Logged in - Order is: ".$order." Booking is ".$booking." <br>";

// In the Customer Personality - We Return this User's Customer Perspective Orders
		if ($order=="" AND $booking=="")
		{	    $infototalorders=cc_orders_by_user($user_id);
			//print_r($infototalorders);
			$validoutputcount=0;
			$outputcustomerarray=array();
			foreach ($infototalorders as $ordernumber)
			{		$infoordersmerchant=cc_bookings_by_order($ordernumber, $time_offset,"false");
				//echo $infoordersmerchant['validcount']." Valid Count <br>";
				if ($infoordersmerchant['validcount']>=1 OR $infoordersmerchant['futurecount']>=1)
				{	$ordermessage="For Order ". $ordernumber. ":<br>";
					array_push($outputcustomerarray, $ordermessage);
					$validoutputcount++;
					foreach( $infoordersmerchant as $value )
					{  foreach ($value as $subvalue)	{	array_push($outputcustomerarray, $subvalue);   }				}
				}
			}
			if ($validoutputcount==0){}
			else
			{	$display_default=false;
				array_unshift($outputcustomerarray,'<div style="font-size:2.50em;color:black"><br>Bookings you have purchased<br></div>') ;
				foreach ($outputcustomerarray as $subvalue) {echo $subvalue;}				 }
		}//end Customer Personality Section

	}//end of if logged in section
	$booking_was_entered_check=$_GET["booking"];   //Get Booking Number Status;
	if ($booking_was_entered_check=="")
	{	$booking_was_entered_check="blank";}
//Check Order Num Exists
	if($order!="")   //first Validate Order
	{		 $order_val_check= cc_validate_order($order);
		if ($order_val_check==false)
		{	 $display_default=false;
			return '<div style="font-size:2.5em;color:black"><br><br><br><br>'.$order.' is not a valid Order Number. Please check your number and try again<br><br><br><br><br></div>';}
		//Now retrieve Bookings Count for Order
		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order );//check the validity of bookings made
		$bookingcount=count($booking_ids);
		// echo $bookingcount." Booking Count <br>";
		if ($bookingcount==1)
		{	 $booking=$booking_ids[0];
			$bookingupdatedflag=true; }
	}
	//Deal with Bookings Case for non signed in users (guests) - Validate Booking and Time- and trigger Constructor
	//echo "875 Booking was Entered is:".$booking_was_entered_check." and Booking was Updated Flag ".$bookingupdatedflag."<br>";
	if ($booking_was_entered_check!="blank" OR $bookingupdatedflag==true)		//bypass filter if no booking ID entered
	{	  $validbookingcheck=cc_validate_booking($booking);
		//echo "1326 - inside booking check tree<br>";
		if ($validbookingcheck==false)
		{	$display_default=false;
			return '<div style="font-size:2.50em;color:black"><br><br><br>Booking Number '.$booking.' is invalid. Please check and try again<br><br><br><br><br></div>';
		}
		//echo "1331 got to timecheck<br>";
		$timecheck_status=cc_validate_booking_time ($booking, $time_offset,"checkonly",0);
		//print_r($timecheck_status)." 1332 Time Check Status <br>";
		if ($timecheck_status !="true"){
			$display_default=false;
			return $timecheck_status;	}
		else {
			echo cc_validate_booking_time ($booking, $time_offset,"messagecustomer",$xprofile_room);
			$returnitem=cc_validate_booking_time ($booking, $time_offset,"singlebook", $xprofile_room);
			echo $returnitem;
			return;
		}
	}
	//deal with order numbers for non-signed in users
	//echo "1341 Pre-Constructor Fire - Booking is ".$booking." and Order is ".$order."<br>";
	if ($booking!="" OR $order!="")//exclude from the Constructor function a case where decisions still need to be made
	{ 	//echo "1347 Got to the Constructor <br>";
		$outputbooking= array();
		if ($booking!="")
		{  //echo "inside simple debug loop at 1346<br>";
			$multibook_call= cc_shortcode_build("simple",$order,0,$xprofile_room,$booking,$time_offset, "false");			}
		else if ($ismerchant==true or is_admin()==true)
		{	//echo "inside 1322-isadmin<br>";
			$multibook_call= cc_shortcode_build("merchant",$order,0,$xprofile_room,$booking,$time_offset, "false");}
		else
		{//	echo "1356 pre fire<br>";
			$multibook_call= cc_shortcode_build("multibooking",$order,0,$xprofile_room,$booking,$time_offset, "false");}
		//Display the Function in case of Array or Single Value
		//echo "1359 -".$multibook_call['validcount']. " Valid Count and ".$multibook_call['futurecount']." Future Count - ".$multibook_call['pastcount']." Past Count<br>";
		if ($multibook_call['validcount']>=1 OR $multibook_call['futurecount']>=1)
		{	if ($multibook_call['validcount']==1)//straight to shortcode if there is only one booking
		{	$display_default=false;
			echo $multibook_call['message'];
			return $multibook_call['shortcode'];
		}
			array_unshift ($outputbooking,'<div style="font-size:2.50em;color:black"><br>Purchased Bookings<br></div>') ;
			foreach($multibook_call['shortcode'] as $value )//push all other bookings into array if there are more than one option
			{array_push($outputbooking, $value );}
			if ($multibook_call['futurecount']>=1)
			{	$display_default=false;
				array_push ($outputbooking,'<div style="font-size:1.50em;color:black"><br>Your Future Bookings<br></div>');
				foreach( $multibook_call['future'] as $value )
				{array_push ($outputbooking, $value);			}
			}
			foreach( $outputbooking as $returnvalue )//display the output of Merchant Bookings Array
			{echo $returnvalue;		}
			$display_default=false	 ;
			return "<br>";
		}
		else return '<div style="font-size:1.75em;color:black"><br>No current or future bookings exist to enter under this number</div>';
	}
	if ($display_default==true AND $isloggedin==true)
	{		return do_shortcode ('[elementor-template id="24508"]');	}
	else if ($display_default==true AND $isloggedin==false)
	{return do_shortcode ('[elementor-template id="24550"]');}
}
add_shortcode('ccconnect', 'cc_connect');
/*ClubCloud - a Function to Construct the Clubvideo Shortcode correctly with right settings///////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - Shortcode Type, Order Number, VendorID(optional), XProfile Field Number, BookingID, and Time Offset)
# Returns - a correctly formatted shortcode, or rejection of Booking information*/
function cc_shortcode_build($sc_type, $ordernum,$vendorid,$xprofile_field,$bookingid,$time_offset, $showpast)
{	global $WCFM, $WCFMmp;
	if ($bookingid!="" and is_array($bookingid)==false)
	{$bookingisvalid=cc_validate_booking ($bookingid);
		if ($bookingisvalid==false){  		//trapping blank entry
			return "Invalid Booking Number is Entered";}
	}
	if (is_array($bookingid)==true)//if booking id is array - sanitise and validate each booking array entry (to remove booking orphans, and non valid bookings)
	{	$bookingout=array();
		foreach ($bookingid as $bookingitem)
		{	$vendor_temp= cc_validate_booking_time ($bookingitem, $time_offset,"singleid", $xprofile_field);
			if ($vendor_temp!=""){
				array_push ($bookingout, $vendor_temp	);}
		}
		$bookingid=$bookingout;
	}
	if ($sc_type=="merchantsimple")//setting Merchant flag to True - and changing processing type to multi-booking
	{ $hasbookingflag=true;
		$merchant_flag=true;
		$window_bookingid=$bookingid;
		$sc_type="multibooking";	}
	else if ($sc_type=="merchant")//setting Merchant flag to True - and changing processing type to multi-booking
	{ $merchant_flag=true;
		$sc_type="multibooking";	}
	if ($sc_type=="simple" OR $sc_type=="multibooking"){}       //Reject Gate of No Type Input
	else return "Invalid Shortcode Type";
	//debug only return "<br>1336 Booking -".$bookingid." Order Num ".$ordernum. " SC Type -".$sc_type. " X Profile Setting - ".$xprofile_field ." Vendor ID".$vendorid."<br>";
	// set up default parameters
	$time_offsetdisplay=$time_offset/60;
	$current_time=current_time('timestamp');
	if (is_array($bookingid)==true) //Option 1 - function got passed multiple variables in array - apply passed variables
	{	$orderdetail=$bookingid;	}
	elseif ($sc_type=="merchant" or $merchant_flag==true)//option 2 - check if you are a merchant, and pull your bookings if you are
	{ $orderdetail = apply_filters( 'wcfm_wcb_include_bookings', '' );}
	else 	{$orderdetail= WC_Booking_Data_Store::get_booking_ids_from_order_id( $ordernum );	}//Option 3 you must be a user - so pull your individual bookings.

//Set cases where we have booking ID and order number to simple to bypass heavier logic in multi
	if ($bookingid!="" AND $ordernum!="" AND $merchant_flag==false)
	{	$sc_type="simple";	}
	if ($vendorid=="" AND count($orderdetail)<=1)
	{	$vendorid=$orderdetail[0]['vendorid'];}
	//Get Vendor IDs for Single Bookings
	if($vendorid=="" AND $bookingid !=""){
		if (is_array($bookingid)==true)
		{$bookfirstid= $bookingid[0];}
		else {$bookfirstid=$bookingid;}
		$infoposta=  get_wc_booking($bookfirstid);
		$infopostb= $infoposta->product_id;
		$vendorid= $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $infopostb);		}
	if (count($orderdetail)<=1 AND $bookingid==""){
		$bookingid=$orderdetail[0];		}
	//return "<br>982 Booking -".$bookingid." Order Num ".$ordernum. " SC Type -".$sc_type. " X Profile Setting - ".$xprofile_field ." Vendor ID".$vendorid."<br>";
	if ($vendorid!=""){   //if We have Vendor - Get Store Info
		$xprofile_setting=cc_xprofile_build($xprofile_field,0,$vendorid);}
	$store_slug      = cc_orderinfo_by_booking($bookingid,"store_slug",0)		;
	$store_name		 = cc_orderinfo_by_booking($bookingid,"store_name",0)		;
	//debug only return  $store_name." Indicated Storename at 990<br>";
	if ($xprofile_setting=="" AND is_array($bookingid)==true){
		$xprofile_setting=cc_xprofile_build($xprofile_field,$bookingid[0],0);	}
	else
	{$xprofile_setting=cc_xprofile_build($xprofile_field,$bookingid,0);}
	//return $xprofile_setting;
	if ($xprofile_setting=="A Vendor Blank Setting Was Returned from Xprofile Constructor- User Does not Have this Setting Applied" )
	{	$vendor_detail=get_userdata( $vendorid );
		$vendor_username=$vendor_detail->user_login;
		return "X-Profile Field Return Failure - Vendor : ".$vendorid." Username : ".$vendor_username."- Store name : ".$store_name." probably doesn't have a default setup for Storefront Consult Room<br>";	}
	//debug onlyreturn "Both provided -Booking ID ".$bookingid." and Order Num ".$ordernum. " Storename was - ".$store_name."<br>";
	switch ($sc_type)
	{	case "simple":
		$shortcode= do_shortcode('[clubvideo cc_plan_id="'.$xprofile_setting.'" cc_event_id="'.$store_slug.'-'.$bookingid.'"]');
		$outdatas = array(
			"validcount" => 1	,
			"message" => cc_get_bookingheader($bookingid,"customer",$store_name),
			"shortcode" => $shortcode 				      );
		return $outdatas;				break;
		case "multibooking":
			$outpastarray= array();  // Set up counters and prepare arrays
			$outfuturearray= array();
			$outbookarray= array();
			$out_to_shortcode_array= array();
			//debug only return "got to 1382 Multibooking<br>";
			$invalidcount=0;
			$orderwindowcount=0;
			$futurecount=0;
			foreach( $orderdetail as $booking_id )
			{ //implement time filtration and reject past - or early meetings
				$booking = get_wc_booking( $booking_id );
				$start_date = $booking->get_start_date();
				$end_date = $booking->get_end_date();
				$end_timestamp=strtotime($end_date);
				$start_timestamp=strtotime($start_date);
				$current15_time=$current_time+$time_offset;
				//Get Store Information for Friendly Display
				$store_name= cc_orderinfo_by_booking($booking_id,"store_slug",0);

				if ($current_time >= $end_timestamp AND $showpast=="true"){
					$orderpast= '<div style="font-size:1.75em;color:black">Booking '.$booking_id.' occurs in the past and can no longer be accessed... <br></div>'	;
					array_push($outpastarray,$orderpast)	;						}
				elseif($current15_time<$start_timestamp){
					$orderfuture= '<div style="font-size:1.75em;color:black">Booking '.$booking_id.' occurs in the future, please return within '.$time_offsetdisplay.' Minutes of your session<br></div>';
					$futurecount++;
					array_push($outfuturearray,$orderfuture);							}
				//Room Window for entry is open - push options into two arrays.
				elseif ($current_time<$end_timestamp)					{
					$orderwindowcount++;
					$window_bookingid=$booking_id;
					$menuchoice= '<div style="font-size:1.5em;color:black"><a href="https://clubelemental.com/connect?booking='.$booking_id.'&order='.$ordernum.'">Booking - '.$booking_id.' with '.$store_name. ' Starts at: '.$start_date.'</a> <br></div>';
					array_push($outbookarray,$menuchoice);}
			}	 //return "Order Past- ".$orderpastcount. "- Order Future - ".$orderfuturecount." - Order Window- ".$orderwindowcount. " Total Loops ".$nobookarray_count."<br>";
			//In Case there is only one viable option - Get Data for Message and call the Shortcode
			if ($orderwindowcount==1 or $hasbookingflag==true){
				$store_slug= cc_orderinfo_by_booking($window_bookingid,"store_slug",0);
				$store_name= cc_orderinfo_by_booking($window_bookingid,"store_name",0);
				$xprofile_setting=cc_xprofile_build($xprofile_field, $window_bookingid,0);
				$cust_data=get_wc_booking($window_bookingid);
				$bookingid=$window_bookingid;
				if ($merchant_flag==true)
				{ 	$shortcode= do_shortcode('[clubvideo cc_plan_id="'.$xprofile_setting.'" cc_event_id="'.$store_slug.'-'.$bookingid.'" cc_enable_lobby=true auth=true]');
					$outdatas = array(
						"validcount" => $orderwindowcount,
						"invalidcount" => $invalidcount,
						"futurecount" => $futurecount,
						"message" => cc_get_bookingheader($bookingid,"merchant",$store_name),
						"shortcode" => $shortcode 											     );
					return $outdatas;
				}else {$shortcode= do_shortcode('[clubvideo cc_plan_id="'.$xprofile_setting.'" cc_event_id="'.$store_slug.'-'.$bookingid.'"]');
					$outdatas = array(
						"validcount" => $orderwindowcount,
						"invalidcount" => $invalidcount,
						"futurecount" => $futurecount,
						"message" => cc_get_bookingheader($bookingid,"customer",$store_name),
						"shortcode" => $shortcode     						 			);
					return $outdatas;						 		}
			}else
				$outdatas = array(
					"validcount" 	=> $orderwindowcount  ,
					"futurecount" 	=> $futurecount       ,
					"invalidcount"  => $invalidcount	  ,
					"pastcount"		=> count($outpastarray)	  		,
					"past"			=> $outpastarray	  ,
					"future"		=> $outfuturearray	  ,
					"shortcode" 	=> $outbookarray     						);
			return $outdatas;
			break;	//end Multibooking
	}//end case Multibook
}
/*ClubCloud - a Function to return Product Information and Vendor Info from Booking Numbers///////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes order number
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_orderinfo_by_booking($bookingid,$fieldoption,$vendorid)
{		global $WCFM, $WCFMmp;  // set up default parameters
	if ($bookingid=="" AND $vendorid==""){
		return "Blank ID";}  //reject blank booking and vendor numbers
	$bookval=cc_validate_booking($bookingid);
	if ($bookingid !="" AND $bookval==false){ return "Invalid or Deleted Booking";} //reject invalid booking numbers if entered
	$booking = get_wc_booking( $bookingid );
	$productid= $booking->product_id;
	if ($vendorid !=""){} else {$vendorid= $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product($productid);}
	$store_user      = wcfmmp_get_store( $vendorid );
	$store_info      = $store_user->get_shop_info();
	$store_slug      = $store_info['store_slug'];
	$store_name      = $store_info['store_name'];
	if ($fieldoption=="store_slug"){
		return $store_slug;		}
	else if ($fieldoption=="store_name")
	{return $store_name;}
	else if ($fieldoption=="vendorid")
	{return $vendorid;}
}
/*ClubCloud - a Function to Construct Xprofile Field Settings from Fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - VendorID, XProfile Field Number     # Returns the Clubcloud template to use*/
function cc_xprofile_build($xprofile_field, $bookingid,$vendorid)
{ 	global $WCFM, $WCFMmp;
	if ($bookingid=="" AND $vendorid==""){   //trapping blank entry
		return "Function Needs either a Booking Number OR a Vendor ID<br>";}
	if ($xprofile_field==""){	return "Error- X-Profile Field is required<br>";}
	if ($vendorid!="")
	{ $returnfield= xprofile_get_field_data( $xprofile_field, $vendorid );
		if ($returnfield=="")
		{	return "A Vendor Blank Setting Was Returned from Xprofile Constructor- User Does not Have this Setting Applied";}
		else { return $returnfield;		}
	}
	if ($vendorid=="")
	{	$infoposta=  get_wc_booking($bookingid);
		$infopostb= $infoposta->product_id;
		$vendorid= $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $infopostb);
		$returnfield= xprofile_get_field_data( $xprofile_field, $vendorid );
		if ($returnfield=="")
		{	return "A Booking Blank Setting Was Returned from Xprofile Constructor- User Does not Have this Setting Applied<br>";		}
		else 	{ return $returnfield;			}
	}
}
/*ClubCloud - a Function to return Order Information by Signed in User///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes userID
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_orders_by_user($userid)
{	 $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');
	$array_holder=array();
	$customer = $userid;
	// Get all customer orders
	$customer_orders = get_posts(array(
		'numberposts' => -1,
		'meta_key' => '_customer_user',
		'orderby' => 'date',
		'order' => 'DESC',
		'meta_value' => $customer,
		'post_type' => wc_get_order_types(),
		'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-processing','wc-completed'),    ));
	$Order_Array = []; //
	foreach ($customer_orders as $customer_order)										 {
		$orderq = wc_get_order($customer_order);
		$bookingscheck=WC_Booking_Data_Store::get_booking_ids_from_order_id($customer_order->ID);
		$array_count=count($bookingscheck). "Bookings Count<br>";
		if ($array_count>=1){	$Order_Array[] = $orderq->get_id();	}					}
	//return $array_holder;
	return $Order_Array;
}
/*ClubCloud - a Function to return Order Information by Signed in User///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes userID, and time offset to generate correct timeframes
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_bookings_by_order($order, $time_offset,$showpast)
{	global $bp, $WCFM, $WCFMmp;
	$current_time=current_time('timestamp');
	$orderdetail= WC_Booking_Data_Store::get_booking_ids_from_order_id( $order );
	if ($showpast==""){ $showpast="true";}
	$time_offsetdisplay=$time_offset/60;
	$outnobookarray= array();  // Set up counters and prepare arrays
	$outbookarray= array();
	$futurecount=0;
	$invalidcount=0;
	$orderwindowcount=0;
	foreach( $orderdetail as $booking_id )
	{ //implement time filtration and reject past - or early meetings
		$booking = get_wc_booking( $booking_id );
		$start_date = $booking->get_start_date();
		$end_date = $booking->get_end_date();
		$end_timestamp=strtotime($end_date);
		$start_timestamp=strtotime($start_date);
		$current15_time=$current_time+$time_offset;
		//Get Store Information for Friendly Display
		$infoposta=  get_wc_booking($booking_id);
		$infopostb= $infoposta->product_id;
		$vendorid= $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $infopostb);
		$store_user      = wcfmmp_get_store( $vendorid );
		$store_info      = $store_user->get_shop_info();
		$store_name      = $store_info['store_name'];

		if ($current_time >= $end_timestamp AND $showpast=="true")//only for debug return $booking_id. " booking id and ".$start_date."<br>";
		{	$orderpast= '<div style="font-size:1.50em;color:black">Booking '.$booking_id.' occurs in the past and can no longer be accessed. <br></div>'	;
			array_push($outnobookarray,$orderpast)	;					}
		elseif($current15_time<$start_timestamp)
		{	$orderfuture= '<div style="font-size:1.50em;color:black">Booking '.$booking_id.' occurs in the future, please return within '.$time_offsetdisplay.' Minutes of your session..<br></div>';
			array_push($outnobookarray,$orderfuture);
			$futurecount++;
		}
		//Room Window for entry is open - push options into two arrays.
		elseif ($current_time<$end_timestamp)
		{	$window_bookingid=$booking_id;
			$orderwindowcount++;
			$menuchoice= '<div style="font-size:1.25em;color:black"><a href="https://clubelemental.com/connect?booking='.$booking_id.'&order='.$order.'">Booking - '.$booking_id.' with '.$store_name. ' Starts at: '.$start_date.'..</a> <br></div>';
			array_push($outbookarray,$menuchoice);
		}
	}	$outdatas = array(
	"validcount" 	=> $orderwindowcount,
	"invalidcount" 	=> $invalidcount,
	"futurecount" 	=> $futurecount,
	"validlinks" 	=> $outbookarray  ,
	"rejections"	=> $outnobookarray			);
	if ($returnfalse==true){return;}
	else {return $outdatas;	}
}
/*ClubCloud - a Function to return Product Information and Vendor Info from Order Numbers///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# Arguments - takes order number
# Returns - an Array with VendorID, Store Name, Product ID, and Product Name (or multiple arrays)*/
function cc_orderinfo_by_ordernum($order)
{	global $bp, $WCFM, $WCFMmp;	// set up default parameters
	$order=$order+0;
	if ($order==""){
		return "Blank Order";}
	$orderinfo = wc_get_order($order);
	//return $orderinfo;
	$items = $orderinfo->get_items();
	$outarray1= array();
	foreach ( $items as $item ) {
		$product_name = $item->get_name();
		$product_id = $item->get_product_id();
		$vendor_id= $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id);
		$store_user      = wcfmmp_get_store( $vendor_id );
		$store_info      = $store_user->get_shop_info();
		$store_name      = $store_info['store_slug'];
		$outdatas = array(
			"productname" => $product_name,
			"productid" => $product_id,
			"vendorid"  => $vendor_id,
			"storename" => $store_name
		);
		array_push($outarray1,$outdatas);			}
	return $outarray1;
}
/*Club Cloud - A Function to Validate a Order Number - and Ensure it exists	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- Woocommerce Order Number (postID) - passed into it as string
Returns - False for invalid Number  - True for valid order number*/
function cc_validate_order ($ordnum)
{   if(get_post_type($ordnum) == "shop_order"){
	return true;}
else{		return false;}
}
/*Club Cloud - A Function to Validate a Booking ID - and Ensure it exists////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- BookingID - passed into it as string
Returns - False for invalid booking
Booking Object if Booking exists*/
function cc_validate_booking ($booking)
{   if ($booking ==""){  //reject blank bookings
	return false;}
	$bookingnum = get_wc_booking($booking);  //get booking from woocomm
	$orderobject = $bookingnum->order_id;	  //check if there is an order ID in the booking object (impossible not to have one if it is real)
	if ($orderobject==""){
		return false;	}
	else return $bookingnum;  //return the object
}
/*Club Cloud - A Function to Filter Expired Bookings /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- BookingID - passed into it as string - TimeOffset (to check how long in future we allow meetings to be entered) $returnmenuoption - if Menu items for multiple bookings need to be constructed
Returns - Formatted Error Message or Booking Object if Booking exists*/
function cc_validate_booking_time ($booking, $time_offset,$returnmenuoption, $xprofile_field)
{  	$min_offset=$time_offset/60;//converting time offset back to minutes for friendly user message
	//echo "<br> I got to TimeCheck <br>";
	if (cc_validate_booking($booking) ==false){  //reject invalid bookings
		return ;}
	$current_time=current_time('timestamp');
	$bookingdetail = get_wc_booking( $booking);
	$start_date = $bookingdetail->get_start_date();
	$end_date = $bookingdetail->get_end_date();
	$end_timestamp=strtotime($end_date);
	$start_timestamp=strtotime($start_date);
	$current15_time=$current_time+$time_offset;
	if ($current_time >= $end_timestamp)
	{	if ($returnmenuoption=="singlebook" OR $returnmenuoption=="checkonly")
	{	return '<div style="font-size:2.0em;color:black"><br>Booking- '.$booking.' occurs in the past and can no longer be accessed.. <br></div>';	}
	else return ;
	}
	else if($current15_time<$start_timestamp)
	{
		if($returnmenuoption=="singlebook" OR $returnmenuoption=="checkonly")
		{return '<div style="font-size:2.0em;color:black"><br>Booking '.$booking.' occurs too far in the future. Please return '.$min_offset.' Minutes before your session..<br></div>';}
		else if ($returnmenuoption=="singleid") { return $booking;}
		else return;
	}
	if ($returnmenuoption=="singlebook" OR $returnmenuoption=="message" OR $returnmenuoption=="singleid" OR $returnmenuoption=="messagecustomer")
	{	//return $returnmenuoption." RetMenu at 1669<br>";
		$store_slug= cc_orderinfo_by_booking($booking,"store_slug",0);
		$store_name= cc_orderinfo_by_booking($booking,"store_name",0);
		$xprofile_setting=cc_xprofile_build($xprofile_field, $booking,0);
		$cust_data=get_wc_booking($booking);
		$bookingid=$booking;
		if ($returnmenuoption=="singleid"){ return $bookingid;}
		$shortcode= do_shortcode('[clubvideo cc_plan_id="'.$xprofile_setting.'" cc_event_id="'.$store_slug.'-'.$bookingid.'" cc_enable_lobby=true auth=true]');
		if ($returnmenuoption=="message") 	{	return cc_get_bookingheader($booking,"merchant",$store_name);	}
		if ($returnmenuoption=="messagecustomer") 	{	return cc_get_bookingheader($booking,"customer",$store_name);	}
		if ($returnmenuoption=="singlebook")	{	return $shortcode;	}
	}
	else return "true";
}
/*Club Cloud A function to format Merchant helpful information in Bookings ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Arguments- BookingID - passed into it as string Returns - Formatted Message to Merchant or Error*/
function cc_get_bookingheader($bookingid,$sc_type,$merchantname)
{	$bookingisvalid=cc_validate_booking ($bookingid);
	if ($bookingisvalid==false){  		//trapping blank entry
		return "Invalid Booking Number is Entered";}
	$dp=get_wc_booking($bookingid);
	$booking_start=Date('F j, Y, g:i a',$dp->start);
	$booking_end=Date('F j, Y, g:i a',$dp->end);
	//Formatting Customer Information
	$customer_num=$dp->customer_id;
	$customerinfo=get_userdata($customer_num);
	$user_nice=$customerinfo->user_nicename;
	if ($sc_type=="merchant")
	{	return '<div style="font-size:2.0em;color:black">Booking: '.$bookingid.' Starts: '.$booking_start.' Ends: '.$booking_end.' with Customer: '.$user_nice.'<br></div>';}
	if ($sc_type=="customer"){
		return '<div style="font-size:2.0em;color:black">Booking: '.$bookingid.' Starts: '.$booking_start.' Ends: '.$booking_end.' with Merchant: '.$merchantname.'<br></div>';}
	if ($sc_type=="customerid"){		return $customer_num;}

	else return "Invalid Shortcode Argument<br>";
}
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
