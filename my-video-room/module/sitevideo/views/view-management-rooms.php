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
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Core\Shortcode\UserVideoPreference;
use \MyVideoRoomPlugin\Core\SiteDefaults;
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
	//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a global not user input.


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
	$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( Dependencies::MODULE_SECURITY_ID );
	?>
<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
	<ul class="menu">
		<a class="nav-tab nav-tab-active" href="#page1"><?php esc_html_e( 'Room Hosts', 'my-video-room' ); ?>
		</a>
		<?php
		if ( $security_enabled ) {
			?>
			<a class="nav-tab" href="#page2"><?php esc_html_e( 'Room Permissions', 'my-video-room' ); ?> </a>
			<?php
		}
		?>
		<a class="nav-tab" href="#page4"><?php esc_html_e( 'Video Settings', 'my-video-room' ); ?></a>

	</ul>
</nav>

	<?php
	if ( $security_enabled ) {
		?>
	<article id="page1">
			<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Escaped.
				echo Factory::get_instance( MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference::class )->choose_settings( $room_id, $room_name . Dependencies::MULTI_ROOM_HOST_SUFFIX, null, 'roomhost' );
			?>
		</p>
	</article>

	<article id="page2">
			<?php
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
			echo Factory::get_instance( \MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference::class )->choose_settings( $room_id, esc_textarea( $room_name ), 'roomhost' );
			?>
		</p>
	</article>
		<?php
	}
	?>
	<article id="page4">
			<?php
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
				echo Factory::get_instance( UserVideoPreference::class )->choose_settings( $room_id, $room_name, array( 'basic', 'premium' ) );
			?>
		</p>
	</article>

	<?php

	return ob_get_clean();
};
