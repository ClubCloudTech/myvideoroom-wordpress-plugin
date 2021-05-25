<?php
/**
 * Data Access Object for user video preferences default room setup
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Factory;

/**
 * Class UserVideoPreference
 */
class RoomInit {
	const TABLE_NAME = SiteDefaults::TABLE_NAME_ROOM_MAP;

	/**
	 * Room Default Settings Install
	 *
	 * @param int    $user_id                     The UserID.
	 * @param string $room_name                   The Room Name.
	 * @param string $layout_id_to_set            Template.
	 * @param string $reception_id_to_set         Default Reception View.
	 * @param bool   $reception_enabled           Status of Reception.
	 * @param bool   $overwrite_existing_settings If existing settings should be overwritten.
	 *
	 * @return void
	 */
	public function room_default_settings_install(
		int $user_id,
		string $room_name,
		string $layout_id_to_set,
		string $reception_id_to_set,
		bool $reception_enabled,
		bool $overwrite_existing_settings = true
	) {
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );

		// Check Exists.
		$current_user_setting = $video_preference_dao->get_by_id(
			$user_id,
			$room_name
		);

		if ( $current_user_setting ) {
			if ( $overwrite_existing_settings ) {
				$current_user_setting->set_layout_id( $layout_id_to_set )
					->set_reception_id( $reception_id_to_set )
					->set_reception_enabled( $reception_enabled );
				$video_preference_dao->update( $current_user_setting );
			}
		} else {
			$current_user_setting = new UserVideoPreferenceEntity(
				$user_id,
				$room_name,
				$layout_id_to_set,
				$reception_id_to_set,
				$reception_enabled
			);
			$video_preference_dao->create( $current_user_setting );
		}
	}
}




