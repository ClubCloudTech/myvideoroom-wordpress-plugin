<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Core\Shortcode\UserVideoPreference;
use \MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoListeners;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

return function(
	int $room_id,
	string $input_type = null
	): string {

	ob_start();

	// Delete Room Handler.
	Factory::get_instance( MVRSiteVideoListeners::class )->site_videoroom_delete_page();

	// Stop Rendering further in case of Simple Delete Page (delete listener above would have caught the post).

	//phpcs:ignore --WordPress.Security.NonceVerification.Recommended - Not needed as only using it as a flag - no processing.
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_GET['delete'] ) && isset( $_GET['id'] ) && ( 'true' === $_GET['delete'] ) ) {
		return ob_get_clean();
	}

	// Rendering Only Default Config Page.

	//phpcs:ignore --WordPress.Security.NonceVerification.Recommended - Not needed as only using it as a flag - no processing.
	if ( 'admin' === $input_type ) {
		echo '<div class="mvr-nav-shortcode-outer-wrap mvr-security-room-host">';
		//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Escaped.
		echo Factory::get_instance( UserVideoPreference::class )->choose_settings(
			SiteDefaults::USER_ID_SITE_DEFAULTS,
			esc_textarea( MVRSiteVideo::ROOM_NAME_SITE_VIDEO ),
			array( 'basic', 'premium' )
		);
		echo '</div>';
		return ob_get_clean();
	}

	//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a global not user input.
	if ( null !== ( esc_textarea( wp_unslash( $_GET['id'] ?? '' ) ) ) ) {

	} else {
		echo 'No Room ID Provided - exiting';
		wp_safe_redirect( get_site_url() );
		exit;
	}
	$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
	$room_name   = $room_object->room_name;
	if ( ! $room_name ) {
		return 'Invalid Room Number';
	}

	$base_menu = new MenuTabDisplay();
	$base_menu->set_tab_display_name( esc_html__( 'Room Hosts', 'my-video-room' ) )
	->set_tab_slug( 'roomhosts' )
	->set_function_callback(
		Factory::get_instance( MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference::class )->choose_settings( $room_id, $room_name . Dependencies::MULTI_ROOM_HOST_SUFFIX, null, 'roomhost' )
	);
	$base_option  = array( $base_menu );
	$output_array = apply_filters( 'myvideoroom_sitevideo_admin_page_menu', $base_option, $room_id );
	?>
			<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
				<ul class="menu">
					<?php
					$active = '-active';
					foreach ( $output_array as $menu_output ) {
						$tab_display_name = $menu_output->get_tab_display_name();
						$tab_slug         = $menu_output->get_tab_slug();
						?>
						<a class="nav-tab nav-tab<?php echo esc_textarea( $active ); ?>" href="#<?php echo esc_textarea( $tab_slug ); ?>"><?php echo esc_textarea( $tab_display_name ); ?> </a>
						<?php
						$active = null;
					}
					?>
				</ul>
			</nav>

			<?php
			foreach ( $output_array as $article_output ) {
				$function_callback = $article_output->get_function_callback();
				$tab_slug          = $article_output->get_tab_slug();
				?>
				<article id="<?php echo esc_textarea( $tab_slug ); ?> ">
				<?php
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
				echo $function_callback;
				?>
				</article>	

				<?php
			}
	return ob_get_clean();
};
