<?php
/**
 * Outputs the Maintenance Configuration Settings.
 *
 * @package my-video-room/module/sitevideo/views/admin/view-settings-maintenance.php
 */

/**
 * Render Admin Page Settings Maintenance.
 *
 * @return string
 */

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Library\Maintenance;
use MyVideoRoomPlugin\Module\RoomBuilder\Module;

return function (): string {
	ob_start();
	$index = 7885;

	?>
	<!-- Module Header -->
		<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-database-view"></i><?php esc_html_e( 'Maintenance and Database Configuration', 'myvideoroom' ); ?>
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
			<h2><?php esc_html_e( 'Database Maintenance Operations and Settings', 'myvideoroom' ); ?></h2>
						<p style>
	<?php
		echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
			\esc_html__(
				'The following settings define database and maintenance settings for %s rooms that store details in the database, or for site wide maintenance. ',
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
				'You can view recently synchronised templates for  Visitor Reception, and Room Layout options in the  %s area. ',
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
			<input type="button" class="mvr-main-button-enabled"
			id="myvideoroom_refresh_layout" value="<?php esc_html_e( 'Save Settings', 'myvideoroom' ); ?>" style="display: none;">
			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
		<table id="mvr-table-basket-frame_<?php echo esc_attr( $index++ ); ?>" class="wp-list-table widefat plugins myvideoroom-table-adjust">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Setting', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Value', 'myvideoroom' ); ?>
				</th>

			</tr>
		</thead>
		<tbody>

		<?php
			// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - functions are escaped upstream.
			echo apply_filters( 'myvideoroom_maintenance_page_option', '' );
		?>
	<tr class="active mvr-table-mobile">
		<td>
		<span><?php esc_html_e( 'Sync Receptions and Room Templates', 'myvideoroom' ); ?></span>
		</td>
		<td>
		<input type="button" class="mvr-main-button-enabled mvr-room-update-button-trigger"
			id="myvideoroom_refresh_layout" value="<?php esc_html_e( 'Sync All', 'myvideoroom' ); ?>">
			<i class="myvideoroom-dashicons mvr-icons dashicons-editor-help" title="<?php \esc_html_e( 'Synchronize all current templates from MyVideoRoom servers.', 'myvideoroom' ); ?>"></i>
		<span id="mvr-last-sync-time"><?php echo esc_html__( 'Last Updated: ', 'myvideoroom' ) . esc_textarea( gmdate( 'Y-m-d H:i:s', get_option( Maintenance::OPTION_LAST_TEMPLATE_SYNCC ) ) ); ?></span>
		</td>

	</tr>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Setting', 'myvideoroom' ); ?>
				</th>

				<th scope="col" class="manage-column column-name column-primary">
					<?php esc_html_e( 'Value', 'myvideoroom' ); ?>
				</th>

			</tr>
		</tfoot>
	</table>	
		</div>
	</div>

	<?php
	return ob_get_clean();
};

