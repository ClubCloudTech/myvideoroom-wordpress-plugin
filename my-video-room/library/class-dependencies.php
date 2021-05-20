<?php
/**
 * Display Icon Templates in Header of Meetings
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

/**
 * Class Dependencies
 * Manages Shared Info across Certain Modules where cross module calls should be done to Core instead.
 */
class Dependencies {

	const MODULE_BUDDYPRESS_ID       = 434;
	const MODULE_SITE_VIDEO_ID       = 1095;
	const MODULE_PERSONAL_MEETING_ID = 1065;
	const ROOM_NAME_PERSONAL_MEETING = 'personal-video-room';
	const MODULE_SECURITY_ENTITY_ID  = 1029;
	const MODULE_SECURITY_ID         = 10;
	const MULTI_ROOM_HOST_SUFFIX     = '-hostsetting';

	/**
	 * Is Buddypress Active - checks if BuddyPress is enabled.
	 *
	 * @return bool
	 */
	public function is_buddypress_active(): bool {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		return is_plugin_active( 'buddypress/bp-loader.php' );
	}
}
