<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */
use \MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingHelpers;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Library\ShortcodeDocuments;
use MyVideoRoomPlugin\Module\Elementor\Module as Elementor;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;

return function (
): string {
	ob_start();

	$post_id = Factory::get_instance( RoomMap::class )->get_post_id_by_room_name( MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING );

	global $wp;
	// Handling Regenerate Command clicked by user-  to recreate pages.
	$regenerate = $params['regenerate'] ?? sanitize_text_field( wp_unslash( $_GET['regenerate'] ?? '' ) );
	if ( $regenerate ) {
		// Check Setting is truly empty and not a back button click etc.
		$check_is_empty = $post_id;

		if ( ! $check_is_empty ) {
			Factory::get_instance( RoomMap::class )->delete_room_mapping( MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING );
			Factory::get_instance( MVRPersonalMeetingHelpers::class )->create_personal_meetingroom_page();
			$url_base = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
			echo '<h1>Page Refresh Completed</h1>';
			return wp_safe_redirect( $url_base );
		}
	}
	?>
	<div class="mvr-outer-box-wrap">

	<h1 class="mvr-heading-head"><?php esc_html_e( 'Personal Meeting Rooms', 'my-video-room' ); ?></h1>
		<?php
		$security_enabled = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( SiteDefaults::MODULE_SECURITY_ID );
		if ( $security_enabled ) {
			echo esc_html( Factory::get_instance( \MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons::class )->site_wide_enabled() );
		}
		?>
		<p>
		<?php
		esc_html_e(
			'A Personal Meeting Room is an individually controlled meeting room with its own Reception Area, Room Layout Selection, Privacy, and Room Permissions.
			A Reception page is created automatically with the module to handle Guest Arrival. A user is the host of their own room, and everyone else is a guest.
			Users can send invites by email, or by special unique invite code.',
			'my-video-room'
		);
		?>
			<br></p>
		<?php
		// Activation/module.
		Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_ID );
		?>

	</div>
	<div class="wrap">
		<h2><?php esc_html_e( 'Current Room Information', 'my-video-room' ); ?></h2>
		<table style="width:70%; border: 1px solid black;"  >
				<tr>
					<th style="width:25%; text-align: left; "><?php esc_html_e( 'Page Name', 'my-video-room' ); ?></th>
					<th style="width:25%; text-align: left;" ><?php esc_html_e( 'Page URL', 'my-video-room' ); ?></th>
					<th style="width:25%; text-align: left;" ><?php esc_html_e( 'WordPress Post ID', 'my-video-room' ); ?></th>
					<th style="width:25%; text-align: left;" ><?php esc_html_e( 'Edit', 'my-video-room' ); ?></th>
				</tr>
				<tr>
					<td style="width:25%; text-align: left;">
					<?php
						//$title = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, 'title' );
					if ( $title ) {
						echo esc_html( $title );
					}
					?>
					</td>
					<td style="width:25%; text-align: left;">
					<?php
					//$url = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, 'url' );
					if ( $url ) {
						echo '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_url( $url ) . '</a>';}
					?>
					</td>
					<td style="width:25%; text-align: left;">
					<?php
						//$post_id_return = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, 'post_id' );
					if ( $post_id_return ) {
						echo esc_html( $post_id_return );}
					?>
					</td>
					<td style="width:25%; text-align: left;">
						<?php
						if ( Factory::get_instance( Elementor::class )->is_elementor_active() ) {
							if ( $post_id ) {
								//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  - SiteURL already escaped WP function.
								echo '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=elementor" class="button button-primary" target="_blank">Edit in Elementor</a>';
								echo ' - ';
							}
						}
						if ( $post_id ) {
							//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  - SiteURL already escaped WP function.
							echo '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=edit"class="button button-primary" target="_blank">Edit in WordPress</a>';
						} else {
							$url_base = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
							//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  - URL base comes from already escaped safe WP function.
							echo '<a href="' . $url_base . '&regenerate=personalmeeting" class="button button-primary" class="button button-primary">Page Deleted - Click Here to Regenerate</a>';

						}
						?>
					</td>
				</tr>

			</table>

			<h2><?php esc_html_e( 'Customizing the Room', 'my-video-room' ); ?></h2>
			<p><?php esc_html_e( 'You can edit your room look and feel with any page editor of your choice - the page must contain the shortcode ', 'my-video-room' ); ?> <strong> [ccmeetswitch]</strong>	</p>
			<p>
			<?php
			esc_html_e(
				"You can change the room name, its URL, and its parent page in the normal pages interface of WordPress. Please note whilst the system updates its internal
				links if you change the meeting page URL external emails, or other invites may not be updated by your users' guests. Its a good idea to link to reception page from the 
				main area of your site.",
				'my-video-room'
			);
			?>
			</p>
			<p>
			<?php
			esc_html_e(
				'This room will allow any site user to be a Host of their own room, and everyone else will be a guest. Users can change their privacy, as well as room and reception
				layout templates by accessing their own room, and clicking on the Host tab. This will take affect at the next page refresh.',
				'my-video-room'
			);
			?>
			</p>
	</div>
	<div>	
			<h2><?php esc_html_e( 'Room Video Settings', 'my-video-room' ); ?></h2>
			<p>
			<?php
			esc_html_e(
				'These settings determine the default settings a room of this type will use. Once a user has made a choice as to room type, privacy, and template these default settings 
				are no longer used. Can be left blank to allow site master default settings to apply.',
				'my-video-room'
			);
			?>
			</p>
			<p><strong><?php esc_html_e( 'Note', 'my-video-room' ); ?></strong><?php esc_html_e( '- This setting is shared with BuddyPress personal profile room integration as they are the same room.', 'my-video-room' ); ?> </p>
					<?php
						$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
							SiteDefaults::USER_ID_SITE_DEFAULTS,
							MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
							array( 'basic', 'premium' )
						);
						//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  - Layout Setting already Escaped in function.
						echo $layout_setting;
					?>
	</div>

	<div>

	<?php
		Factory::get_instance( ShortcodeDocuments::class )->render_personalmeeting_shortcode_docs();
	?>
	</div>


	<?php

	return ob_get_clean();
};

