<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */


use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Setup\RoomAdmin;
use MyVideoRoomPlugin\Core\Dao\ModuleConfig;
use \MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoListeners;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoDisplayRooms;

return function (
	array $messages = array()
): string {
	wp_enqueue_style( 'mvr-template' );
	wp_enqueue_style( 'mvr-menutab-header' );
	wp_enqueue_script( 'myvideoroom-protect-input' );
	ob_start();
	$path   = '/core/views/header/header.php';
	$render = require WP_PLUGIN_DIR . '/my-video-room' . $path;

	// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Text is escaped in each variable.
	echo $render( $messages );

	$post_id = Factory::get_instance( RoomAdmin::class )->get_videoroom_info( MVRSiteVideo::ROOM_NAME_SITE_VIDEO, 'post_id' );

	global $wp;

	// Listener for Handling Room Add. Listens for Room Adds- and Handles Form below.
	Factory::get_instance( MVRSiteVideoListeners::class )->site_videoroom_add_page();
	// Listener for Handling Regeneration of Site Video Room Pages in case of orphaning.
	Factory::get_instance( MVRSiteVideoListeners::class )->site_videoroom_regenerate_page();

	?>

	<div class="mvr-outer-box-wrap">
		<h1><?php echo esc_html__( 'Site Conference Center', 'my-video-room' ); ?></h1>
		<?php
		$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( SiteDefaults::MODULE_SECURITY_ID );
		if ( $security_enabled ) {
			echo esc_html( Factory::get_instance( \MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons::class )->site_wide_enabled() );
		}

		echo '<p>';
		echo esc_html__(
			'The site conference module suite is available for team wide meetings, events, or any need for central rooms at the website level.
			These permanent rooms are created automatically by the module, at activation, and can be renamed. They can be individually secured
			such that any site role group can host the room. Room permissions, reception settings, templates, and custom reception videos
			are all available for each conference room. You can connect permanent WebRTC enabled devices like projectors, and microphones to rooms permanently',
			'my-video-room'
		);
		echo '<br></p>';

			// Activation/module.
		if ( ! Factory::get_instance( ModuleConfig::class )->module_activation_button( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
			return '';
		}
		?>
	</div>
	<div class="mvr-outer-box-wrap">
			<h1 class="mvr-title-header"><?php echo esc_html__( 'Current Room Information ', 'my-video-room' ); ?>   </h1>
			<div class="mvr-add-page" >
			<?php
			//phpcs:ignore --WordPress.Security.NonceVerification.Recommended . Its a superglobal not user input.
			$slug = admin_url( 'admin.php?page=' . esc_textarea( wp_unslash( $_GET['page'] ) ) );
			echo '<a href="' . esc_url_raw( $slug ) . '&tab=' . esc_textarea( MVRSiteVideo::MODULE_ROOM_MANAGEMENT_NAME ) . '&id=' . esc_textarea( $post_id ) . '&manage=true" class="dashicons mvr-icons dashicons-cover-image" target="iframe1" title="' . esc_html__( 'Manage Default Video Room Appearance Settings', 'my-video-room' ) . '"></a>';
			?>
				<?php echo esc_html__( 'Default Room Settings', 'my-video-room' ); ?>
			</div>
			<div class="mvr-add-page" tabindex="1"><i class="dashicons mvr-icons dashicons-plus-alt mvr-title-header" title="Add New Room"></i><?php echo esc_html__( 'Add Room', 'my-video-room' ); ?></div>
				<div class="mvr-add-page-form">
					<h2 class="mvr-title-header"><?php echo esc_html__( 'Add a Conference Room ', 'my-video-room' ); ?>   </h2>
					<p><?php echo esc_html__( 'Use this section to add a Conference Room to your site. It will remain available permanently, and can be configured to your needs.', 'my-video-room' ); ?></p>
					<form method="post" action="">
							<label for="myvideoroom_add_room_title" class="mvr-preferences-paragraph"><?php echo esc_html__( 'Room Display Name ', 'my-video-room' ); ?></label>
							<input		type="text"
										id="myvideoroom_add_room_title"
										name="myvideoroom_add_room_title"
										class="mvr-roles-multiselect mvr-select-box-small"
										value="">
							<p class="mvr-preferences-paragraph">
							<?php
							echo esc_html__(
								'Please select a name for your room. This name will be on the Page itself, headers, and menus.',
								'my-video-room'
							);
							?>
							</p>
							<hr>
							<label for="myvideoroom_add_room_slug" class="mvr-preferences-paragraph"><?php echo esc_html__( 'Room URL Link ', 'my-video-room' ); ?></label>
							<input		type="text"
										id="myvideoroom_add_room_slug"
										name="myvideoroom_add_room_slug"
										class="mvr-roles-multiselect mvr-select-box-small"
										maxlength="64"
										onkeyup="chText()"
										onkeydown="chText()"
										value="">
							<p class="mvr-preferences-paragraph">
							<?php
							echo esc_html__(
								'Please select an address for your room. It will be created at ',
								'my-video-room'
							) . esc_url( get_site_url() ) . '/ [ Your Room URL/Address ]';
							?>
							</p>
							<hr>
							<?php
							echo esc_html__(
								'Once your room is created, you can edit its look and feel in your page editor, just ensure the shortcode remains in the page',
								'my-video-room'
							);
							?>
							<br>
							<?php wp_nonce_field( 'myvideoroom_add_room', 'nonce' ); ?>
							<input type="submit" name="submit" id="submit" class="mvr-form-button" value="<?php echo esc_html__( 'Add Room', 'my-video-room' ); ?>"  />
						</form>
				</div>
			</div>
			<div id="video-host-wrap" class="mvr-outer-box-wrap">
			<div class="label" id="label"></div>
				<table class="mvr-templates-outer-wrap mvr-header-table"  >
						<tr>
							<th class="mvr-table-pagename" ><?php echo esc_html__( 'Page Name', 'my-video-room' ); ?></th>
							<th class="mvr-table-pageurl" ><?php echo esc_html__( 'Page URL', 'my-video-room' ); ?></th>
							<th class="mvr-table-pagepostid" ><?php echo esc_html__( 'Shortcode', 'my-video-room' ); ?></th>
							<th class="mvr-table-editpage" ><?php echo esc_html__( 'Actions', 'my-video-room' ); ?></th>
						</tr>
				<?php
					//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - Item already Escaped in parent function.
					echo Factory::get_instance( MVRSiteVideoDisplayRooms::class )->site_videoroom_display_rooms();
				?>
				</table>
				<div class="mvr-nav-shortcode-outer-wrap-clean mvr-security-room-host">
				<iframe id="iframe1" name="iframe1" src="" width="100%" height="950" ></iframe>
						</div>
				</div>
			</div>	
		<div>
	</div>
	<?php

	return ob_get_clean();
};
