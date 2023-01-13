<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Public\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array  $tabs
 * @param array  $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\RoomBuilder\Module;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\SiteDefaults;


return function (
	string $template_override
): string {
	ob_start();
	$index = 788;

	?>
	<!-- Module Header -->
		<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-format-video"></i><?php esc_html_e( 'Video Room Site Default Configuration', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right">
		<h3 class="myvideoroom-settings-offset"><i data-target="" class="myvideoroom-header-dashicons dashicons-admin-settings " title="<?php esc_html_e( 'Go to Settings - Personal Meeting Rooms', 'myvideoroom' ); ?>"></i>
			</h3>
		</div>
	</div>
	<!-- Module State and Description Marker -->
<div class="myvideoroom-feature-outer-table">
		<div id="module-state" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'What This Does', 'myvideoroom' ); ?></h2>
			<div id="parentmodule">

			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<h2><?php esc_html_e( 'Default Room Appearance', 'myvideoroom' ); ?></h2>
						<p style>
	<?php
		echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
			\esc_html__(
				'The following settings define site wide video default parameters for %s in case users, categories, or the addin modules have not specified a room layout. ',
				'myvideoroom'
			),
			'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ) . '">' .
			\esc_html__( 'Room Manager', 'myvideoroom' ) .
			'</a>'
		)
	?>
</p>
<p style>
	<?php
		echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
			\esc_html__(
				'You can view what Visitor Reception, and Room Layout options in the  %s area. ',
				'myvideoroom'
			),
			'<a href="' . \esc_url( \menu_page_url( Module::PAGE_SLUG_BUILDER, false ) ) . '">' .
			\esc_html__( 'Room Design', 'myvideoroom' ) .
			'</a>'
		)
	?>
</p>
		</div>
	</div>

	<!-- Module State and Description Marker -->
<div class="myvideoroom-feature-outer-table">
		<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Settings', 'myvideoroom' ); ?></h2>
			<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">

			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
		<label for="myvideoroom_user_show_floorplan_preference"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Enforce Plugin Style:', 'myvideoroom' ); ?></strong>
			</label>
				<select name="myvideoroom_enforce_plugin_style" id="myvideoroom_enforce_plugin_style">

					<option value="lite" <?php echo ( $template_override === 'lite' ) ? "selected" : '' ?>>Lite Mood</option>
					<option value="dark" <?php echo ( $template_override === 'dark' ) ? "selected" : '' ?>>Dark Mood</option>
					<option value="standard" <?php echo ( $template_override === 'standard' ) ? "selected" : '' ?>>Standard Mood</option>
					<option value="theme_color" <?php echo ( $template_override === 'theme_color' ) ? "selected" : '' ?>>Theme Mood</option>
					
				</select>
			<p class="mvr-preferences-paragraph mvr-paragraph-override">
				<?php
				esc_html_e(
					'Enforce the Default Plugin style for Video Rooms and avoid using the stylesheet built into your theme. With this checked the plugin will use its own styling and not the one in your theme. This is the case for buttons, icons, fonts, and text and background colours.',
					'myvideoroom'
				);
				?>
			</p>
			<hr />

			<?php
			$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
				SiteDefaults::USER_ID_SITE_DEFAULTS,
				SiteDefaults::ROOM_NAME_SITE_DEFAULT
			);
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
			echo $layout_setting;
			?>

		</div>
	</div>

	<?php
	return ob_get_clean();
};

