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
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Library\ShortcodeDocuments;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;

return function (): string {
	ob_start();

	?>
	<div class="mvr-outer-box-wrap">

	<h1 class="mvr-heading-head"><?php esc_html_e( 'Personal Meeting Rooms', 'my-video-room' ); ?></h1>

		<p>
		<?php
		esc_html_e(
			'A Personal Meeting Room is an individually controlled meeting room with its own Reception Area, Room Layout Selection, Privacy, and Room Permissions. A Reception page is created automatically with the module to handle Guest Arrival. A user is the host of their own room, and everyone else is a guest. Users can send invites by email, or by special unique invite code.',
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
			<?php
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already escaped above.
			echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
			?>

			<h2><?php esc_html_e( 'Customizing the Room', 'my-video-room' ); ?></h2>
			<p><?php esc_html_e( 'You can edit your room look and feel with any page editor of your choice - the page must contain the shortcode ', 'my-video-room' ); ?> <strong> [myvideoroom_meetswitch]</strong>	</p>
			<p>
			<?php
			esc_html_e(
				"You can change the room name, its URL, and its parent page in the normal pages interface of WordPress. Please note whilst the system updates its internal links if you change the meeting page URL external emails, or other invites may not be updated by your users' guests. Its a good idea to link to reception page from the main area of your site.",
				'my-video-room'
			);
			?>
			</p>
			<p>
			<?php
			esc_html_e(
				'This room will allow any site user to be a Host of their own room, and everyone else will be a guest. Users can change their privacy, as well as room and reception layout templates by accessing their own room, and clicking on the Host tab. This will take affect at the next page refresh.',
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
				'These settings determine the default settings a room of this type will use. Once a user has made a choice as to room type, privacy, and template these default settings are no longer used. Can be left blank to allow site master default settings to apply.',
				'my-video-room'
			);
			?>
			</p>
			<p><strong><?php esc_html_e( 'Note', 'my-video-room' ); ?></strong><?php esc_html_e( '- This setting is shared with BuddyPress personal profile room integration as they are the same room.', 'my-video-room' ); ?> </p>
					<?php
						$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
							SiteDefaults::USER_ID_SITE_DEFAULTS,
							MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING
						);
						//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  - Layout Setting already Escaped in function.
						echo $layout_setting;
					?>
	</div>

	<div>

	<!-- 
	<?php
		// Factory::get_instance( ShortcodeDocuments::class )->render_personalmeeting_shortcode_docs();
	?>
	 -->
	</div>


	<?php

	return ob_get_clean();
};

