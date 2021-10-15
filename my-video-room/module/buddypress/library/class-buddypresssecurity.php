<?php
/**
 * Addon functionality for Security Module Support for BuddyPress
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressSecurity
 */

namespace MyVideoRoomPlugin\Module\BuddyPress\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDAO;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;

/**
 * Class BuddyPress
 */
class BuddyPressSecurity {

	/**
	 * BuddyPress Render Security Options Menu Hook.
	 *
	 * @param  int    $user_id - the User ID.
	 * @param  string $room_name - the room name.
	 * @param  int    $id_index - Index counter from form.
	 * @return null|string
	 */
	public function mvrbp_security_menu_hook( int $user_id, string $room_name, int $id_index ) {

		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
			return null;
		}

		$output = null;
		global $bp;
			$is_group_page = $bp->groups->current_group->slug;
			$room_object   = Factory::get_instance( RoomMap::class )->get_room_info( $user_id );

		// Group setting from BP.
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) && function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() && ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_GROUP_ID ) ) && ! $room_object &&
		( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_ID ) ) && $is_group_page ) {
			$output .= esc_attr( Factory::get_instance( BuddyPressConfig::class )->render_group_menu_options( $bp->groups->current_group->creator_id, $room_name, $id_index ) );
		} elseif ( SiteDefaults::USER_ID_SITE_DEFAULTS === $user_id ) {
			$output .= esc_attr( Factory::get_instance( BuddyPressConfig::class )->render_group_menu_options( SiteDefaults::USER_ID_SITE_DEFAULTS, BuddyPress::MODULE_BUDDYPRESS_GROUP_NAME, $id_index ) );
		}
			// Friends Setting from BP.

		if ( ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_FRIENDS_ID ) ) &&
		( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_ID ) ) && ! $room_object && ! $is_group_page ) {
			$output .= esc_attr( Factory::get_instance( BuddyPressConfig::class )->render_friends_menu_options( $user_id, $room_name, $id_index ) );
		}
		return $output;
	}

	/**
	 * Change User ID for BuddyPress Security Filtration
	 *
	 * @param  int $user_id - User ID to Filter.
	 * @return int
	 */
	public function mvrbp_change_user_id( int $user_id ): int {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ||
		! function_exists( 'bp_is_active' ) || ! bp_is_active( 'groups' ) ||
		! ( function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() )
		) {
			return $user_id;
		}
		global $bp;
		return $bp->groups->current_group->creator_id;
	}

	/**
	 * Change Room Name for BuddyPress Security Filtration
	 *
	 *  @param string $room_name - Room Name to Filter.
	 *  @return string
	 */
	public function mvrbp_change_room_name( string $room_name ): string {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ||
		! function_exists( 'bp_is_active' ) ||
		! bp_is_active( 'groups' ) ||
		! ( function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() )
		) {
			return $room_name;
		}
		global $bp;
		return $bp->groups->current_group->slug;
	}
	/**
	 * BuddyPress Render Security Options Menu Hook.
	 *
	 * @param  string $input - inbound filter.
	 * @param  int    $user_id - the User ID.
	 * @param  string $room_name - the room name.
	 * @param  string $host_status - Whether or not user is a host.
	 * @param  string $room_type - Class of Room.
	 * @return null|string
	 */
	public function mvrbp_security_friends_group_filter_hook( string $input, int $user_id, string $room_name, string $host_status, string $room_type ) {
		if ( ! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
			return null;
		}
		// Check BuddyPress Group Membership - and other related if module enabled.
		if ( function_exists( 'bp_is_groups_component' ) && \bp_is_groups_component() ) {
			// Check Group Filter.
			$bp_group_block = $this->block_bp_non_group_member_video_render( $room_name, $host_status, $room_type );
			if ( $bp_group_block ) {
				return $bp_group_block;
			}
		}

		// Check Friend Filter.
		if ( ( strpos( $room_type, 'guest' ) !== false ) ) {
			$bp_friend_block = $this->block_bp_friend_video_render( $user_id, $room_name, $room_type );

			if ( $bp_friend_block ) {
				return $bp_friend_block;
			}
		}
		return $input;
	}

	/**
	 * Block_friends_display - Handles whether Block non friends setting is enabled, and returns to caller whether to block access to room or not
	 *
	 * @param  mixed $user_id - not required. Will take logged in user if not passed.
	 * @return bool
	 */
	public function block_friends_display( $user_id = null ): bool {
		if (
			! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ||
			! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_ID ) ||
			! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_FRIENDS_ID ) ||
			! function_exists( 'bp_is_active' ) ||
			! \bp_is_active( 'friends' )
			) {
			return false;
		}
		$site_override         = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		$site_friends_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'bp_friends_setting' );

		if ( ! $user_id ) {
				$user_id = \bp_displayed_user_id();
		}
			$visitor_id           = get_current_user_id();
			$friends_status       = Factory::get_instance( BuddyPressHelpers::class )->bp_are_users_friends( $user_id, $visitor_id );
			$user_friends_setting = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( $user_id, MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, 'bp_friends_setting' );
		if ( $site_override && $site_friends_override ) {
			$bp_friends_setting = $site_friends_override;
		} else {
			$bp_friends_setting = $user_friends_setting;
		}
		// Controlling Output based on status and overrides above.
		// Are we in Own Profile ?
		if ( $user_id === $visitor_id ) {
			return false;
		}
		// Are We Friends ?
		if ( BuddyPress::SETTING_IS_FRIEND === $friends_status ) {
			return false;

			// Is Setting set to Do Not Disturb (in which case Render Block will need to display a template and we fall through here) OR is Setting Allow All ?
		} elseif ( '' === $bp_friends_setting || BuddyPress::SETTING_DO_NOT_DISTURB === $bp_friends_setting || null === $bp_friends_setting ) {
				return false;
		}
		// If none of the above fire the filter.
			return true;
	}

	/**
	 * This function Checks The Group Membership setting ( allow only group members ) of BuddyPress Groups - and enforces result
	 * Used by Only BuddyPress Groups
	 *
	 * @param  string $room_name    Name of room.
	 * @param  string $room_type    Type of room for inspection.
	 *
	 * @return String  Template if Blocked - null if allowed.
	 */
	public function block_bp_non_group_member_video_render( string $room_name, string $room_type = null ) {
		// Exit for Non Groups.
		if (
			! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ||
			! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_ID ) ||
			! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_GROUP_ID ) ||
			! function_exists( 'bp_is_active' ) ||
			! bp_is_active( 'groups' ) ||
			! bp_is_groups_component()
		) {
			return null;
		}
		// Check Settings.
		global $bp;
		$creator_id = $bp->groups->current_group->creator_id;
		// Check Site Override Status.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );
		if ( $site_override ) {
			$creator_id = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name  = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		}
		// Site Default Settings Section.
		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $creator_id, $room_name, 'room_name' );

		if ( ! $does_room_record_exist ) {
			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'restrict_group_to_members_enabled' );
		} else {
			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( $creator_id, $room_name, 'restrict_group_to_members_enabled' );
		}

		// Exit early if no setting for filter.
		if ( ! $room_access_setting ) {
			return null;
		}

		// Get Global Information on Group.
		$group_id          = $bp->groups->current_group->id;
		$user_id           = get_current_user_id();
		$is_user_member    = Factory::get_instance( BuddyPressHelpers::class )->bp_is_user_member( $group_id, $user_id );
		$is_user_moderator = Factory::get_instance( BuddyPressHelpers::class )->bp_is_user_moderator( $group_id, $user_id );
		$is_user_admin     = Factory::get_instance( BuddyPressHelpers::class )->bp_is_user_admin( $group_id, $user_id );

		switch ( $room_access_setting ) {
			case 'Administrators':
				if ( $is_user_admin ) {
					return null;
				}
				// Check for all Roles starting with Admin - and Fall through.
			case 'Moderators':
				if ( $is_user_admin || $is_user_moderator ) {
					return null;
				}
				// Check for all Roles - and Fall through.
			case 'Members':
				if ( $is_user_admin || $is_user_moderator || $is_user_member ) {
					return null;
				}
				// Else Fire the Block.
		}

		$output = $this->blocked_by_group_membership( $creator_id, $room_type );
		return $output;
	}

	/**
	 * Block Room if BP Module Disabled.
	 *
	 *  @param int $module_id - Room Name to Filter.
	 *  @return string|void
	 */
	public function mvrbp_disabled_module_block( int $module_id ) {
		if (
			Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_ID ) &&
			Factory::get_instance( BuddyPress::class )->is_buddypress_active()
		) {
			if ( BuddyPress::MODULE_BUDDYPRESS_ID === $module_id ) {
				$is_module_enabled = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( $module_id );

				if ( ! $is_module_enabled ) {
					return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_site();
				}
			}
		}
	}

	/**
	 * This function Checks The Friends Membership setting of BuddyPress Friends- and enforces result
	 * Used by Only BuddyPress Friends
	 *
	 * @param  int    $user_id      userid.
	 * @param  string $room_name    Name of room.
	 * @param  string $room_type    Type of room for inspection.
	 *
	 * @return ?String  Template if Blocked - null if allowed.
	 */
	public function block_bp_friend_video_render( int $user_id, string $room_name, string $room_type = null ): ?string {
		if (
			! Factory::get_instance( BuddyPress::class )->is_buddypress_active() ||
			! bp_is_active( 'friends' ) ||
			! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_ID ) ||
			! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( BuddyPress::MODULE_BUDDYPRESS_FRIENDS_ID )
		) {
			return null;
		}

		// Exiting for All room types except personal meeting.
		if ( MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING !== $room_name ) {
			return null;
		}

		$user_id_original = $user_id;
		// Check Site Override Status.
		$site_override = Factory::get_instance( SecurityVideoPreferenceDao::class )
			->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT, 'site_override_enabled' );

		if ( $site_override ) {
			$owner_id  = SiteDefaults::USER_ID_SITE_DEFAULTS;
			$room_name = SiteDefaults::ROOM_NAME_SITE_DEFAULT;
		} else {
			$owner_id = $user_id;
		}

		// Site Default Settings Section.
		$does_room_record_exist = Factory::get_instance( SecurityVideoPreferenceDAO::class )
			->read_security_settings( $owner_id, $room_name, 'room_name' );

		if ( ! $does_room_record_exist ) {
			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( SiteDefaults::USER_ID_SITE_DEFAULTS, Security::PERMISSIONS_TABLE, 'bp_friends_setting' );
		} else {
			$room_access_setting = Factory::get_instance( SecurityVideoPreferenceDAO::class )
				->read_security_settings( $owner_id, $room_name, 'bp_friends_setting' );
		}

		// Exit early if no setting for filter.
		if ( ! $room_access_setting ) {
			return null;
		}

		// Get Global Information on User Relationships to start.
		$visitor_id = get_current_user_id();
		if ( function_exists( 'friends_check_friendship_status' ) ) {
			$friends_status = friends_check_friendship_status( $user_id, $visitor_id );
		}

		switch ( $room_access_setting ) {
			case '':
				return null;
				// Drop Setting Off Cases.
			case BuddyPress::SETTING_DO_NOT_DISTURB:
				if ( $friends_status ) {
					return null;
				} else {
					return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id_original, $room_type );
				}
				// Else Fire the Block.
		}
		return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_user( $user_id_original, $room_type );
	}

	/**
	 * Blocked By Group Membership Template.
	 *
	 * @param  int $user_id - the user ID who is blocking.
	 * @return string
	 */
	public function blocked_by_group_membership( $user_id = null ) {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>
		<div class="mvr-row mvr-background">
			<h2 class="mvr-header-text">
				<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This room is set to Specific Group Members Only', 'myvideoroom' ) . '</h2>';
				?>
				<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

				<p class="mvr-template-text">
					<?php
					$first_display_name  = '<strong>' . esc_html__( 'Group Administrators', 'myvideoroom' ) . '</strong>';
					$second_display_name = '<strong>' . esc_html__( ' the group admins ', 'myvideoroom' ) . '</strong>';

					echo sprintf(
					/* translators: %1s is the text "Site Policy" and %2s is "the site administrators" */
						esc_html__( ' %1$s  only allow specific members to access this group room. Please contact %2$s for more assistance.', 'myvideoroom' ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
						$first_display_name,
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
						$second_display_name
					);
					?>
				</p>
		</div>
		<?php

		return ' ';
	}
}

