<?php
/**
 * Display Shortcode Documentation
 *
 * @package MyVideoRoomPlugin\Library\ShortcodeDocuments
 */

namespace MyVideoRoomPlugin\Library;

/**
 * Class SectionTemplate
 */
class ShortcodeDocuments {


	/**
	 * Render all General Shortcodes that are published for User usage.
	 */
	public function render_general_shortcode_docs() {

		?>
		<div class="mvr-row">
			<h2><?php esc_html_e( 'General Shortcodes', 'my-video-room' ); ?></h2>
			<table style="width:70%; border: 1px solid black;">
				<tr>
					<th style="width:25%; text-align: left;"><h2>[ccsitedefaultconfig]</h2></th>
					<th style="width:75%; text-align: left;"><p>
							<?php
							esc_html_e(
								'This Shortcode renders the site default room configuration in the frontend of the site. Please be careful with the placement of this shortcode as it allows site default settings to be edited, so care must be taken its placement.',
								'my-video-room'
							);
							?>
						</p>
					</th>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[getvideo_room_info]</h2>
						<p><b><?php echo esc_html__( 'Arguments', 'my-video-room' ); ?></b><br>
							room="XX" type="YY"</p>
					</td>
					<td style="width:75%; text-align: left;">
						<?php
						echo esc_html__(
							'Returns a variety of useful Information about a room that you can place in your pages',
							'my-video-room'
						);
						?>
						<br />

						<?php
						/* translators: %s is the site URL */
						esc_html__(
							'Room=(one of the following - meet-center, bookings-center, site-video-room) - selects the auto generated room type to query. This is required.',
							'my-video-room'
						);
						?>
						<br />
						<?php
						\printf(
						/* translators: %s is the site URL */
							esc_html__(
								'Type (title) - Room Name (with spaces) - Type (slug) - returns the post slug (eg- %s has slug of Jones) - Type (post_id) - returns the WordPress Post ID of a room Type (url) - returns URL of room.',
								'my-video-room'
							),
							esc_url_raw( get_site_url() . '/jones' )
						);
						?>
						<br />
						<?php
						echo esc_html__(
							'Usage -',
							'my-video-room'
						);
						?>
						<br />
						<?php
						echo esc_html__(
							'<strong>[getvideo_room_info room="bookings-center" type = "url"]</strong> will return the URL of the Bookings Center',
							'my-video-room'
						);
						?>
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

			<h2><?php esc_html_e( 'BuddyPress Video Shortcodes', 'my-video-room' ); ?></h2>
			<table>
				<tr>
					<th style="width:25%; text-align: left;"><?php esc_html_e( 'Shortcode Name', 'my-video-room' ); ?></th>
					<th style="width:75%; text-align: left;"><?php esc_html_e( 'Usage', 'my-video-room' ); ?></th>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccbpboardroomswitch]</h2></td>
					<td style="width:75%; text-align: left;">
						<p>
							<?php
							esc_html_e(
								'This Shortcode is designed to be used in BuddyPress profile pages. It is not available outside of the BuddyPress profile loop environment. It handles everything in the context of whose profile you are viewing If you are viewing your own profile, then you get a host video experience, if you are looking at someone elses profile (or are signed out) then the guest page for that profile is rendered. The room that is rendered is the same as the Personal Video Room - and seamlessly works with a Personal Video Room used in a non BuddyPress environment.
					',
								'my-video-room'
							);
							?>
						</p>
						<p>
							<?php
							echo esc_html__(
								'There are no Guest Shortcodes needed, as normal room shortcodes work correctly for users who are signed out and thus not in the BuddyPress loop. Normal meeting invites, links, guest reception settings are available for rooms whose hosts enter via BuddyPress.',
								'my-video-room'
							);
							?>
						</p>
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
			<h2><?php esc_html_e( 'Site Video Room Shortcodes', 'my-video-room' ); ?></h2>
			<table style="width:70%; border: 1px solid black;">
				<tr>
					<th style="width:25%; text-align: left;">
						<h2><?php esc_html_e( 'Shortcode Name', 'my-video-room' ); ?></h2></th>
					<th style="width:75%; text-align: left;"><?php esc_html_e( 'Usage', 'my-video-room' ); ?></th>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccsitevideoroom]</h2></td>
					<td style="width:75%; text-align: left;"><?php esc_html_e( 'Renders the main Site Video Room - it can be used on any page in the site - and handles automatically whether you are a host or guest.', 'my-video-room' ); ?></td>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccsitevideoroomhost]</h2></td>
					<td style="width:75%; text-align: left;"><?php esc_html_e( 'Renders the Site Video Room - it can be used on any page in the site - It will make whoever uses this entrance a <strong>Host</strong> of site Video Room<', 'my-video-room' ); ?>/td>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccsitevideoroomguest]</h2></td>
					<td style="width:75%; text-align: left;"><?php esc_html_e( 'Renders the Site Video Room - it can be used on any page in the site - It will make whoever uses this entrance a <strong>Guest</strong> of site Video Room', 'my-video-room' ); ?></td>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccsitevideoroomsettings]</h2></td>
					<td style="width:75%; text-align: left;">
						<?php
						esc_html_e(
							'Renders the settings of the Site Video Room - <strong>Note</strong> - any place where this is added will be able to adjust the settings please pay attention to security where placing this shortcode to prevent unwanted modification',
							'my-video-room'
						);
						?>
					</td>
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
			<h2><?php esc_html_e( 'Personal Meeting Shortcodes', 'my-video-room' ); ?></h2>
			<table style="width:70%; border: 1px solid black;">
				<tr>
					<th style="width:25%; text-align: left;"><?php esc_html_e( 'Shortcode Name', 'my-video-room' ); ?></th>
					<th style="width:75%; text-align: left;"><?php esc_html_e( 'Usage', 'my-video-room' ); ?></th>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccmeetswitch]</h2></td>
					<td style="width:75%; text-align: left;"><strong>
							<?php
							esc_html_e(
								'Use this Shortcode wherever possible to render Personal Meetings </strong>. Renders the Main Site Meeting Center Reception page for users. This page is automatically created by the plugin in the details above, but can also be added anywhere on the site. Please note that this switch automatically changes the host and guest context depending on user state (logged on/off/admins etc). Take special care when using this page with regards to emails- invites etc. The page contains filters in the host for anonymous meeting invites, querying users etc. We recommend using the default Meeting Center location for emails, invites etc. The plugins own template and widgets always use the default location of the Meeting Center, which you can change on this tab without issue.',
								'my-video-room'
							);
							?>
							<br>
					</td>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccpersonalmeetingguest]</h2></td>
					<td style="width:75%; text-align: left;">
						<?php
						esc_html_e(
							'This shortcode will always render the <strong>Guest</strong> reception of the meeting center. It will prompt the user for the username of the Host, accept a meeting invite link (automatically in the URL), or accept a hostname (automatically in the URL) It will also prompt for the Site Video Room if enabled. <strong>Please note</strong>- this link is not meant to be used for BuddyPress, WCFM, or WooCommerce Bookings pages which	use their own logic. Please use the shortcodes in BuddyPress, WCFM, WooCommerce Bookings, etc for placing on plugin pages.',
							'my-video-room'
						);
						?>
					</td>

				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccpersonalmeetinghost]</h2></td>
					<td style="width:75%; text-align: left;">
						<?php
						esc_html_e(
							'This shortcode will always render the <strong>Host</strong> reception of the meeting center. This page determines its host from the logged in user. If placed in anonymous/non-logged in areas of the site the shortcode will default to guest reception mode. <strong>Please note</strong> this link is not meant to be used for BuddyPress, WCFM, or WooCommerce Bookings pages which use their own logic. Please use the shortcodes in BuddyPress, WCFM, WooCommerce Bookings, etc for placing on plugin pages. Host settings render automatically in the short code or can be rendered separately by using the [personalmeetinghostsettings] shortcode',
							'my-video-room'
						);
						?>
					</td>
				</tr>

				<tr>
					<td style="width:25%; text-align: left;"><h2>[ccpersonalmeetinghostsettings]</h2></td>
					<td style="width:75%; text-align: left;">
						<?php
						esc_html_e(
							'This shortcode will render only the <strong>settings</strong> page of the <strong>Host</strong>. This is useful if you just want to edit the room settings without launching the full room. This shortcode determines its host from the logged in user. If placed in anonymous/non-logged in areas of the site the shortcode will return blank. <strong>Please note</strong> admin settings for personal rooms are shared between BuddyPress Profile Rooms and Personal Video Rooms as they are effectively the same room, with multiple entrances ',
							'my-video-room'
						);
						?>
					</td>
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
			<h2><?php esc_html_e( 'WooCommerce Bookings Shortcodes', 'my-video-room' ); ?></h2>
			<table style="width:70%; border: 1px solid black;">
				<tr>
					<th style="width:25%; text-align: left;"><?php esc_html_e( 'Shortcode Name', 'my-video-room' ); ?></th>
					<th style="width:75%; text-align: left;"><?php esc_html_e( 'Usage', 'my-video-room' ); ?></th>
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
			<h2><?php esc_html_e( 'WCFM Shortcodes', 'my-video-room' ); ?></h2>
			<table style="width:70%; border: 1px solid black;">
				<tr>
					<th style="width:25%; text-align: left;"><?php esc_html_e( 'Shortcode Name', 'my-video-room' ); ?></th>
					<th style="width:75%; text-align: left;"><?php esc_html_e( 'Usage', 'my-video-room' ); ?></th>
				</tr>

			</table>
		</div>
		<?php

	}

}

