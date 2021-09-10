<?php
/**
 * Addon functionality for BuddyPress - Template Views
 *
 * @package MyVideoRoomPlugin\Modules\BuddyPress
 */

namespace MyVideoRoomPlugin\Module\BuddyPress\Views;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\BuddyPress\BuddyPress;

/**
 * Class BuddyPressViews - Renders the Video Views and Templates for BuddyPress Video Pages.
 */
class BuddyPressViews {

	// ---
	// BuddyPress Groups Templates

	/**
	 * Render Group Host Template
	 *
	 * @param  int $host_id - ID of User to Generate from.
	 * @return string
	 */
	public function bp_group_host_template( int $host_id ): string {
		$module_id    = BuddyPress::DISPLAY_NAME_BUDDYPRESS_GROUPS;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$name_output  = esc_html__( 'Hosting ', 'my-video-room' ) . Factory::get_instance( BuddyPress::class )->bp_groupname_shortcode( array( 'type' => 'name' ) );
		$is_guest     = false;
		$meeting_link = Factory::get_instance( BuddyPress::class )->bp_groupname_shortcode( array( 'type' => 'group_video' ) );

		return $render( $module_id, $name_output, $host_id, BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS, $is_guest, $meeting_link );
	}

	/**
	 * Render Group Guest Template
	 *
	 * @param  int $host_id - ID of User to Generate from.
	 * @return string
	 */
	public function bp_group_guest_template( int $host_id ): string {
		$module_id    = BuddyPress::DISPLAY_NAME_BUDDYPRESS_GROUPS;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$name_output  = esc_html__( 'Visiting ', 'my-video-room' ) . Factory::get_instance( BuddyPress::class )->bp_groupname_shortcode( array( 'type' => 'name' ) );
		$is_guest     = true;
		$meeting_link = Factory::get_instance( BuddyPress::class )->bp_groupname_shortcode( array( 'type' => 'group_video' ) );

		return $render( $module_id, $name_output, $host_id, BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS, $is_guest, $meeting_link );
	}

	/**
	 * Render Main Dashboard Template for user's own account control panel
	 *
	 * @return null
	 */
	public function bp_plugin_control_centre_dashboard() {

		?>
<div class="mvr-row">
	<h2 class="mvr-reception-header">
		<?php esc_html_e( 'Video Settings for ', 'my-video-room' ) . esc_html( get_bloginfo( 'name' ) ); ?></h2>

	<table style="width:100%">
		<tr>
			<th style="width:50%"><img src="
									<?php
									// Get ClubCloud Logo from Plugin folder for Form, or use Site Logo if loaded in theme.
									$custom_logo_id = get_theme_mod( 'custom_logo' );
									$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
									if ( ! $image ) {
										$image = plugins_url( '/images/logoCC-clear.png', __FILE__ );
										echo esc_url( $image );
									} else {
										echo esc_url( $image[0] );
									}
									?>
									" alt="Site Logo"></th>
			<th>
				<?php esc_html_e( 'Video Settings for Please Choose Configuration Option from Tab Above', 'my-video-room' ); ?>

			</th>

		</tr>

	</table>
</div>
		<?php
		return null;
	}

}
