<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;

/**
 * Render the admin page
 *
 * @param ?string $details_section Optional details section.
 *
 * @return string
 */
return function (
	string $details_section = null
): string {
	\wp_enqueue_script( 'myvideoroom-monitor' );
	ob_start();
	$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
	$inbound_tabs = array();

	/**
	 * A list of tabs to show
	 *
	 * @var \MyVideoRoomPlugin\Entity\MenuTabDisplay[] $tabs
	 */
	$tabs = apply_filters( 'myvideoroom_room_manager_menu', $inbound_tabs );
	?>
<!-- Module Header -->	
<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-cover-image"></i><?php esc_html_e( 'Room Management and Control', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right-wide">
		</div>
	</div>

	<p>
		<?php esc_html_e( 'This section allows you manage the configuration of your rooms and plugins that manage room components.', 'myvideoroom' ); ?>
	</p>
	<p>
		<?php
		echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
			\esc_html__(
				'You can visit the %s section to add extra room modules, features, and expand the power of you rooms by addin packs and plugin integration modules.',
				'myvideoroom'
			),
			'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ) . '">' .
			\esc_html__( 'Modules', 'myvideoroom' ) .
			'</a>'
		)
		?>
	</p>

	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
		<ul class="mvr-ul-style-top-menu">
			<li>
				<a class="nav-tab nav-tab-active" href="#<?php echo esc_attr( $html_library->get_id( 'base' ) ); ?>">
					<?php esc_html_e( 'Room Summary', 'myvideoroom' ); ?>
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
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $function_callback;
			?>
		</article>
		<?php
	}
	?>

	<article id="<?php echo esc_attr( $html_library->get_id( 'base' ) ); ?>">

		<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
		?>

	</article>
	<?php
	return ob_get_clean();
};
