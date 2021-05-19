<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomExtrasPlugin\Modules\BuddyPress
 */

namespace MyVideoRoomPlugin\Core\Shortcode;

use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\Core\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Shortcode;

/**
 * Class UserVideoPreference
 * Allows the user to select their room display parameters and persist those settings.
 */
class UserVideoPreference extends Shortcode {
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
		$this->add_shortcode( '_choose_settings', array( $this, 'choose_settings_shortcode' ) );
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

		$room_name    = $params['room'] ?? 'default';
		$user_id      = $params['user'] ?? null;
		$allowed_tags = array_map( 'trim', explode( ',', $params['tags'] ?? '' ) );

		if ( ! $user_id ) {
			$user_id = Factory::get_instance( WordPressUser::class )->get_logged_in_wordpress_user()->ID;
		}

		$this->check_for_update_request( );
		return $this->choose_settings( $user_id, $room_name, $allowed_tags );
	}

	public function check_for_update_request( ) {

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD'] &&
			'uservideopreference' === ( $_POST['myvideoroom_type'] ?? '' )
		) {
			$room_name = sanitize_text_field( wp_unslash( $_POST['myvideoroom_room_name'] ?? '' ) );
			$user_id = (int) sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_id'] ?? -1 ) );

			$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );

			$current_user_setting = $video_preference_dao->read(
				$user_id,
				$room_name
			);

			$layout_id               = sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_layout_id_preference'] ?? null ) );
			$reception_id            = sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_reception_id_preference'] ?? null ) );
			$reception_enabled       = sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_reception_enabled_preference'] ?? '' ) ) === 'on';
			$reception_video_enabled = sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_reception_video_enabled_preference'] ?? '' ) ) === 'on';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, - esc_url raw does the appropriate sanitisation.
			$reception_video_url = esc_url_raw( $_POST['myvideoroom_user_reception_waiting_video_url'] );
			$show_floorplan      = sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_show_floorplan_preference'] ?? '' ) ) === 'on';

			if ( $current_user_setting ) {
				$current_user_setting->set_layout_id( $layout_id )
					->set_reception_id( $reception_id )
					->set_reception_enabled( $reception_enabled )
					->set_reception_video_enabled_setting( $reception_video_enabled )
					->set_reception_video_url_setting( $reception_video_url )
					->set_show_floorplan_setting( $show_floorplan );

				$video_preference_dao->update( $current_user_setting );
			} else {
				$current_user_setting = new UserVideoPreferenceEntity(
					$user_id,
					$room_name,
					$layout_id,
					$reception_id,
					$reception_enabled,
					$reception_video_enabled,
					$reception_video_url,
					$show_floorplan
				);
				$video_preference_dao->create( $current_user_setting );
			}
		}
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param int    $user_id The user id to fetch.
	 * @param string $room_name The room name to fetch.
	 * @param array  $allowed_tags List of tags to allow.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function choose_settings( int $user_id, string $room_name, array $allowed_tags = array() ): string {
		// Trap BuddyPress Environment and send Group ID as the User ID for storage in DB.
		// phpcs:ignore MyVideoRoomPlugin\Shortcode\bp_is_groups_component() is a Buddypress function.
		if ( function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() ) {
			global $bp;
			$user_id = $bp->groups->current_group->creator_id;
		}
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );

		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		$available_scenes_library = Factory::get_instance( AvailableScenes::class );

		$available_layouts    = $available_scenes_library->get_available_layouts();
		$available_receptions = $available_scenes_library->get_available_receptions();

		if ( ! $available_layouts ) {
			return \esc_html__( 'No Layouts Found', 'myvideoroom' );
		}

		$render = require __DIR__ . '/../../views/shortcode/view-shortcode-uservideopreference.php';

		// Auto Refresh Room Post Settings Change.
		// @TODO - ALEC - check if we can sort this.
		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD'] &&
			sanitize_text_field( wp_unslash( $_POST['myvideoroom_user_room_name'] ?? null ) ) === $room_name
		) {
			echo( "<meta http-equiv='refresh' content='.1'>" );
		}
		return $render( $available_layouts, $available_receptions, $current_user_setting, $room_name, self::$id_index++, $user_id );
	}
}
