<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;

/**
 * Render the admin page
 *
 * @param array   $room_list       The list of rooms.
 * @param ?string $details_section Optional details section.
 *
 * @return string
 */
return function (
	string $details_section = null
): string {
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
<h2><?php esc_html_e( 'Room Manager', 'my-video-room' ); ?></h2>
<p>
	<?php esc_html_e( 'This section allows you manage the configuration of permanent rooms that you or your modules have created.', 'myvideoroom' ); ?>
</p>

<div aria-label="button" class="button button-primary myvideoroom-sitevideo-add-room-button">
	<i class="dashicons dashicons-plus-alt"></i>
	<?php esc_html_e( 'Add new room', 'my-video-room' ); ?>
</div>

<hr />

<div class="myvideoroom-sitevideo-add-room">
	<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/add-new-room.php' )();
	?>
	<hr />
</div>

<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
	<ul>
		<li>
			<a class="nav-tab nav-tab-active" href="#<?php echo esc_attr( $html_library->get_id( 'base' ) ); ?>">
				<?php esc_html_e( 'Room Manager', 'myvideoroom' ); ?>
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
	//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - already escaped in function.
	echo Factory::get_instance( MVRSiteVideoViews::class )->generate_room_table();
	?>
	<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host"
		data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>">
		<?php
			//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $details_section;
		?>
	</div>
</article>
	<?php
			return ob_get_clean();
};
