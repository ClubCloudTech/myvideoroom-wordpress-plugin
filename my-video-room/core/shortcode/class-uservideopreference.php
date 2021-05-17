<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomExtrasPlugin\Modules\BuddyPress
 */

namespace MyVideoRoomPlugin\Core\Shortcode;

use MyVideoRoomPlugin\Core\Dao\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\Core\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Core\Library\WordPressUser;
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
	public function runtime() {
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

		return $this->choose_settings( $user_id, $room_name, $allowed_tags );
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

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD'] &&
			sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_room_name'] ?? null ) ) === $room_name
		) {
			check_admin_referer( 'myvideoroom_extras_update_user_video_preference', 'nonce' );
			$layout_id               = sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_layout_id_preference'] ?? null ) );
			$reception_id            = sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_reception_id_preference'] ?? null ) );
			$reception_enabled       = sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_reception_enabled_preference'] ?? '' ) ) === 'on';
			$reception_video_enabled = sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_reception_video_enabled_preference'] ?? '' ) ) === 'on';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, - esc_url raw does the appropriate sanitisation.
			$reception_video_url = esc_url_raw( $_POST['myvideoroom_extras_user_reception_waiting_video_url'] );
			$show_floorplan      = sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_show_floorplan_preference'] ?? '' ) ) === 'on';

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

		$available_layouts    = $this->get_available_layouts( $allowed_tags );
		$available_receptions = $this->get_available_receptions( $allowed_tags );

		$render = require __DIR__ . '/../views/shortcode/view-shortcode-uservideopreference.php';
		// Auto Refresh Room Post Settings Change.
		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD'] &&
			sanitize_text_field( wp_unslash( $_POST['myvideoroom_extras_user_room_name'] ?? null ) ) === $room_name
		) {
			$second = 0.1;
			header( "Refresh:$second" );
		}
		return $render( $available_layouts, $available_receptions, $current_user_setting, $room_name, self::$id_index++, $user_id );
	}

	/**
	 * Get a list of available layouts from MyVideoRoom
	 *
	 * @param array $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_layouts( array $allowed_tags = array( 'basic' ) ): array {
		$scenes = $this->get_available_scenes( 'layouts', $allowed_tags );
		if ( $scenes ) {
			return $scenes;
		} else {
			return array( 'No Layouts Found' );
		}
	}

	/**
	 * Get a list of available receptions from MyVideoRoom
	 *
	 * @param array $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_receptions( array $allowed_tags = array( 'basic' ) ): array {
		return $this->get_available_scenes( 'receptions', $allowed_tags );
	}

	/**
	 * Get a list of available scenes from MyVideoRoom
	 *
	 * @param string         $uri The type of scene (layouts/receptions).
	 * @param array|string[] $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_scenes( string $uri, array $allowed_tags = array( 'basic' ) ): array {
		$url     = 'https://rooms.clubcloud.tech/' . $uri;
		$tag_uri = array();

		foreach ( $allowed_tags as $allowed_tag ) {
			$tag_uri[] = 'tag[]=' . $allowed_tag;
		}

		if ( $tag_uri ) {
			$url .= '?' . implode( '&', $tag_uri );
		}

		$request = \wp_remote_get( $url );

		if ( \is_wp_error( $request ) ) {
			return array();
		}

		$body = \wp_remote_retrieve_body( $request );

		return \json_decode( $body );
	}
}
