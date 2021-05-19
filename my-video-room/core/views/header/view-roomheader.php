<?php
/**
 * Renders the Main Header for all Meetings.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomPlugin\Core\Views\view-roomheader.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\TemplateIcons;
use MyVideoRoomExtrasPlugin\Modules\WooCommerceBookings\ShortCodeConstructor;

return function (
	string $module_name = null,
	string $name_output,
	int $user_id = null,
	string $room_name = null,
	bool $visitor_status = false,
	string $meeting_link = null,
	string $post_site_title = null
): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	ob_start();
	if ( isset( $_SERVER['REQUEST_METHOD'] )
			&& 'POST' === $_SERVER['REQUEST_METHOD']
			&& sanitize_text_field( wp_unslash( $_POST['myvideoroom_refresh'] ?? null ) ) === 'true'
			) {
		check_admin_referer( 'myvideoroom_refresh_nonce', 'nonce' );
		$refresh = $params['refresh'] ?? sanitize_text_field( wp_unslash( $_POST['myvideoroom_refresh'] ?? '' ) );
		if ( true === $refresh ) {
				$second = 0.1;
				header( "Refresh:$second" );
				exit();
		}
	}

	if ( true === $visitor_status ) {

		$invite_menu = Factory::get_instance( ShortCodeConstructor::class )->
		invite_menu_shortcode(
			array(
				'type'    => 'guestlink',
				'user_id' => $user_id,
			)
		);
	}
	// Generate Invite Link for Meeting - First simple case of already received it in template.
	if ( $meeting_link ) {
		$invite_menu = $meeting_link;
	} else {
		$invite_menu = Factory::get_instance( ShortCodeConstructor::class )->invite_menu_shortcode( array( 'user_id' => $user_id ) );
	}

	?>
<div id="video-host-wrap" class="mvr-header-outer-wrap">
	<section class="mvr-header-section">
		<div class="mvr-header-table-left">
			<h2 class="mvr-header-title"><?php echo esc_html( get_bloginfo( 'name' ) ) . esc_html( $post_site_title ); ?></h2>
			<?php
			if ( false === $visitor_status ) {
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function is Icon only, and already escaped within it.
				echo Factory::get_instance( TemplateIcons::class )->show_icon( $user_id, $room_name );
			} else {
				echo '<form method="post" action="">';
				echo '<input name="myvideoroom_refresh" type="hidden" value="true" />';
				wp_nonce_field( 'myvideoroom_refresh_nonce', 'nonce' );
				echo '<input type="submit" name="submit" id="submit" class="button mvr-form-button mvr-form-button-max" value="Exit Meeting"  />';
			}
			?>
		</div>
		<div class="mvr-header-table-right">
			<h2 class="mvr-header-title"><?php echo esc_html( $name_output ) . ' ' . esc_html( $module_name ); ?></h2>
			<p class="mvr-preferences-paragraph">
				<?php echo esc_html__( 'Meeting Link- ', 'my-video-room' ) . esc_url( $invite_menu ); ?>
			</p>
		</div>
	</section>
</div>

	<?php
	return ob_get_clean();
};
