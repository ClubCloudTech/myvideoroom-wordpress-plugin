<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomExtrasPlugin\Module\UserVideoPreference
 */

namespace MyVideoRoomPlugin\Shortcode;

use MyVideoRoomPlugin\DAO\SessionState;
use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Factory;

/**
 * Class UserVideoPreference
 * Allows the user to select their room display parameters and persist those settings.
 */
class UserVideoPreference {
	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_choose_settings';
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
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'choose_settings_shortcode' ) );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param string|array $params List of shortcode params.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function choose_settings_shortcode( $params = array() ): string {
		$room_name = $params['room'] ?? 'default';
		$user_id   = $params['user'] ?? null;

		if ( ! $user_id ) {
			$user_id = Factory::get_instance( WordPressUser::class )->get_logged_in_wordpress_user()->ID;
		}

		$this->check_for_update_request();

		return $this->choose_settings( $user_id, $room_name );
	}

	/**
	 * Check for updating the user video preference
	 *
	 * @throws \Exception @TODO - remove me!.
	 */
	public function check_for_update_request() {
		$http_post_library = Factory::get_instance( HttpPost::class );

		if ( $http_post_library->is_post_request( 'update_user_video_preference' ) ) {
			if ( ! $http_post_library->is_nonce_valid( 'update_user_video_preference' ) ) {
				?>
				<script type="text/javascript">
				location.reload()
				</script>
				<?php
			}

			$room_name = $http_post_library->get_string_parameter( 'room_name' );
			$user_id   = $http_post_library->get_integer_parameter( 'user_id' );

			$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );

			$current_user_setting = $video_preference_dao->get_by_id(
				$user_id,
				$room_name
			);

			$layout_id               = $http_post_library->get_string_parameter( 'user_layout_id_preference' );
			$reception_id            = $http_post_library->get_string_parameter( 'user_reception_id_preference' );
			$reception_enabled       = $http_post_library->get_checkbox_parameter( 'user_reception_enabled_preference' );
			$reception_video_enabled = $http_post_library->get_checkbox_parameter( 'user_reception_video_enabled_preference' );
			$reception_video_url     = $http_post_library->get_string_parameter( 'user_reception_waiting_video_url' );
			$show_floorplan          = $http_post_library->get_checkbox_parameter( 'user_show_floorplan_preference' );
			$timestamp               = current_time( 'timestamp' );

			if ( $current_user_setting ) {
				$current_user_setting->set_layout_id( $layout_id )
					->set_reception_id( $reception_id )
					->set_reception_enabled( $reception_enabled )
					->set_reception_video_enabled_setting( $reception_video_enabled )
					->set_reception_video_url_setting( $reception_video_url )
					->set_show_floorplan_setting( $show_floorplan )
					->set_timestamp( $timestamp );

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
					$show_floorplan,
					$timestamp
				);
				$video_preference_dao->create( $current_user_setting );
			}
			// Notify Global Change Queue.
			Factory::get_instance( SessionState::class )->notify_user( $room_name );
		}
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param int    $user_id   The user id to fetch.
	 * @param string $room_name The room name to fetch.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function choose_settings( int $user_id, string $room_name ): string {
		// User ID Transformation for plugins.
		$user_id              = apply_filters( 'myvideoroom_video_choosesettings_change_user_id', $user_id );
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );

		$current_user_setting = $video_preference_dao->get_by_id(
			$user_id,
			$room_name
		);

		$available_scenes_library = Factory::get_instance( AvailableScenes::class );

		$available_layouts    = $available_scenes_library->get_available_layouts();
		$available_receptions = $available_scenes_library->get_available_receptions();

		if ( ! $available_layouts ) {
			return \esc_html__( 'No Layouts Found', 'myvideoroom' );
		}

		$render = require __DIR__ . '/../views/shortcode/view-shortcode-uservideopreference.php';

		return $render( $available_layouts, $available_receptions, $current_user_setting, $room_name, self::$id_index ++, $user_id );
	}
}
