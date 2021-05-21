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
	 * @return void
	 */
	public function site_videoroom_display_rooms() {

		wp_enqueue_style( 'myvideoroom-template' );
		wp_enqueue_style( 'myvideoroom-menutab-header' );
		$available_rooms = Factory::get_instance( RoomMap::class )->get_room_list( MVRSiteVideo::ROOM_SHORTCODE_SITE_VIDEO );

		foreach ( $available_rooms as $room_id ) {
			$room_object  = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
			$room_name    = $room_object->room_name;
			$post_id      = $room_object->post_id;
			$display_name = $room_object->display_name;
			?>
<tr data-room-id="<?php echo esc_attr( $room_id ); ?>">
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
				// phpcs:ignore Squiz.WhiteSpace.LanguageConstructSpacing.IncorrectSingle, ESCURL is escaped properly.
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

			$edit_actions = array(
				array(
					__( 'Edit in WordPress' ),
					get_site_url() . '/wp-admin/post.php?post=' . esc_textarea( $post_id ) . '&action=edit',
					'dashicons dashicons-wordpress',
				),
			);

			// Add any extra options.
			$edit_actions = \apply_filters( 'myvideoroom_sitevideo_edit_actions', $edit_actions, $post_id );

			// Render Buttons for Action Area.
			if ( $post_id_return ) {

				foreach ( $edit_actions as $action ) {
					echo '<a href="' . esc_url( $action[1] ) . '" class="mvr-icons ' . esc_attr( $action[2] ) . '" target="_blank" title="' . esc_attr( $action[0] ) . '"></a>';
				}

				//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
				if ( null !== ( esc_url_raw( wp_unslash( $_GET['page'] ) ) ) ){
				//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
					$slug = admin_url( 'admin.php?page=' . esc_textarea( wp_unslash( $_GET['page'] ) ) );
				}

				$settings_url = \add_query_arg( array( 'room_id' => $room_id ), \esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ) );

				$delete_nonce = wp_create_nonce( 'delete_room_' . $room_id );
				$delete_url   = \add_query_arg(
					array(
						'room_id'  => $room_id,
						'confirm'  => null,
						'delete'   => 'true',
						'_wpnonce' => $delete_nonce,
					),
					\esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
				);

				?>
					<a
						href="<?php echo esc_url( $settings_url ); ?>"
						class="dashicons mvr-icons dashicons-admin-generic myvideoroom-sitevideo-settings"
						data-room-id="<?php echo esc_attr( $room_id ); ?>"
						title="<?php esc_html_e( 'View settings', 'myvideoroom' ); ?>"
					></a>

					<a
						href="<?php echo esc_url( $delete_url ); ?>"
						class="dashicons mvr-icons dashicons-dismiss myvideoroom-sitevideo-delete"
						data-room-id="<?php echo esc_attr( $room_id ); ?>"
						data-nonce="<?php echo esc_attr( $delete_nonce ); ?>"
						title="<?php esc_html_e( 'Delete room', 'myvideoroom' ); ?>"
					></a>
				<?php
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
