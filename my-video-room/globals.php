<?php
/**
 * My Video Room Globals.
 * These are declarations for global functions outside Class Taxonomy.
 *
 * @package MyVideoRoomPlugin/globals.php
 */

declare(strict_types=1);

use MyVideoRoomPlugin\Library\Maintenance;

if ( ! function_exists( 'mvr_trim_room_presence_table' ) ) {
	/**
	 * Wrapper for Elemental Get WCFM Memberships Function
	 *
	 * @return array|false|int|mixed|string|void|null
	 */
	function mvr_trim_room_presence_table() {
		$function_to_call = new Maintenance();
		return $function_to_call->mvr_trim_room_presence_table( ...func_get_args() );
	}
}
do_action( 'myvideoroom_global_function_register' );
