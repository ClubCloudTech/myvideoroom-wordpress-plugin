<?php
/**
 * Addon functionality for BuddyPress -Video Room Handlers for BuddyPress
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressConfig
 */

namespace MyVideoRoomPlugin\Module\BuddyPress\Library;

/**
 * Class BuddyPress
 */
class BuddyPressHelpers {

	/**
	 * Modify UserID for Groups Hook
	 *
	 * @param int $user_id - required.
	 * @return string
	 */
	public function modify_user_id_for_groups( int $user_id ) {
		global $bp;
		if ( function_exists( 'bp_is_groups_component' ) && \bp_is_groups_component() && $bp->groups->current_group->id ) {
			$user_id = $bp->groups->current_group->id;
		}
		return $user_id;
	}

}
