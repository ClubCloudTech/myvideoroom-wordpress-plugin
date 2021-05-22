<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;

/**
 * Render the admin page
 *
 * @param array   $room_list        The list of rooms.
 * @param ?string $details_section  Optional details section.
 *
 * @return string
 */
return function (
	array $room_list,
	string $details_section = null
): string {
	ob_start();
	$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
	$inbound_tabs = array();
	$tabs         = apply_filters( 'myvideoroom_room_manager_menu', $inbound_tabs );
	?>
<h2><?php esc_html_e( 'Room Manager', 'my-video-room' ); ?></h2>
<p><?php esc_html_e( 'This section allows you manage the configuration of permanent rooms that you have added, or installed modules have created for you.', 'myvideoroom' ); ?>
</p>
<div aria-label="button" class="button button-primary myvideoroom-sitevideo-add-room-button">
	<i class="dashicons dashicons-plus-alt"></i>
	<?php esc_html_e( 'Add new room', 'my-video-room' ); ?>
</div>

<hr />

<div class="myvideoroom-sitevideo-add-room">
	<?php
		//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( require __DIR__ . '/add-new-room.php')();
	?>
	<hr />
</div>

<nav class="myvideoroom-nav-tab-wrapper ">
	<ul class="mvr-ul-header">
		<li class="mvr-title-header"><a class="nav-tab nav-tab-active"
				href="#base"><?php esc_html_e( 'Room Manager', 'myvideoroom' ); ?></a>
		</li>

		<?php
		foreach ( $tabs as $menu_output ) {
			$tab_display_name = $menu_output->get_tab_display_name();
			$tab_slug         = $menu_output->get_tab_slug();
			?>
		<li class="mvr-title-header"><a class="mvr-menu-shortcode-button nav-tab"
				href="#<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>"><?php echo esc_html( $tab_display_name ); ?></a>
		</li>
			<?php
					$active = null;
		}
		?>
	</ul>
</nav>
<br>
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
		?>
<article id="base">
	<?php
	if ( $room_list ) {
		?>
	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Page Name', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Page URL', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Shortcode', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Type', 'my-video-room' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Actions', 'my-video-room' ); ?>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php
			$room_item_render = require __DIR__ . '/room-item.php';
			foreach ( $room_list as $room ) {
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $room_item_render( $room );
			}
			?>
		</tbody>
	</table>
	<?php } else { ?>
	<p>
		<?php
			printf(
			/* translators: %s is the text "Add new room" */
				esc_html__(
					'You don\'t current have any site conference rooms. Please click on "%s" above to get started',
					'myvideoroom'
				),
				esc_html__( 'Add new room', 'my-video-room' ),
			)
		?>
	</p>
	<?php } ?>
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
