<?php
/**
 * Renders the form for changing the user video preference.
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML as HTML;

return function (
	\stdClass $room_object
): string {
	$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
	ob_start();

	$base_option  = array();
	$output_array = apply_filters( 'myvideoroom_sitevideo_admin_page_menu', $base_option, $room_object->id );
	?>
	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
		<ul>
			<?php
			$active = ' nav-tab-active';
			foreach ( $output_array as $menu_output ) {
				$tab_display_name = $menu_output->get_tab_display_name();
				$tab_slug         = $menu_output->get_tab_slug();
				?>
				<li>
					<a class="nav-tab<?php echo esc_attr( $active ); ?>" href="#<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
						<?php echo esc_html( $tab_display_name ); ?>
					</a>
				</li>
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
		<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
			<?php
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
			echo $function_callback;
			?>
		</article>

		<?php
	}

	return ob_get_clean();
};
