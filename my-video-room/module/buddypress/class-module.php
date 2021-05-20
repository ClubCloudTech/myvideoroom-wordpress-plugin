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
		\add_action( 'myvideoroom_security_preference_form', array( $this, 'add_security_settings' ) );
	}

	public function add_security_settings( SecurityVideoPreferenceEntity $security_video_preference ) {
		ob_start();

		$restrict_to_members = false;
		$restrict_bp_friends = '';

		if ( $security_video_preference ) {
			global $wpdb;
			$query  = $wpdb->prepare(
				'SELECT restrict_group_to_members_enabled, bp_friends_setting FROM ' . $wpdb->prefix . 'myvideoroom_buddypress WHERE record_id = %s',
				$security_video_preference->get_id()
			);
			$result = $wpdb->get_row( $query );

			if ( $result ) {
				$restrict_to_members = (bool) $result->restrict_group_to_members_enabled;
				$restrict_bp_friends = $result->bp_friends_setting;
			}
		}

		$checked = '';
		if ( $restrict_to_members ) {
			$checked = ' checked';
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


	public function update_security_video_preference( SecurityVideoPreferenceEntity $security_video_preference ) {
		$http_post_library = Factory::get_instance( HttpPost::class );

		$restrict_group_to_members_setting = $http_post_library->get_checkbox_parameter( 'security_restrict_group_to_members' );
		$bp_friends_setting                = $http_post_library->get_string_parameter( 'security_restrict_bp_friends' );

		// Handle Default Off State for Group Restrictions.
		if ( $restrict_group_to_members_setting ) {
			if ( 'Turned Off' === $restrict_group_to_members_setting || '' === $restrict_group_to_members_setting ) {
				$restrict_group_to_members_setting = null;
			}
		}

		global $wpdb;
		// Create Post.
		$wpdb->replace(
			$wpdb->prefix . 'myvideoroom_buddypress',
			array(
				'record_id'                         => $security_video_preference->get_id(),
				'restrict_group_to_members_enabled' => (int) $restrict_group_to_members_setting,
				'bp_friends_setting'                => $bp_friends_setting,
			),
			array(
				'record_id'                         => '%d',
				'restrict_group_to_members_enabled' => '%d',
				'bp_friends_setting'                => '%s',
			)
		);

		return $security_video_preference;
	}


}
