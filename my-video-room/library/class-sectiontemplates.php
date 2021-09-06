<?php
/**
 * Display section templates
 *
 * @package MyVideoRoomPlugin\Library\SectionTemplates.php
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Factory;

/**
 * Class SectionTemplate
 */
class SectionTemplates {

	/**
	 * Render a Template to Automatically Wrap the Video Shortcode with additional tabs to add more functionality
	 *  Used to add Admin Page for each Room for Hosts, Returns Header and Shortcode if no additional pages passed in
	 *
	 * @param string  $header       The Header of the Shortcode.
	 * @param array   $inbound_tabs Inbound object with tabs.
	 * @param ?int    $user_id      User ID for passing to other Filters.
	 * @param ?string $room_name    Room Name for passing to other Filters.
	 * @param bool    $host_status  Whether user is a host.
	 *
	 * @return string The completed Formatted Template.
	 */
	public function shortcode_template_wrapper( string $header, array $inbound_tabs, int $user_id = null, string $room_name = null, bool $host_status = null ): string {
		ob_start();
		// Randomizing Pages by Header to avoid page name conflicts if multiple frames.
		$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
		$tabs         = apply_filters( 'myvideoroom_main_template_render', $inbound_tabs, $user_id, $room_name, $host_status );
		?>

		<div class="mvr-nav-shortcode-outer-wrap">
			<div class="mvr-header-section">
				<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
				echo $header;
				?>
			</div>

			<?php
			$tab_count = \count( $tabs );
			if ( $tab_count <= 1 ) {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode already properly escaped.
				echo $tabs[0]->get_function_callback();
			} else {
				?>
				<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
					<ul>
						<?php
						$active = ' nav-tab-active';

						foreach ( $tabs as $menu_output ) {
							$tab_display_name = $menu_output->get_tab_display_name();
							$tab_slug         = $menu_output->get_tab_slug();
							?>
							<li>
								<a class="nav-tab<?php echo esc_attr( $active ); ?>"
									href="#<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
									<?php echo esc_html( $tab_display_name ); ?>
								</a>
							</li>
							<?php
							$active = null;
						}
						?>
					</ul>
				</nav>
<div id="mvr-notification-master" class="mvr-nav-shortcode-outer-wrap-clean mvr-notification-master">
<?php
	echo \do_action( 'myvideoroom_notification_master', $room_name );
?>
</div>
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
		</div>
		<?php
		return \ob_get_clean();
	}
}
