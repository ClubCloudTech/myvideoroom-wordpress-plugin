<?php
/**
 * Room Admin Functions for MVR Site Video
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\RoomAdmin
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Setup;

use MyVideoRoomPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDao;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Class RoomAdmin
 */
class RoomAdmin {

	const TABLE_NAME = SiteDefaults::TABLE_NAME_ROOM_MAP;

	/**
	 * Create a page into the WordPress environment, register in page table, and ensure its enabled.
	 *
	 * @param string  $room_name     Name of room to build.
	 * @param string  $display_title Title of Page.
	 * @param string  $slug          WordPress Slug to assign page.
	 * @param string  $room_type     Type of Room in DB.
	 * @param  ?string $shortcode     Shortcode to store for room.
	 * @param  ?string $old_post_id   Type Old Room in DB to update the value to the new post..
	 * @param  ?string $redirect_page  Redirect Page flag - sets the function to create a redirect page.
	 *
	 * @return integer
	 */
	public function create_and_check_sitevideo_page( string $room_name, string $display_title, string $slug, string $room_type, string $shortcode = null, string $old_post_id = null, string $redirect_page = null ): ?int {
		// Check Page Doesn't already Exist in Database and hasn't been deleted if it does.
		$check_page_exists = Factory::get_instance( RoomMap::class )->check_page_exists( $room_name );
		// Set status of redirect page to private rather than default publish.
		if ( ! $redirect_page ) {
			$post_status = 'publish';
		} else {
			$post_status = 'private';
		}

		// Check_page_exists has three states, Yes, No, Or Orphan - if yes - exit function, if no create the room, if orphan delete room mapping in database and create room again.
		if ( RoomMap::PAGE_STATUS_EXISTS === $check_page_exists ) {
			$post_id = Factory::get_instance( RoomMap::class )->get_post_id_by_room_name( $room_name );

			wp_update_post(
				array(
					'ID'          => $post_id,
					'post_author' => 1,
					'post_name'   => strtolower( str_replace( ' ', '-', trim( $slug ) ) ),
					'post_status' => $post_status,
					'post_type'   => 'page',
				)
			);
			\do_action( 'myvideoroom_post_room_create', $slug, $display_title );
			return $post_id;
		}

		// Create Page in DB as Page doesn't exist.
		$post_id = wp_insert_post(
			array(
				'post_author' => 1,
				'post_title'  => get_bloginfo( 'name' ) . ' ' . $display_title,
				'post_name'   => strtolower( str_replace( ' ', '-', trim( $slug ) ) ),
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_status' => $post_status,
			)
		);

		if ( $redirect_page ) {
			$post_content = array(
				'ID'           => $post_id,
				'post_content' => '[' . MVRSiteVideo::SHORTCODE_REDIRECT . '"]',
			);
		} else {
			$post_content = array(
				'ID'           => $post_id,
				'post_content' => '[' . MVRSiteVideo::SHORTCODE_SITE_VIDEO . ' id="' . $post_id . '"]',
			);
			\do_action( 'myvideoroom_post_room_create', $slug, $display_title );
		}

		wp_update_post( $post_content );
		wp_publish_post( $post_id );

		if ( $old_post_id ) {
			// Update Database References to New Post IDs to ensure Room Permissions and Settings stay intact with New Pages.
			Factory::get_instance( UserVideoPreferenceDao::class )->update_user_id( $post_id, $old_post_id );

			// Handle case if other modules have tables to update.
			\do_action( 'myvideoroom_page_delete_post_number_refresh', $post_id, $old_post_id );
		}

		// Insert into DB as Page Didn't Exist.
		if ( RoomMap::PAGE_STATUS_NOT_EXISTS === $check_page_exists ) {
			Factory::get_instance( RoomMap::class )->register_room_in_db( $room_name, $post_id, $room_type, $display_title, $slug, $shortcode );
		} elseif ( RoomMap::PAGE_STATUS_ORPHANED === $check_page_exists ) {
			// Update the DB if Orphan.
			Factory::get_instance( RoomMap::class )->update_room_post_id( $post_id, $room_name );
		}

		return $post_id;
	}

	/**
	 * Initialise Room Category Default Settings for Site Video.
	 *
	 * @return bool
	 */
	public function initialise_default_sitevideo_settings(): bool {
		$video_preference_dao = Factory::get_instance( UserVideoPreferenceDao::class );

		// Check Exists.
		$current_user_setting = $video_preference_dao->get_by_id(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
		);

		if ( ! $current_user_setting ) {
			$current_user_setting = new UserVideoPreferenceEntity(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				MVRSiteVideo::ROOM_NAME_SITE_VIDEO,
				'boardroom',
				'default',
				false,
			);
			$video_preference_dao->create( $current_user_setting );
		}

		return true;
	}
}
