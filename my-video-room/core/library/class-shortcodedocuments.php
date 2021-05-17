<?php
/**
 * Display Shortcode Documentation
 *
 * @package MyVideoRoomPlugin\Core\Library\ShortcodeDocuments
 */

namespace MyVideoRoomPlugin\Core\Library;

use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Factory;

/**
 * Class SectionTemplate
 */
class ShortcodeDocuments {

	// ---
	// Shortcode Documents Section.

	/**
	 * Render all Shortcodes that are published for User usage.
	 */
	public function render_all_shortcode_docs() {

		$this->render_general_shortcode_docs();
		$this->render_sitevideoroom_shortcode_docs();
		$this->render_personalmeeting_shortcode_docs();
		$this->render_buddypress_shortcode_docs();
		$this->render_wcfm_shortcode_docs();
		$this->render_wcbookings_shortcode_docs();
		$this->render_elementor_shortcode_docs();

	}

	/**
	 * Render_filtered_shortcode_documents.
	 *
	 * @return null - it prints strings.
	 */
	public function render_filtered_shortcode_docs() {

		$this->render_general_shortcode_docs();
		$this->render_sitevideoroom_shortcode_docs();
		$this->render_personalmeeting_shortcode_docs();
		if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
			$this->render_buddypress_shortcode_docs();
		}
		if ( Factory::get_instance( SiteDefaults::class )->is_wcfm_active() ) {
			$this->render_wcfm_shortcode_docs();
		}
		if ( Factory::get_instance( SiteDefaults::class )->is_elementor_active() ) {
			$this->render_elementor_shortcode_docs();
		}
		if ( Factory::get_instance( SiteDefaults::class )->is_woocommerce_bookings_active() ) {
			$this->render_wcbookings_shortcode_docs();
		}
		return null;
	}


	/**
	 * Render all General Shortcodes that are published for User usage.
	 */
	public function render_general_shortcode_docs() {

		?>
	<div class="mvr-row">
	<h2>General Shortcodes</h2>
		<table style="width:70%; border: 1px solid black;">
			<tr>
				<th style="width:25%; text-align: left;"><h2>[ccsitedefaultconfig]</h2></th>
				<th style="width:75%; text-align: left;"><p>This Shortcode renders the site default room configuration
				in the frontend of the site. Please be careful with the placement of this shortcode as it allows site
				default settings to be edited, so care must be taken its placement. </p>
				</th>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[getvideo_room_info]</h2>
			<p><b>Arguments</b><br>
			room="XX" type="YY"</p>
			</td>
			<td style="width:75%; text-align: left;">Returns a Variety of useful Information about a room that you can place in your pages<br>
			Room=(one of the following - meet-center, bookings-center, site-video-room) - selects the auto generated room type to query. This is required.<br>
			Type (title) - Room Name (with spaces) - Type (slug) - returns the post slug (eg- www.c.com/jones has slug of Jones) - Type (post_id) - returns the WordPress Post ID of a room
			Type (url) - returns URL of room. <BR>
			Usage - <b>[getvideo_room_info room="bookings-center" type = "url"]</b> will return the URL of the Bookings Center]
			</td>
			</tr>

		</table>
	</div>
		<?php

	}

	/**
	 * Render all BuddyPress Shortcodes that are published for User usage.
	 */
	public function render_buddypress_shortcode_docs() {

		?>
	<div class="mvr-row">

		<h2>BuddyPress Video Shortcodes</h2>
			<table>
				<tr>
					<th style="width:25%; text-align: left;">Shortcode Name</th>
					<th style="width:75%; text-align: left;">Usage</th>
				</tr>

				<tr>
				<td style="width:25%; text-align: left;"><h2>[ccbpboardroomswitch]</h2></td>
				<td style="width:75%; text-align: left;">This Shortcode is designed to be used in BuddyPress profile pages. It is not
				available outside of the BuddyPress profile loop environment. It handles everything in the context of whose profile you are viewing
				If you are viewing your own profile, then you get a host video experience, if you are looking at someone elses profile (or are signed out)
				then the guest page for that profile is rendered. The room that is rendered is the same as the Personal Video Room - and seamlessly
				works with a Personal Video Room used in a non BuddyPress environment.
				<p>There are no Guest Shortcodes needed, as normal room shortcodes work correctly for users who are signed out and thus
				not in the BuddyPress loop. Normal meeting invites, links, guest reception settings are available for rooms whose hosts
				enter via BuddyPress.</p>
				</td>
				</tr>
			</table>
	</div>
		<?php

		return null;
	}



	/**
	 * Render all SiteVideoRoom Shortcodes that are published for User usage.
	 */
	public function render_sitevideoroom_shortcode_docs() {

		?>
	<div class="mvr-row">
	<h2>Site Video Room Shortcodes</h2>
		<table style="width:70%; border: 1px solid black;">
			<tr>
				<th style="width:25%; text-align: left;"><h2>Shortcode Name</h2></th>
				<th style="width:75%; text-align: left;">Usage</th>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccsitevideoroom]</h2></td>
			<td style="width:75%; text-align: left;">Renders the main Site Video Room - it can be used on any page in the site - and handles automatically whether you are a host or guest.</td>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccsitevideoroomhost]</h2></td>
			<td style="width:75%; text-align: left;">Renders the Site Video Room - it can be used on any page in the site - It will make whoever uses this entrance a <b>Host</b> of site Video Room</td>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccsitevideoroomguest]</h2></td>
			<td style="width:75%; text-align: left;">Renders the Site Video Room - it can be used on any page in the site - It will make whoever uses this entrance a <b>Guest</b> of site Video Room</td>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccsitevideoroomsettings]</h2></td>
			<td style="width:75%; text-align: left;">
			Renders the settings of the Site Video Room - <b>Note</b> - any place where this is added will be able to adjust the settings
			please pay attention to security where placing this shortcode to prevent unwanted modification</td>
			</tr>

		</table>
	</div>
		<?php

	}

	/**
	 * Render all Personal Meeting Shortcodes that are published for User usage.
	 */
	public function render_personalmeeting_shortcode_docs() {

		?>
	<div class="mvr-row">
	<h2>Personal Meeting Shortcodes</h2>
		<table style="width:70%; border: 1px solid black;">
			<tr>
				<th style="width:25%; text-align: left;">Shortcode Name</th>
				<th style="width:75%; text-align: left;">Usage</th>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccmeetswitch]</h2></td>
			<td style="width:75%; text-align: left;"><b>Use this Shortcode wherever possible to render Personal Meetings </b>. Renders the Main Site Meeting Center Reception page for users. This page is automatically
			created by the plugin in the details above, but can also be added anywhere on the site. Please note that this switch automatically
			changes the host and guest context depending on user state (logged on/off/admins etc). Take special care when using this page with
			regards to emails- invites etc. The page contains filters in the host for anonymous meeting invites, querying users etc. We recommend
			using the default Meeting Center location for emails, invites etc. The plugins own template and widgets always use the default
			location of the Meeting Center, which you can change on this tab without issue.<br>
			</td>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccpersonalmeetingguest]</h2></td>
			<td style="width:75%; text-align: left;">This shortcode will always render the <b>Guest</b> reception of the meeting center. It will prompt
			the user for the username of the Host, accept a meeting invite link (automatically in the URL), or accept a hostname (automatically in the URL)
			It will also prompt for the Site Video Room if enabled. <b>Please note</b>- this link is not meant to be used for BuddyPress, WCFM, or WooCommerce Bookings pages which
			use their own logic. Please use the shortcodes in BuddyPress, WCFM, WooCommerce Bookings, etc for placing on plugin pages.</td>
			<br>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccpersonalmeetinghost]</h2></td>
			<td style="width:75%; text-align: left;">This shortcode will always render the <b>Host</b> reception of the meeting center. This page determines its host from
			the logged in user. If placed in anonymous/non-logged in areas of the site the shortcode will default to guest reception mode.
			<b>Please note</b> this link is not meant to be used for BuddyPress, WCFM, or WooCommerce Bookings pages which
			use their own logic. Please use the shortcodes in BuddyPress, WCFM, WooCommerce Bookings, etc for placing on plugin pages. Host settings render
			automatically in the short code or can be rendered separately by using the [personalmeetinghostsettings] shortcode </td>
			<br>
			</tr>

			<tr>
			<td style="width:25%; text-align: left;"><h2>[ccpersonalmeetinghostsettings]</h2></td>
			<td style="width:75%; text-align: left;">This shortcode will render only the <b>settings </b>page of the <b>Host</b>. This is useful if you just want to edit
			the room settings without launching the full room. This shortcode determines its host from
			the logged in user. If placed in anonymous/non-logged in areas of the site the shortcode will return blank.
			<b>Please note</b> admin settings for personal rooms are shared between BuddyPress Profile Rooms and Personal Video Rooms as
			they are effectively the same room, with multiple entrances </td>
			<br>
			</tr>

		</table>
	</div>
		<?php

	}

	/**
	 * Render all WooCommerce Bookings Shortcodes that are published for User usage.
	 */
	public function render_wcbookings_shortcode_docs() {

		?>
	<div class="mvr-row">
	<h2>WooCommerce Bookings Shortcodes</h2>
		<table style="width:70%; border: 1px solid black;">
			<tr>
				<th style="width:25%; text-align: left;">Shortcode Name</th>
				<th style="width:75%; text-align: left;">Usage</th>
			</tr>


			</tr>

		</table>
	</div>
		<?php

	}

	/**
	 * Render all WCFM Shortcodes that are published for User usage.
	 */
	public function render_wcfm_shortcode_docs() {

		?>
	<div class="mvr-row">
	<h2>WCFM Shortcodes</h2>
		<table style="width:70%; border: 1px solid black;">
			<tr>
				<th style="width:25%; text-align: left;">Shortcode Name</th>
				<th style="width:75%; text-align: left;">Usage</th>
			</tr>

		</table>
	</div>
		<?php

	}

	/**
	 * Render all Elementor Shortcodes that are published for User usage.
	 */
	public function render_elementor_shortcode_docs() {

		?>
	<div class="mvr-row">
	<h2>Elementor Shortcodes</h2>
		<table style="width:70%; border: 1px solid black;">
			<tr>
				<th style="width:25%; text-align: left;">Shortcode Name</th>
				<th style="width:75%; text-align: left;">Usage</th>
			</tr>

		</table>
	</div>
		<?php

	}



}

