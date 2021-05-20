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
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\Security\Settings\Field\Checkbox;
use MyVideoRoomPlugin\Module\Security\Settings\Field\Text;

/**
 * Class Module
 */
class Module {
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
	 * @param callable                      $register_setting          Callback to add an option.
	 * @param SecurityVideoPreferenceEntity $security_video_preference The security settings.
	 */
	public function add_security_settings( callable $register_setting, SecurityVideoPreferenceEntity $security_video_preference ) {
		$settings = null;
		if ( $security_video_preference ) {
			$settings = Factory::get_instance( Dao::class )->get_by_id( $security_video_preference->get_id() );
		}

		$restrict_group_to_members = new Checkbox(
			'restrict_group_to_members',
			__( 'Restrict security group to members', 'myvideoroom' ),
			__( 'This is a long description', 'myvideoroom' ),
			$settings && $settings->is_restrict_group_to_members_enabled()
		);

		$register_setting( $restrict_group_to_members );

		$restrict_bp_friends = new Text(
			'restrict_bp_friends',
			__( 'Restricted friends setting', 'myvideoroom' ),
			__( 'This is a long description', 'myvideoroom' ),
			$settings ? $settings->get_friend_restriction() : ''
		);

		$register_setting( $restrict_bp_friends );
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

		$restrict_group_to_members_setting = $http_post_library->get_checkbox_parameter( 'security_restrict_group_to_members' );
		$bp_friends_setting                = $http_post_library->get_string_parameter( 'security_restrict_bp_friends' );

		$settings = new Settings(
			$security_video_preference->get_id(),
			$restrict_group_to_members_setting,
			$bp_friends_setting
		);

		Factory::get_instance( Dao::class )->persist( $settings );

		return $security_video_preference;
	}


}
