<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomAdmin;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use \MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoListeners;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoDisplayRooms;
use MyVideoRoomPlugin\SiteDefaults as MyVideoRoomPluginSiteDefaults;

/**
 * Render the admin page
 *
 * @param ?string $settings Optional setting section
 * @param bool    $deleted  If a page was deleted
 *
 * @return string
 */
return function ( string $settings = null, bool $deleted = false ): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	wp_enqueue_script( 'myvideoroom-protect-input' );
	ob_start();

	// Listener for Handling Regeneration of Site Video Room Pages in case of orphaning.
	Factory::get_instance( MVRSiteVideoListeners::class )->site_videoroom_regenerate_page();

	?>

	<div class="mvr-outer-box-wrap">
		<h1><?php esc_html_e( 'Site Conference Center', 'my-video-room' ); ?></h1>
		<?php
		$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( MyVideoRoomPluginSiteDefaults::MODULE_SECURITY_ID );
		if ( $security_enabled ) {
			echo esc_html( Factory::get_instance( \MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons::class )->site_wide_enabled() );
		}

		echo '<p>';
		esc_html_e(
			'The site conference module suite is available for team wide meetings, events, or any need for central rooms at the website level. These permanent rooms are created automatically by the module, at activation, and can be renamed. They can be individually secured such that any site role group can host the room. Room permissions, reception settings, templates, and custom reception videos are all available for each conference room. You can connect permanent WebRTC enabled devices like projectors, and microphones to rooms permanently',
			'my-video-room'
		);
		echo '</p>';

			// Activation/module.
		if ( ! Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
			return '';
		}
		?>
	</div>

	<div class="mvr-outer-box-wrap">
		<h1 class="mvr-title-header"><?php esc_html_e( 'Current Room Information ', 'my-video-room' ); ?></h1>

		<div class="mvr-add-page" >
			<?php
			//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
			$slug = admin_url( 'admin.php?page=' . esc_textarea( wp_unslash( $_GET['page'] ) ) );
			echo '<a href="#" class="myvideoroom-sitevideo-settings" title="' . esc_html__( 'Manage Default Video Room Appearance Settings', 'my-video-room' ) . '" data-room-id="' . esc_attr( MyVideoRoomPluginSiteDefaults::USER_ID_SITE_DEFAULTS ) . '">
				<i class="dashicons mvr-icons dashicons-cover-image"></i>
				' . esc_html__( 'Default Room Settings', 'my-video-room' ) . '
</a>';
			?>
		</div>

		<div class="mvr-add-page myvideoroom-sitevideo-add-room-button" tabindex="1">
			<i class="dashicons mvr-icons dashicons-plus-alt mvr-title-header" title="Add New Room"></i><?php esc_html_e( 'Add Room', 'my-video-room' ); ?>
		</div>

		<div class="mvr-add-page-form myvideoroom-sitevideo-add-room">
			<h2 class="mvr-title-header"><?php esc_html_e( 'Add a Conference Room ', 'my-video-room' ); ?>   </h2>
			<p><?php esc_html_e( 'Use this section to add a Conference Room to your site. It will remain available permanently, and can be configured to your needs.', 'my-video-room' ); ?></p>

			<?php
				$post_url = \add_query_arg(
					array(
						'delete'   => null,
						'_wpnonce' => null,
						'confirm'  => null,
						'room_id'  => null,
					),
					\esc_url_raw( \wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
				);
			?>

			<form method="post" action="<?php echo \esc_url_raw( $post_url ); ?>">
					<label for="myvideoroom_add_room_title" class="mvr-preferences-paragraph"><?php esc_html_e( 'Room Display Name ', 'my-video-room' ); ?></label>
					<input		type="text"
								id="myvideoroom_add_room_title"
								name="myvideoroom_add_room_title"
								class="mvr-roles-multiselect mvr-select-box-small"
								value="">
					<p class="mvr-preferences-paragraph">
					<?php
					esc_html_e(
						'Please select a name for your room. This name will be on the Page itself, headers, and menus.',
						'my-video-room'
					);
					?>
					</p>
					<hr>
					<label for="myvideoroom_add_room_slug" class="mvr-preferences-paragraph"><?php esc_html_e( 'Room URL Link ', 'my-video-room' ); ?></label>
					<input		type="text"
								id="myvideoroom_add_room_slug"
								name="myvideoroom_add_room_slug"
								class="mvr-roles-multiselect mvr-select-box-small myvideoroom-input-restrict-alphanumeric"
								maxlength="64"
								value="">
					<p class="mvr-preferences-paragraph">
					<?php
					esc_html_e(
						'Please select an address for your room. It will be created at ',
						'my-video-room'
					) . esc_url( get_site_url() ) . '/ [ Your Room URL/Address ]';
					?>
					</p>
					<hr>
					<?php
					esc_html_e(
						'Once your room is created, you can edit its look and feel in your page editor, just ensure the shortcode remains in the page',
						'my-video-room'
					);
					?>
					<br>

					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo Factory::get_instance( \MyVideoRoomPlugin\Library\HttpPost::class )->create_form_submit(
						'add_room',
						esc_html__( 'Add Room', 'my-video-room' )
					);
					?>
				</form>
		</div>
	</div>

	<div id="video-host-wrap" class="mvr-outer-box-wrap">
		<div class="label" id="label"></div>
			<table class="mvr-templates-outer-wrap mvr-header-table"  >
					<tr>
						<th class="mvr-table-pagename" ><?php esc_html_e( 'Page Name', 'my-video-room' ); ?></th>
						<th class="mvr-table-pageurl" ><?php esc_html_e( 'Page URL', 'my-video-room' ); ?></th>
						<th class="mvr-table-pagepostid" ><?php esc_html_e( 'Shortcode', 'my-video-room' ); ?></th>
						<th class="mvr-table-editpage" ><?php esc_html_e( 'Actions', 'my-video-room' ); ?></th>
					</tr>
			<?php
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - Item already Escaped in parent function.
				echo Factory::get_instance( MVRSiteVideoDisplayRooms::class )->site_videoroom_display_rooms();
			?>
			</table>

			<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host" data-loading-text="<?php echo esc_attr__( 'Loading...', 'myvideoroom' ); ?>">
				<?php
				if ( $deleted ) {
					echo '<span class="page-deleted">' . esc_html__( 'The page was successfully deleted', 'myvideoroom' ) . '</span>';
				}
				?>
				<?php
					//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - Item already Escaped in parent function.
					echo $settings;
				?>
			</div>
		</div>
	</div>

	<?php

	return ob_get_clean();
};
