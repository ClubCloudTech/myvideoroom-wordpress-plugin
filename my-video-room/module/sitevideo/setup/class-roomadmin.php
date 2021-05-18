<?php
/**
 * Room Admin Functions for MVR Site Video
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\RoomAdmin
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Setup;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomAdmin as RoomAdminSetup;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Class RoomAdmin
 */
class RoomAdmin extends RoomAdminSetup {

	const TABLE_NAME = SiteDefaults::TABLE_NAME_ROOM_MAP;
	/**
	 * Create a page into the Worpress environment, register in page table, and ensure its enabled.
	 *
	 * @param  string $room_name - name of room to build.
	 * @param  string $display_title - Title of Page.
	 * @param  string $slug - Worpress Slug to assign page.
	 * @param  string $room_type - Type of Room in DB.
	 * @param  string $old_post_id - Type Old Room in DB to update the value to the new post..
	 * @return null  - page executes database functions doesn't return to user.
	 */
	public function create_and_check_page( string $room_name, string $display_title, string $slug, string $room_type, string $old_post_id = null ) {
		// Check Page Doesn't already Exist in Database and hasn't been deleted if it does.
		$check_page_exists = Factory::get_instance( RoomMap::class )->check_page_exists( $room_name );

		// Check_page_exists has three states, Yes, No, Or Orphan - if yes - exit function, if no create the room, if orphan delete room mapping in database and create room again.
		if ( 'Yes' === $check_page_exists ) {
			return null;
		}

		// Create Page in DB as Page doesn't exist.
		$post_id          = wp_insert_post(
			array(
				'post_author' => 1,
				'post_title'  => get_bloginfo( 'name' ) . ' ' . $display_title,
				'post_name'   => strtolower( str_replace( ' ', '-', trim( $slug ) ) ),
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);
			$post_content = array(
				'ID'           => $post_id,
				'post_content' => '[' . MVRSiteVideo::ROOM_SHORTCODE_SITE_VIDEO . ' id="' . $post_id . '"]',
			);
			wp_update_post( $post_content );
			if ( $old_post_id ) {
				// Update Database References to New Post IDs to ensure Room Permissions and Settings stay intact with New Pages.
				Factory::get_instance( UserVideoPreferenceDao::class )->update_post_id( $post_id, $old_post_id );

				$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( SiteDefaults::MODULE_SECURITY_ID );
				if ( $security_enabled ) {
					Factory::get_instance( \MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference::class )->update_post_id( $post_id, $old_post_id );
				}
			}

			// Insert into DB as Page Didn't Exist.
			if ( 'No' === $check_page_exists ) {
				Factory::get_instance( RoomMap::class )->register_room_in_db( $room_name, $post_id, $room_type, $display_title, $slug );
				return null;
			} elseif ( 'Orphan' === $check_page_exists ) {
				// Update the DB if Orphan.
				Factory::get_instance( RoomMap::class )->update_room_post_id( $room_name, $post_id, $room_type, $display_title, $slug );
				return null;
			}
	}
}
