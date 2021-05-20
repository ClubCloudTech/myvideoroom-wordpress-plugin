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

/**
 * Class Module
 */
class Module {
	/**
	 * Module constructor.
	 */
	public function __construct() {
		\add_action( 'myvideoroom_securityvideopreference_persisted', array( $this, 'update_security_video_preference' ) );
		\add_action( 'myvideoroom_security_preference_settings', array( $this, 'add_security_settings' ) );
	}

	/**
	 * Add security settings to settings page
	 *
	 * @param SecurityVideoPreferenceEntity $security_video_preference The security settings.
	 */
	public function add_security_settings( SecurityVideoPreferenceEntity $security_video_preference ) {
		ob_start();

		$settings = null;

		if ( $security_video_preference ) {
			$settings = Factory::get_instance( Dao::class )->get_by_id( $security_video_preference->get_id() );
		}

		$checked             = '';
		$restrict_bp_friends = '';

		if ( $settings ) {
			if ( $settings->is_restrict_group_to_members_enabled() ) {
				$checked = ' checked';
			}

			$restrict_bp_friends = $settings->get_friend_restriction();
		}

		?>

		myvideoroom_security_restrict_group_to_members:
		<input type="checkbox" name="myvideoroom_security_restrict_group_to_members"<?php echo $checked; ?>/>
		<br />

		myvideoroom_security_restrict_bp_friends:
		<input type="text" name="myvideoroom_security_restrict_bp_friends" maxlength="255" value="<?php echo $restrict_bp_friends; ?>" />
		<br />
		<?php

		echo ob_get_clean();
	}

	/**
	 * Update the security video preference
	 *
	 * @param SecurityVideoPreferenceEntity $security_video_preference The updated security video preference.
	 *
	 * @return SecurityVideoPreferenceEntity
	 */
	public function update_security_video_preference( SecurityVideoPreferenceEntity $security_video_preference ) {
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
