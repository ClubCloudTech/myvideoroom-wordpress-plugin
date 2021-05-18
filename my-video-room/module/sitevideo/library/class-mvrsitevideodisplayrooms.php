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
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomAdmin;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;


/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoDisplayRooms extends Shortcode {

	// ---
	// Site Video Room Display Rooms.

	/**
	 * Render Site Videoroom Available Rooms.
	 *
	 * @param  string $type - type of display.
	 * @return void
	 */
	public function site_videoroom_display_rooms( $type = null ) {
		wp_enqueue_style( 'mvr-template' );
		wp_enqueue_style( 'mvr-menutab-header' );
		$available_rooms = Factory::get_instance( RoomMap::class )->get_room_list( MVRSiteVideo::ROOM_SHORTCODE_SITE_VIDEO );
		foreach ( $available_rooms as $room_id ) {
			$room_object  = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
			$room_name    = $room_object->room_name;
			$post_id      = $room_object->post_id;
			$display_name = $room_object->display_name;
			?>
<tr>
<td class="mvr-table-height">
			<?php
				$title = $display_name;
			if ( $title ) {
				echo esc_html( $title );
			}
			?>
</td>
<td class="mvr-table-height">
			<?php
				$url = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, 'url' );
			if ( $url ) {
				//phpcs:ignore --Squiz.WhiteSpace.LanguageConstructSpacing.IncorrectSingle - ESCURL is escaped properly.
				echo  '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>';
			}
			?>
</td>
<td class="mvr-table-height">
			<?php
			$post_id_return = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( $room_name, 'post_id' );
			if ( $post_id_return ) {
				echo '<strong>[ ' . esc_html( MVRSiteVideo::ROOM_SHORTCODE_SITE_VIDEO ) . ' id="' . esc_html( $post_id_return ) . '" ]</strong>';
			}
			?>
</td>
<td class="mvr-table-height mvr-table-editpage">
			<?php
			// Render Buttons for Action Area.
			if ( $post_id_return ) {
				if ( Factory::get_instance( SiteDefaults::class )->is_elementor_active() ) {
					echo '<a href="' . esc_url( get_site_url() ) . '/wp-admin/post.php?post=' . esc_textarea( $post_id ) . '&action=elementor" class="fab fa-elementor mvr-icons"target="_blank" title="Edit in Elementor"></a>';
				}
				echo '<a href="' . esc_url( get_site_url() ) . '/wp-admin/post.php?post=' . esc_textarea( $post_id ) . '&action=edit" class="dashicons mvr-icons dashicons-wordpress" target="_blank" title="Edit in WordPress"></a>';

				//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
				if ( null !== ( esc_url_raw( wp_unslash( $_GET['page'] ) ) ) ){
				//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
					$slug = admin_url( 'admin.php?page=' . esc_textarea( wp_unslash( $_GET['page'] ) ) );
				}
				echo '<a href="#" class="dashicons mvr-icons dashicons-admin-generic myvideoroom-sitevideo-settings" data-post-id="' . esc_attr( $post_id ) . '"></a>';
				echo '<a href="' . esc_url_raw( $slug ) . '&tab=' . esc_textarea( MVRSiteVideo::MODULE_ROOM_MANAGEMENT_NAME ) . '&id=' . esc_textarea( $post_id ) . '&delete=true" class="dashicons mvr-icons dashicons-dismiss" target="iframe1" title="' . esc_html__( 'Delete Room', 'my-video-room' ) . '"></a>';
			} else {
				?>
				<form method="post" action="">
				<input name="myvideoroom_regenerate_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />
				<input name="myvideoroom_regenerate_page_id" type="hidden" value="<?php echo esc_attr( $post_id ); ?>" />

					<?php wp_nonce_field( 'myvideoroom_regenerate_page', 'nonce' ); ?>
					<i class="dashicons dashicons-image-rotate"></i>
					<input type="submit" name="submit" id="submit" class="dashicons-image-rotate" value="Repair" />
				</form>
				<?php
			}
			?>
</td>
</tr>
			<?php
		}

	}
}
