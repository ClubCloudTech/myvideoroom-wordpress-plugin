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
use MyVideoRoomPlugin\Library\MeetingIdGenerator;
use MyVideoRoomPlugin\Library\TemplateIcons;

return function (
	string $module_name = null,
	string $name_output,
	int $user_id = null,
	string $room_name = null,
	bool $visitor_status = false,
	string $invite_menu = null,
	string $post_site_title = null
): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	ob_start();

	if ( $visitor_status ) {

		$invite_menu = Factory::get_instance( MeetingIdGenerator::class )->
		invite_menu_shortcode(
			array(
				'type'    => 'guestlink',
				'user_id' => $user_id,
			)
		);
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
				echo '<input type="submit" name="submit" id="submit" class="button mvr-form-button mvr-form-button-max" value="Exit Meeting"  />';
			}
			?>
		</div>
		<div class="mvr-header-table-right">
			<h2 class="mvr-header-title"><?php echo esc_html( $name_output ) . ' ' . esc_html( $module_name ); ?></h2>
			<p class="mvr-preferences-paragraph">
				<?php
				if ( $invite_menu ) {
					echo esc_html__( 'Meeting Link- ', 'my-video-room' ) . esc_url( $invite_menu );
				}
				?>
			</p>
		</div>
	</section>
</div>

	<?php
	return ob_get_clean();
};
