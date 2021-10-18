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
 * Class BuddyPressViews Groups- Renders the Video Views and Templates for BuddyPress Video Pages for Groups.
 */
class BuddyPressViews {

	/**
	 * Render Group Host Template
	 *
	 * @param  int $host_id - ID of User to Generate from.
	 * @return array
	 */
	public function bp_group_host_template( int $host_id ): array {
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
	 * @return array
	 */
	public function bp_group_guest_template( int $host_id ):array {
		$module_id    = BuddyPress::DISPLAY_NAME_BUDDYPRESS_GROUPS;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$name_output  = esc_html__( 'Visiting ', 'my-video-room' ) . Factory::get_instance( BuddyPress::class )->bp_groupname_shortcode( array( 'type' => 'name' ) );
		$is_guest     = true;
		$meeting_link = Factory::get_instance( BuddyPress::class )->bp_groupname_shortcode( array( 'type' => 'group_video' ) );

		return $render( $module_id, $name_output, $host_id, BuddyPress::ROOM_NAME_BUDDYPRESS_GROUPS, $is_guest, $meeting_link );
	}

	/**
	 * Blocked By Group Membership Template.
	 *
	 * @param  int $user_id - the user ID who is blocking.
	 * @return string
	 */
	public function blocked_by_group_membership( $user_id = null ) {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>
		<div class="mvr-row mvr-background">
			<h2 class="mvr-header-text">
				<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This room is set to Specific Group Members Only', 'myvideoroom' ) . '</h2>';
				?>
				<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

				<p class="mvr-template-text">
					<?php
					$first_display_name  = '<strong>' . esc_html__( 'Group Administrators', 'myvideoroom' ) . '</strong>';
					$second_display_name = '<strong>' . esc_html__( ' the group admins ', 'myvideoroom' ) . '</strong>';

					echo sprintf(
					/* translators: %1s is the text "Site Policy" and %2s is "the site administrators" */
						esc_html__( ' %1$s  only allow specific members to access this group room. Please contact %2$s for more assistance.', 'myvideoroom' ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
						$first_display_name,
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
						$second_display_name
					);
					?>
				</p>
		</div>
		<?php

		return \ob_get_clean();
	}
}
