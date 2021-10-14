<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomPlugin\Module\Security\Shortcode
 */

namespace MyVideoRoomPlugin\Module\Security\Shortcode;

use MyVideoRoomPlugin\DAO\SessionState;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDao;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\Shortcode\App;
use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class SecurityVideoPreference
 */
class SecurityVideoPreference {
	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_choose_security_settings';
	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Provide Init
	 */
	public function init() {
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'choose_security_settings_shortcode' ) );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array|string $attributes List of shortcode params.
	 *
	 * @return string
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
				return;
			}

			$room_name = $http_post_library->get_string_parameter( 'room_name' );
			$user_id   = $http_post_library->get_integer_parameter( 'user_id' );

			$security_preference_dao = Factory::get_instance( SecurityVideoPreferenceDao::class );

			$current_user_setting = $security_preference_dao->get_by_id(
				$user_id,
				$room_name
			);

			$blocked_roles              = $http_post_library->get_string_parameter( 'security_blocked_roles_preference' );
			$room_disabled              = $http_post_library->get_checkbox_parameter( 'security_room_disabled_preference' );
			$anonymous_enabled          = $http_post_library->get_checkbox_parameter( 'security_anonymous_enabled_preference' );
			$allow_role_control_enabled = $http_post_library->get_checkbox_parameter( 'security_allow_role_control_enabled_preference' );
			$block_role_control_enabled = $http_post_library->get_checkbox_parameter( 'security_block_role_control_enabled_preference' );
			$site_override_enabled      = $http_post_library->get_checkbox_parameter( 'override_all_preferences' );
			$bp_friends_setting         = $http_post_library->get_string_parameter( 'security_restrict_bp_friends' );
			$bp_group_setting           = $http_post_library->get_string_parameter( 'security_restrict_group_to_members' );
			$timestamp                  = current_time( 'timestamp' );

			// Handle Multi_box array and change it to a Database compatible string.
			$allowed_roles_list = $http_post_library->get_string_list_parameter( 'security_allowed_roles_preference' );
			$allowed_roles      = implode( '|', $allowed_roles_list );

			if ( $current_user_setting ) {

				$current_user_setting
					->set_allowed_roles( $allowed_roles )
					->set_blocked_roles( $blocked_roles )
					->set_room_disabled( $room_disabled )
					->set_anonymous_enabled( $anonymous_enabled )
					->set_allow_role_control_enabled( $allow_role_control_enabled )
					->set_block_role_control_enabled( $block_role_control_enabled )
					->set_site_override_setting( $site_override_enabled )
					->set_timestamp( $timestamp );
				// Handle BuddyPress Module (extension not in a filter as BP is in core security database already).
				if ( $bp_friends_setting ) {
					if ( 'all' === $bp_friends_setting ) {
						$bp_friends_setting = '';
					}
					$current_user_setting->set_bp_friends_setting( $bp_friends_setting );
				}
				if ( $bp_group_setting ) {
					if ( 'off' === $bp_group_setting ) {
						$bp_group_setting = '';
					}
					$current_user_setting->set_restrict_group_to_members_setting( $bp_group_setting );
				}

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
					$site_override_enabled,
					$bp_group_setting,
					$bp_friends_setting,
					$timestamp
				);

				$security_preference_dao->create( $current_user_setting );
			}

			\do_action( 'myvideoroom_security_preference_persisted', $current_user_setting );

			// Notify Global Change Queue.
			Factory::get_instance( SessionState::class )->notify_user( $room_name );
		}
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param int         $user_id    The user id.
	 * @param string      $room_name  The room name.
	 * @param  ?string     $group_name Name of group.
	 * @param string|null $type       To return.
	 *
	 * @return string
	 */
	public function choose_settings( int $user_id, string $room_name, string $group_name = null, string $type = null ): string {
		// User ID Transformation for plugins.
		$user_id = apply_filters( 'myvideoroom_security_choosesettings_change_user_id', $user_id );

		$security_preference_dao = Factory::get_instance( SecurityVideoPreferenceDao::class );

		$site_override_permissions = Factory::get_instance( SecurityVideoPreferenceDAO::class )->get_by_id( SiteDefaults::USER_ID_SITE_DEFAULTS, SiteDefaults::ROOM_NAME_SITE_DEFAULT );
		if ( $site_override_permissions && $site_override_permissions->is_site_override_enabled() && ( 'admin' !== $type || 'roomhost' !== $type ) ) {
			$current_user_setting = $site_override_permissions;
			$roles_output         = $this->read_multi_checkbox_admin_roles( $site_override_permissions->get_user_id(), $site_override_permissions->get_room_name() );
		} else {
			$current_user_setting = $security_preference_dao->get_by_id(
				$user_id,
				$room_name
			);
			$roles_output         = $this->read_multi_checkbox_admin_roles( $user_id, $room_name );
		}

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

		return $render( $current_user_setting, $room_name, self::$id_index ++, $roles_output, $user_id, $group_name );

	}


	/**
	 * Reads WordPress Roles, and Merges with Security Settings stored in DB to render Multi-Select Dialog Boxes
	 *
	 * @param int    $user_id   - The User_ID.
	 * @param string $room_name - Name of Room.
	 *
	 * @return string
	 */
	public function read_multi_checkbox_admin_roles( int $user_id, string $room_name ): string {
		// Setup.
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$output    = null;

		// Get Settings in Database - return type - matches the field in the database - return it on top.
		$preference = Factory::get_instance( SecurityVideoPreferenceDao::class )->get_by_id( $user_id, $room_name );

		$allowed_roles = array();

		if ( $preference ) {
			$allowed_roles = $preference->get_roles();
		}

		// Add Clear Option to Select Box if there are parameters Stored.
		$clear_option = '';
		if ( $allowed_roles ) {
			$clear_option = '<option value="">' . esc_html__( '(Clear Selections - Remove Stored Roles)', 'myvideoroom' ) . '</option>';
		}

		foreach ( $all_roles as $key => $value ) {
			if ( in_array( $key, $allowed_roles, true ) ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_html( $value['name'] ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $value['name'] ) . '</option>';
			}
		}

		return $clear_option . $output;
	}
}
