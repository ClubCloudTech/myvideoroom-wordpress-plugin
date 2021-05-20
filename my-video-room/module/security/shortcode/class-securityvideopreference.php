<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomPlugin\Module\Security\Shortcode
 */

namespace MyVideoRoomPlugin\Module\Security\Shortcode;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDao;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\ValueObject\Notice;

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
		$this->add_shortcode( 'choose_security_settings', array( $this, 'choose_security_settings_shortcode' ) );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array|string $attributes List of shortcode params.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function choose_security_settings_shortcode( $attributes = array() ): string {
		$room_name = $attributes['room'] ?? 'default';
		$user_id   = $attributes['user'] ?? null;

		if ( ! $user_id ) {
			$user_id = Factory::get_instance( WordPressUser::class )->get_logged_in_wordpress_user()->ID;
		}

		$this->check_for_update_request();
		return $this->choose_settings( $user_id, $room_name );
	}

	/**
	 * Check if this is an update request
	 */
	public function check_for_update_request() {
		$http_post_library = Factory::get_instance( HttpPost::class );

		if ( $http_post_library->is_post_request( 'update_security_video_preference' ) ) {
			if ( ! $http_post_library->is_nonce_valid( 'update_security_video_preference' ) ) {
				// @TODO - FIX ME/HANDLE ME/...
				throw new \Exception( 'Invalid nonce' );
			}

			$room_name = $http_post_library->get_string_parameter( 'room_name' );
			$user_id   = $http_post_library->get_integer_parameter( 'user_id' );

			$security_preference_dao = Factory::get_instance( SecurityVideoPreferenceDao::class );

			$current_user_setting = $security_preference_dao->read(
				$user_id,
				$room_name
			);

			$blocked_roles              = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_blocked_roles_preference'] ?? null ) );
			$room_disabled              = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_room_disabled_preference'] ?? '' ) ) === 'on';
			$anonymous_enabled          = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_anonymous_enabled_preference'] ?? '' ) ) === 'on';
			$allow_role_control_enabled = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_allow_role_control_enabled_preference'] ?? '' ) ) === 'on';
			$block_role_control_enabled = sanitize_text_field( wp_unslash( $_POST['myvideoroom_security_block_role_control_enabled_preference'] ?? '' ) ) === 'on';

			$site_override_enabled             = sanitize_text_field( wp_unslash( $_POST['myvideoroom_override_all_preferences'] ?? '' ) ) === 'on';

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
					->set_site_override_setting( $site_override_enabled );

				$security_preference_dao->update( $current_user_setting );
			} else {

				$current_user_setting = new SecurityVideoPreferenceEntity(
					null,
					$user_id,
					$room_name,
					$allowed_roles,
					$blocked_roles,
					$room_disabled,
					$anonymous_enabled,
					$allow_role_control_enabled,
					$block_role_control_enabled,
					$site_override_enabled
				);

				$security_preference_dao->create( $current_user_setting );
			}


			/**
			 * Update the current user setting
			 *
			 * @var SecurityVideoPreferenceEntity $current_user_setting
			 */
			\do_action( 'myvideoroom_security_preference_persisted', $current_user_setting );
		}
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param  int         $user_id    The user id.
	 * @param  string      $room_name  The room name.
	 * @param  ?string     $group_name Name of group.
	 * @param string|null $type       To return.
	 *
	 * @return string
	 */
	public function choose_settings( int $user_id, string $room_name, string $group_name = null, string $type = null ): string {
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

		// Type of Shortcode to render.
		switch ( $type ) {
			case 'admin':
				$render = include __DIR__ . '/../views/shortcode-securityadminvideopreference.php';
				break;
			case 'roomhost':
				$render = include __DIR__ . '/../views/shortcode-securityroomhost.php';
				break;
			default:
				$render = include __DIR__ . '/../views/shortcode-securityvideopreference.php';
		}

		return $render( $current_user_setting, $room_name, self::$id_index++, $user_id, $group_name );
	}
}
