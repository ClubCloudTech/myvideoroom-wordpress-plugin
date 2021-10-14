<?php
/**
 * Addon functionality for BuddyPress -Video Room Handlers for BuddyPress
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressConfig
 */

namespace MyVideoRoomPlugin\Module\BuddyPress\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class BuddyPress
 */
class BuddyPressConfig {

	/**
	 * Render_group_menu_options
	 *
	 * @param    int    $user_id - required.
	 * @param    string $room_name - required.
	 * @param    int    $id_index - required.
	 * @return   null
	 */
	public function render_group_menu_options( int $user_id, string $room_name, int $id_index ) {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
			return null;
		}
		?>

		<label for="myvideoroom_security_restrict_group_to_members_<?php echo esc_attr( $id_index ); ?>">
				<h2><i class="dashicons mvr-icons dashicons-format-chat"></i> <?php esc_html_e( 'BuddyPress Group Member Restrictions', 'myvideoroom' ); ?></h2>
				</label>
				<select 
						style = "width:50%"
						class="mvr-roles-multiselect mvr-select-box"
						name="myvideoroom_security_restrict_group_to_members"
						id="myvideoroom_security_restrict_group_to_members_<?php echo esc_attr( $id_index ); ?>"
				>
		<?php

		$current_selection = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'restrict_group_to_members_enabled' );
		$site_override     = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

		if ( SiteDefaults::USER_ID_SITE_DEFAULTS === $user_id ) {
			$is_in_own_room = false;
		}

		if ( ! $current_selection ) {
			if ( $site_override ) {
				$current_selection = '<option value="" selected>Current Setting->Override Active - Turned Off</option>';
			} elseif ( ! $site_override && ! $is_in_own_room ) {
				$current_selection = '<option value="" selected>Current Setting->User Decides</option>';
			} elseif ( ! $site_override && $is_in_own_room ) {
				$current_selection = '<option value="" selected>Current Setting->Turned Off</option>';
			} else {
				$current_selection = '<option value="" selected>Current Setting->Turned Off</option>';
			}
		} else {
			$current_selection = '<option value="' . esc_attr( $current_selection ) . '">Currently Set To : ' . esc_html( $current_selection ) . '</option>';
		}
		esc_html_e( 'Currently set to:', 'myvideoroom' );
		// phpcs:ignore --WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized  --  Sanitised in construction of options above.
		echo $current_selection;
		?>
						<option value="Administrators"><?php esc_html_e( 'Administrators Only', 'myvideoroom' ); ?></option>	
						<option value="Moderators"><?php esc_html_e( 'Moderators and above', 'myvideoroom' ); ?></option>
						<option value="Members"><?php esc_html_e( 'Members and above', 'myvideoroom' ); ?></option>
						<option value="off"><?php esc_html_e( 'Turned Off- all roles allowed', 'myvideoroom' ); ?></option>				
					</select><br>
							<p class="mvr-preferences-paragraph">
							<?php
							esc_html_e(
								'You can select if you want to make the group available to all Administrators, Moderators, Members, or Normal ( no access control ),
								for example the "Administrator Only" setting will only allow Group Administrators to enter the video room, "Administrator and Moderators" will 
								allow only group admins and moderators to enter video, and Members - will allow admins, moderators, and members to enter.',
								'myvideoroom'
							);
							?>
							</p><br>
		<?php
		return null;
	}

	/**
	 * Render_friends_menu_options - Formats Friends Security Dialog Box selectors.
	 *
	 * @param    int    $user_id - required.
	 * @param    string $room_name - required.
	 * @param    int    $id_index - required.
	 * @return   null
	 */
	public function render_friends_menu_options( int $user_id, string $room_name, int $id_index ) {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
			return null;
		}
		?>
		<label for="myvideoroom_security_restrict_bp_friends_<?php echo esc_attr( $id_index ); ?>">
				<h2><i class="dashicons mvr-icons dashicons-share"></i><?php esc_html_e( 'BuddyPress Friends Only Room Access Control', 'myvideoroom' ); ?></h2>
				</label>
				<select 
						style = "width:60%"
						class="mvr-roles-multiselect mvr-select-box"
						name="myvideoroom_security_restrict_bp_friends"
						id="myvideoroom_security_restrict_bp_friends_<?php echo esc_attr( $id_index ); ?>"
				>
		<?php
		$site_override     = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		$current_selection = Factory::get_instance( SecurityVideoPreferenceDAO::class )->read_security_settings( $user_id, $room_name, 'bp_friends_setting' );
		// Format Display Box Default Message Correctly for No Setting Returned.
		if ( ! $current_selection && ! $site_override ) {
			$current_selection_text = 'Current Setting->Turned Off';
		} elseif ( ! $current_selection && $site_override ) {
			$current_selection_text = 'Current Setting->Override Active - Turned Off';
		} elseif ( ! $current_selection && ! $site_override ) {
				$current_selection_text = 'Current Setting->User Decides';
		}
		// Format Display Box if there WAS a setting returned.
		if ( ! $current_selection_text ) {
			$current_selection_text = 'Currently set to: ' . $current_selection;
		}
		?>
						<option value="<?php echo esc_html( $current_selection ); ?>"><?php echo esc_html( $current_selection_text ); ?> </option>
						<option value="Stealth-Remove-Video"><?php esc_html_e( 'Stealth- Remove Video Tab from My Profile to Non-Friends', 'myvideoroom' ); ?></option>	
						<option value="Do-Not-Disturb"><?php esc_html_e( 'Do Not Disturb Page - Show to Non Friends', 'myvideoroom' ); ?></option>
						<option value="all"><?php esc_html_e( 'Allow All- Friends and Non-Friends allowed', 'myvideoroom' ); ?></option>			
					</select><br>
							<p class="mvr-preferences-paragraph">
							<?php
							esc_html_e(
								'You can choose if you want to restrict access to your Video Room to your friends. This setting has an option to allow all users to access your room(default), or to enable access control. If you enable access control, there are two options: ',
								'myvideoroom'
							);
							?>
							</p>
							<p class="mvr-preferences-paragraph">
							<?php
							esc_html_e(
								'Stealth Mode - will just remove your video room from your profile from your non-friends (and Blocked users). Show Do 
								not Disturb- will show your room entrance on your profile but will block any user that tries to access your reception with a message. In any case you will not be notified.',
								'myvideoroom'
							);
							?>
							</p>
							</p>
							<p class="mvr-preferences-paragraph">
							<?php
							esc_html_e(
								'Show Do Not Disturb- will show your room entrance on your profile but will block any user that tries to access your reception with a message. In any case you will not be notified.',
								'myvideoroom'
							);
							?>
							</p>
		<?php
		return null;
	}
}

