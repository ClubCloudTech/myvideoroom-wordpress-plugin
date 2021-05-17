<?php
/**
 * Display Icon Templates in Header of Meetings
 *
 * @package MyVideoRoomPlugin\Core\Library
 */

namespace MyVideoRoomPlugin\Core\Library;

/**
 * Class Dependencies
 * Manages Shared Info across Certain Modules where cross module calls should be done to Core instead.
 */
class Dependencies {

	const MODULE_BUDDYPRESS_ID       = 434;
	const MODULE_SITE_VIDEO_ID       = 1095;
	const MODULE_PERSONAL_MEETING_ID = 1065;
	const MODULE_SECURITY_ENTITY_ID  = 1029;
	const MODULE_SECURITY_ID         = 10;
	const MULTI_ROOM_HOST_SUFFIX     = '-hostsetting';

	/**
	 * Is Buddypress Active - checks if BuddyPress is enabled.
	 *
	 * @return bool
	 */
	public function is_buddypress_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			// plugin is active.
			return true;
		} else {
			return false;
		}
	}

}
