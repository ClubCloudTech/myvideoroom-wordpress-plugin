<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\SiteVideo\Setup\RoomAdmin;
/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoListeners extends Shortcode {

	// ---
	// Site Video Room Templates.


	/**
	 * Render Site Videoroom Regenerate Page Engine.
	 *
	 * @return void|null
	 */
	public function site_videoroom_regenerate_page() {

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['myvideoroom_regenerate_room_name'] ) && isset( $_POST['myvideoroom_regenerate_page_id'] ) &&
			( 'POST' === $_SERVER['REQUEST_METHOD'] )
		) {
			check_admin_referer( 'myvideoroom_regenerate_page', 'nonce' );
			$room_name      = sanitize_text_field( wp_unslash( $_POST['myvideoroom_regenerate_room_name'] ) );
			$old_page_id    = sanitize_text_field( wp_unslash( $_POST['myvideoroom_regenerate_page_id'] ) );
			$check_is_empty = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, 'post_id' );

			if ( ! $check_is_empty ) {
				if ( MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $room_name ) {
					Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_name );
					Factory::get_instance( MVRSiteVideo::class )->create_site_videoroom_page( $old_page_id );
				} else {
					$room_object  = Factory::get_instance( RoomMap::class )->get_room_info( $old_page_id );
					$room_name    = $room_object->room_name;
					$display_name = $room_object->display_name;
					$slug         = $room_object->slug;
					$room_type    = MVRSiteVideo::ROOM_SHORTCODE_SITE_VIDEO;
					// Delete Objects and Rebuild.
					Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_name );
					Factory::get_instance( RoomAdmin::class )->create_and_check_page( $room_name, $display_name, $slug, $room_type, $old_page_id );
				}

				// @TODO Alec check this
				echo '<h1>Page Refresh Completed</h1>';
				$second = .5;
				header( "Refresh:$second" );
				return null;
			}
		}
	}
	/**
	 * Handle Room Delete Requests Site Videoroom Page Engine.
	 *
	 * @return void|null
	 */
	public function site_videoroom_delete_page() {

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['myvideoroom_delete_page_id'] ) &&
			( 'POST' === $_SERVER['REQUEST_METHOD'] )
		) {
			check_admin_referer( 'myvideoroom_delete_page', 'nonce' );
			$page_id     = sanitize_text_field( wp_unslash( $_POST['myvideoroom_delete_page_id'] ) );
			$room_id     = intval( $page_id );
			$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
			$room_name   = $room_object->room_name;
			// Now Delete.
			Factory::get_instance( RoomMap::class )->delete_room_mapping( $room_name );
			wp_delete_post( $room_id, true );

			echo '<h1>Page Delete Completed</h1>';
			wp_enqueue_script( 'myvideoroom-frame-refresh' );
			return null;
		}

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_GET['delete'] ) && isset( $_GET['id'] ) && ( 'true' === $_GET['delete'] ) ) {
			$page_id      = sanitize_text_field( wp_unslash( $_GET['id'] ) );
			$room_id      = intval( $page_id );
			$room_object  = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
			$display_name = $room_object->display_name;
			?>

			<div class="mvr-nav-shortcode-outer-wrap mvr-security-room-host">
				<form method="post" action="">
					<h1><?php esc_html_e( 'Are you sure you want to delete ', 'my-video-room' ) . esc_textarea( $display_name ) . '?'; ?> </h1>
					<p class="mvr-add-page"> <?php esc_html_e( 'This action cannot be undone', 'my-video-room' ); ?> </p>
					<input name="myvideoroom_delete_page_id" type="hidden" value="<?php echo esc_attr( $room_id ); ?>" />
					<?php wp_nonce_field( 'myvideoroom_delete_page', 'nonce' ); ?>
					<i class="dashicons dashicons-dismiss"></i>
						<input type="submit" name="submit" id="submit" class="dashicons-image-rotate" value="Delete Room" />
					</form>
			</div>

			<?php
			return null;
		}
	}
}
