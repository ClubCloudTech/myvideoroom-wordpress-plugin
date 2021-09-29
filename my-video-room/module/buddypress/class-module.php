<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\BuddyPress;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\BuddyPress\Entity\Settings;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\Security\Settings\Field\Select;
use MyVideoRoomPlugin\Module\Security\Settings\Field\SelectOption;

/**
 * Class Module
 */
class Module {
	const MEMBER_OPTION_NONE      = 'none';
	const MEMBER_OPTION_ADMIN     = 'admin';
	const MEMBER_OPTION_MODERATOR = 'moderator';
	const MEMBER_OPTION_MEMBERS   = 'members';
	const MEMBER_OPTION_ALL       = 'all';

	const MEMBER_OPTIONS = array(
		self::MEMBER_OPTION_NONE,
		self::MEMBER_OPTION_ADMIN,
		self::MEMBER_OPTION_MODERATOR,
		self::MEMBER_OPTION_MEMBERS,
		self::MEMBER_OPTION_ALL,
	);

	const FRIEND_OPTION_OFF            = 'off';
	const FRIEND_OPTION_STEALTH        = 'stealth';
	const FRIEND_OPTION_DO_NOT_DISTURB = 'do-no-disturb';
	const FRIEND_OPTION_ALLOW_ALL      = 'allow_all';

	const FRIEND_OPTIONS = array(
		self::FRIEND_OPTION_OFF,
		self::FRIEND_OPTION_STEALTH,
		self::FRIEND_OPTION_DO_NOT_DISTURB,
		self::FRIEND_OPTION_ALLOW_ALL,
	);

	/**
	 * Module constructor.
	 */
	public function __construct() {
		\add_action( 'myvideoroom_security_preference_persisted', array( $this, 'update_security_video_preference' ) );
		\add_action( 'myvideoroom_security_preference_settings', array( $this, 'add_security_settings' ), 10, 2 );
	}

	/**
	 * Add security settings to settings page
	 *
	 * @param callable                       $register_setting          Callback to add an option.
	 * @param ?SecurityVideoPreferenceEntity $security_video_preference The security settings.
	 */
	public function add_security_settings( callable $register_setting, SecurityVideoPreferenceEntity $security_video_preference = null ) {
		$settings = null;
		if ( $security_video_preference ) {
			$settings = Factory::get_instance( Dao::class )->get_by_id( $security_video_preference->get_id() );
		}

		$member_restrictions = new Select(
			'restrict_group_to_members',
			__( 'BuddyPress Group Member Restrictions', 'myvideoroom' ),
			__( 'You can select if you want to make the group available to all Administrators, Moderators, Members, or normal (no access control), for example teh "Administrator Only" setting will only allow Group Administrators to enter the room, "Administrators and Moderators" will allow only group admins and moderators to enter video, and "Members" will allow admins, moderators, and members to enter.', 'myvideoroom' ),
			array(
				new SelectOption( self::MEMBER_OPTION_NONE, __( 'User decides', 'myvideoroom' ) ),
				// @FRED I assume this needs some logic around it.
				new SelectOption( self::MEMBER_OPTION_ADMIN, __( 'Administrators only', 'myvideoroom' ) ),
				new SelectOption( self::MEMBER_OPTION_MODERATOR, __( 'Moderators and above', 'myvideoroom' ) ),
				new SelectOption( self::MEMBER_OPTION_MODERATOR, __( 'Members and above', 'myvideoroom' ) ),
				new SelectOption( self::MEMBER_OPTION_ALL, __( 'Turned off - all roles allowed', 'myvideoroom' ) ),
			),
			$settings ? $settings->get_member_restriction() : null
		);
		$register_setting( $member_restrictions );

		$friend_restriction = new Select(
			'restrict_bp_friends',
			__( 'BuddyPress friends only room access control', 'myvideoroom' ),
			__( 'You can choose if you want to restrict access to your video room. This settings has an option to allow all users to access your room (default), or to enable access control. If you enable access control the are two options: "Stealth mode" - will just remove your video room from  you profile from you non-friends (and blocked users), or "Do not disturb" - which will show your room entrance on your profile, but will block any user that tries to access your reception with a message. In either case you will not be notified of waiting guests.', 'myvideoroom' ),
			array(
				new SelectOption( self::FRIEND_OPTION_OFF, __( 'Turned Off', 'myvideoroom' ) ),
				new SelectOption( self::FRIEND_OPTION_STEALTH, __( 'Stealth - Remove video tab from My Profile to non-friends', 'myvideoroom' ) ),
				new SelectOption( self::FRIEND_OPTION_DO_NOT_DISTURB, __( 'Do not disturb - shown to non-friends', 'myvideoroom' ) ),
				new SelectOption( self::FRIEND_OPTION_ALLOW_ALL, __( 'Allow all - both friends and non-friends allowed', 'myvideoroom' ) ),
			),
			$settings ? $settings->get_friend_restriction() : null
		);

		$register_setting( $friend_restriction );

	}

	/**
	 * Update the security video preference
	 *
	 * @param SecurityVideoPreferenceEntity $security_video_preference The updated security video preference.
	 *
	 * @return SecurityVideoPreferenceEntity
	 */
	public function update_security_video_preference( SecurityVideoPreferenceEntity $security_video_preference ): SecurityVideoPreferenceEntity {
		$http_post_library = Factory::get_instance( HttpPost::class );

		$current_settings = Factory::get_instance( Dao::class )->get_by_id( $security_video_preference->get_id() );

		if ( $http_post_library->has_parameter( 'security_restrict_group_to_members' ) ) {
			$member_restriction = $http_post_library->get_string_parameter( 'security_restrict_group_to_members' );
		} elseif ( $current_settings ) {
			$member_restriction = $current_settings->get_member_restriction();
		} else {
			$member_restriction = null;
		}

		if ( $http_post_library->has_parameter( 'security_restrict_bp_friends' ) ) {
			$friend_restriction = $http_post_library->get_string_parameter( 'security_restrict_bp_friends' );
		} elseif ( $current_settings ) {
			$friend_restriction = $current_settings->get_friend_restriction();
		} else {
			$friend_restriction = null;
		}

		if (
			! in_array( $member_restriction, self::MEMBER_OPTIONS, true ) ||
			! in_array( $friend_restriction, self::FRIEND_OPTIONS, true )
		) {

			return $security_video_preference;
		}

		$settings = new Settings(
			$security_video_preference->get_id(),
			$member_restriction,
			$friend_restriction
		);

		Factory::get_instance( Dao::class )->persist( $settings );

		return $security_video_preference;
	}


}
