<?php
/**
 * This Class formats module settings in order of preference going upstream from user level, to module level, to site level and returns
 * the correct parameters to ensure default settings are always applied
 *
 * One function is added per setting
 *
 * @package MyVideoRoomPlugin\Core\VideoHelpers
 */

namespace MyVideoRoomPlugin\Core;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\DAO\ModuleConfig;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\Core\Dao\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomExtrasPlugin\Modules\MVRSiteVideo\MVRSiteVideo;

/**
 * Class Template
 */
class VideoHelpers extends Shortcode {

	/**
	 * For Video Room Template - Order of Preference Function.
	 * This function will try to get the Video Room Template from locally defined up to module level and then site default
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name = the room name to check.
	 * @param  bool   $multi_owner = Flag for Site Video Multi-tenanted case.
	 * @return string Video Room Template if any.
	 */
	public function get_videoroom_template( int $user_id, string $room_name, bool $multi_owner = false ) {
		// First try the User's Value.
		$video_preference_dao = factory::get_instance( UserVideoPreferenceDao::class );
		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( $current_user_setting && $current_user_setting->get_layout_id() ) {
			return $current_user_setting->get_layout_id();
		}
		// Now Try the Category Preference.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			$room_name
		);

		if ( $current_user_setting && $current_user_setting->get_layout_id() ) {
			return $current_user_setting->get_layout_id();
		}
		// Multi-Owner Case in SiteVideo.
		$sitevideo_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( SiteDefaults::MODULE_SECURITY_ID );
		if ( $multi_owner ) {
			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO
			);
			if ( $current_user_setting && $current_user_setting->get_layout_id() ) {
				return $current_user_setting->get_layout_id();
			}
		}

		// Now Try the Main Site Default.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( $current_user_setting && $current_user_setting->get_layout_id() ) {
			return $current_user_setting->get_layout_id();
		} else {
			return null;
		}
	}

	/**
	 * For Video Reception URL - Order of Preference Function.
	 * This function will try to get the reception URL status from locally defined up to module level and then site default
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name = the room name to check.
	 * @param  bool   $multi_owner = Flag for Site Video Multi-tenanted case.
	 * @return string url if any.
	 */
	public function get_video_reception_url( int $user_id, string $room_name, bool $multi_owner = false ) {
		// First try the User's Value.
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );
		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_reception_video_url_setting();
		}
		// Now Try the Category Preference.

			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				$room_name
			);

		if ( $current_user_setting ) {
			return $current_user_setting->get_reception_video_url_setting();
		}
		// Multi-Owner Case in SiteVideo.
		if ( $multi_owner ) {
			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO
			);
			if ( $current_user_setting && $current_user_setting->get_reception_video_url_setting() ) {
				return $current_user_setting->get_reception_video_url_setting();
			}
		}
		// Now Try the Main Site Default.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_reception_video_url_setting();
		} else {
			return null;
		}
	}


	/**
	 * For Video Reception State - Order of Preference Function.
	 * This function will try the reception status from locally defined up to modeule level and then site default
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name = the room name to check.
	 * @param  bool   $multi_owner = Flag for Site Video Multi-tenanted case.
	 * @return string Video Reception State.
	 */
	public function get_video_reception_state( int $user_id, string $room_name, bool $multi_owner = false ) {
		// First try the User's Value.
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );
		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_reception_video_enabled_setting();
		}
		// Now Try the Category Preference.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			$room_name
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_reception_video_enabled_setting();
		}

		// Multi-Owner Case in SiteVideo.
		if ( $multi_owner ) {
			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO
			);
			if ( $current_user_setting && $current_user_setting->get_reception_video_enabled_setting() ) {
				return $current_user_setting->get_reception_video_enabled_setting();
			}
		}
		// Now Try the Main Site Default.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_reception_video_enabled_setting();
		} else {
			return null;
		}
	}

	/**
	 * For Video Reception Template - Order of Preference Function.
	 * This function will try to get the reception Template from locally defined up to module level and then site default
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name = the room name to check.
	 * @param  bool   $multi_owner = Flag for Site Video Multi-tenanted case.
	 * @return string Reception template
	 */
	public function get_reception_template( int $user_id, string $room_name, bool $multi_owner = false ) {
		// First try the User's Value.
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );
		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( $current_user_setting && $current_user_setting->get_reception_id() ) {
			return $current_user_setting->get_reception_id();
		}
		// Now Try the Category Preference.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			$room_name
		);

		if ( $current_user_setting && $current_user_setting->get_reception_id() ) {
			return $current_user_setting->get_reception_id();
		}
		// Multi-Owner Case in SiteVideo.
		if ( $multi_owner ) {
			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO
			);
			if ( $current_user_setting && $current_user_setting->get_reception_id() ) {
				return $current_user_setting->get_reception_id();
			}
		}
		// Now Try the Main Site Default.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( $current_user_setting && $current_user_setting->get_reception_id() ) {
			return $current_user_setting->get_reception_id();
		} else {
			return null;
		}
	}
	/**
	 * For Video Reception Enabled State - Order of Preference Function.
	 * This function will try to get the reception status from locally defined up to module level and then site default
	 *
	 * @param  int    $user_id - userid.
	 * @param  string $room_name = the room name to check.
	 * @param  bool   $multi_owner = Flag for Site Video Multi-tenanted case.
	 * @return bool - Video Reception State.
	 */
	public function get_enable_reception_state( int $user_id, string $room_name, bool $multi_owner = false ) {
		// First try the User's Value.
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );
		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( $current_user_setting ) {
			return $current_user_setting->is_reception_enabled();
		}
		// Now Try the Category Preference.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			$room_name
		);
		if ( $current_user_setting ) {
			return $current_user_setting->is_reception_enabled();
		}
		// Multi-Owner Case in SiteVideo.
		if ( $multi_owner ) {
			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO
			);
			if ( $current_user_setting && $current_user_setting->is_reception_enabled() ) {
				return $current_user_setting->is_reception_enabled();
			}
		}
		// Now Try the Main Site Default.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( $current_user_setting ) {
			return $current_user_setting->is_reception_enabled();
		} else {
			return null;
		}
	}

	/**
	 * Show Floorplan Function
	 * Gets the floorplan setting for a user
	 *
	 * @param  int    $user_id - required.
	 * @param  string $room_name - required.
	 * @param  bool   $multi_owner = Flag for Site Video Multi-tenanted case.
	 * @return string Floorplan status.
	 */
	public function get_show_floorplan( int $user_id, string $room_name, bool $multi_owner = false ) {
		// First try the User's Value.
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );
		$current_user_setting = $video_preference_dao->read(
			$user_id,
			$room_name
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_show_floorplan_setting();
		}
		// Now Try the Category Preference.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			$room_name
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_show_floorplan_setting();
		}

		// Multi-Owner Case in SiteVideo.
		if ( $multi_owner ) {
			$current_user_setting = $video_preference_dao->read(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO
			);
			if ( $current_user_setting && $current_user_setting->get_show_floorplan_setting() ) {
				return $current_user_setting->get_show_floorplan_setting();
			}
		}
		// Now Try the Main Site Default.

		$current_user_setting = $video_preference_dao->read(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			SiteDefaults::ROOM_NAME_SITE_DEFAULT
		);

		if ( $current_user_setting ) {
			return $current_user_setting->get_show_floorplan_setting();
		} else {
			return null;
		}
	}

	/**
	 * Render an Elementor Template by the id
	 *
	 * @param int $elementor_template_id The id of the elementor template id.
	 *
	 * @return string
	 */
	public function call_elementor_template( int $elementor_template_id ): string {
		return \do_shortcode( '[elementor-template id="' . $elementor_template_id . '"]' );
	}

}
