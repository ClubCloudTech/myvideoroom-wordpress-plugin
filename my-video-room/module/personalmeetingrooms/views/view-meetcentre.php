<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingControllers;

/**
 * Render the Meet Center Reception page
 *
 * @param array   $room_list        The list of rooms.
 * @param ?string $details_section  Optional details section.
 *
 * @return string
 */
return function (
$tabs = null,
$html_library
): string {
	ob_start();
	if ( count( $tabs ) >= 1 ) {
		?>
<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
	<ul>
		<li>
			<a class="nav-tab nav-tab-active" href="#<?php echo esc_attr( $html_library->get_id( 'base' ) ); ?>">
				<?php esc_html_e( 'Join Meeting', 'myvideoroom' ); ?>
			</a>
		</li>
		<?php
		foreach ( $tabs as $menu_output ) {
			$tab_display_name = $menu_output->get_tab_display_name();
			$tab_slug         = $menu_output->get_tab_slug();
			?>
		<li>
			<a class="nav-tab" href="#<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
				<?php echo esc_html( $tab_display_name ); ?>
			</a>
		</li>
			<?php
		}
		?>
	</ul>
</nav>
		<?php
		foreach ( $tabs as $article_output ) {
			$function_callback = $article_output->get_function_callback();
			$tab_slug          = $article_output->get_tab_slug();
			?>
<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
			<?php
						// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
						echo $function_callback;
			?>
</article>
			<?php
		}
	}
	?>
<article id="<?php echo esc_attr( $html_library->get_id( 'base' ) ); ?>">
	<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
			echo Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_guest_shortcode(); 
	?>
</article>
	<?php
	return ob_get_clean();
};
