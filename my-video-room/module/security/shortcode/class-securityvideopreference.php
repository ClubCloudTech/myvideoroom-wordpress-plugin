<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomPlugin\Module\Security\Shortcode
 */

namespace MyVideoRoomPlugin\Module\Security\Shortcode;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDao;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;

/**
 * Class SecurityVideoPreference
 */
class SecurityVideoPreference extends Shortcode {
	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Provide Runtime
	 */
	public function init() {
		$this->add_shortcode( 'choose_security_settings', array( $this, 'choose_settings_shortcode' ) );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array $params List of shortcode params.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function choose_settings_shortcode( $params = array() ): string {
		$room_name = $params['room'] ?? 'default';
		$user_id   = $params['user'] ?? null;

		if ( ! $user_id ) {
			$user_id = $this->get_instance( WordPressUser::class )->get_logged_in_wordpress_user()->ID;
		}

		return $this->choose_settings( $user_id, $room_name );
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param  int    $user_id - the user id.
	 * @param  string $room_name - the room name.
	 * @param  string $group_name - name of group.
	 * @param  string $type to return.
	 * @return string
	 */
	public function choose_settings( int $user_id, string $room_name, string $group_name = null, $type = null ): string {
		// Trap BuddyPress Environment and send Group ID as the USer ID for storage in DB.
		if ( Factory::get_instance( Dependencies::class )->is_buddypress_active() ) {
			if ( function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() ) {
				global $bp;
				$user_id = $bp->groups->current_group->creator_id;
			}
		}
		$security_preference_dao = Factory::get_instance( SecurityVideoPreferenceDao::class );
		$current_user_setting    = $security_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( isset( $_SERVER['REQUEST_METHOD'] )
			&& 'POST' === $_SERVER['REQUEST_METHOD']
			&& sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_room_name'] ?? null ) ) === $room_name
			) {
			check_admin_referer( 'myvideoroom_update_security_video_preference', 'nonce' );
			$blocked_roles              = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_blocked_roles_preference'] ?? null ) );
			$room_disabled              = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_room_disabled_preference'] ?? '' ) ) === 'on';
			$anonymous_enabled          = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_anonymous_enabled_preference'] ?? '' ) ) === 'on';
			$allow_role_control_enabled = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_allow_role_control_enabled_preference'] ?? '' ) ) === 'on';
			$block_role_control_enabled = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_block_role_control_enabled_preference'] ?? '' ) ) === 'on';
			//phpcs:ignore --WordPress.Security.ValidatedSanitizedInput.InputNotValidated - input sanitised but not mandatory.
			$restrict_group_to_members_setting = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_restrict_group_to_members'] ) );
			$site_override_enabled             = sanitize_text_field( wp_unslash( $_POST['myvideoroom_override_all_preferences'] ?? '' ) ) === 'on';
			//phpcs:ignore --WordPress.Security.ValidatedSanitizedInput.InputNotValidated - input sanitised but not mandatory.
			$bp_friends_setting                = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_restrict_bp_friends'] ) );

			// Handle Default Off State for Group Restrictions.
			if ( $restrict_group_to_members_setting ) {
				if ( 'Turned Off' === $restrict_group_to_members_setting || '' === $restrict_group_to_members_setting ) {
					$restrict_group_to_members_setting = null;
				}
			}

			// Handle Multi_box array and change it to a Database compatible string.
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized  --  Sanitised in function below (post MultiBox Array dissasembly that is destroyed by WP sanitising functions).
			$inbound_multibox = $_POST['myvideoroom_security_allowed_roles_preference'] ?? null;
			if ( $inbound_multibox ) {
				$output_data = array_unique( $inbound_multibox ); // ensure there are no duplicated roles.
				sort( $output_data );

				$sanitized_output_data = array_map(
					function ( $item ) {
						return sanitize_text_field( wp_unslash( $item ) );
					},
					$output_data
				);
				$allowed_roles         = implode( '|', $sanitized_output_data );
			}
			// now sanitise.
			$allowed_roles = sanitize_text_field( wp_unslash( $allowed_roles ) );
			if ( $current_user_setting ) {

				$current_user_setting
					->set_allowed_roles( $allowed_roles )
					->set_blocked_roles( $blocked_roles )
					->set_room_disabled( $room_disabled )
					->set_anonymous_enabled( $anonymous_enabled )
					->set_allow_role_control_enabled( $allow_role_control_enabled )
					->set_block_role_control_enabled( $block_role_control_enabled )
					->set_restrict_group_to_members_setting( $restrict_group_to_members_setting )
					->set_site_override_setting( $site_override_enabled )
					->set_bp_friends_setting( $bp_friends_setting );
				$security_preference_dao->update( $current_user_setting );
			} else {

				$current_user_setting = new SecurityVideoPreferenceEntity(
					$user_id,
					$room_name,
					$allowed_roles,
					$blocked_roles,
					$room_disabled,
					$anonymous_enabled,
					$allow_role_control_enabled,
					$block_role_control_enabled,
					$site_override_enabled,
					$restrict_group_to_members_setting,
					$bp_friends_setting
				);
				$security_preference_dao->create( $current_user_setting );
			}
		}
		// Auto Refresh Room Post Settings Change.
		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD'] &&
			sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_room_name'] ?? null ) ) === $room_name
		) {
			echo( "<meta http-equiv='refresh' content='.1'>" );
		}
		if ( 'admin' === $type ) {
				$render = include __DIR__ . '/../views/shortcode-securityadminvideopreference.php';
		}
		if ( 'roomhost' === $type ) {
				$render = include __DIR__ . '/../views/shortcode-securityroomhost.php';
		} else {
			$render = include __DIR__ . '/../views/shortcode-securityvideopreference.php';
		}
		return $render( $current_user_setting, $room_name, self::$id_index++, $user_id, $group_name );

	}
}
