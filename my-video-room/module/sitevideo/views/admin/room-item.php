<?php
/**
 * BACKEND/ADMIN Shows a single row in the conference center rooms table (shows shortcodes)
 *
 * @package MyVideoRoomPlugin\Views\Public\Admin
 */

use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

/**
 * Render the admin page
 *
 * @param \stdClass $room The room
 * @param ?string $room_type  Category of Room to Filter.
 *
 * @return string
 */
return function (
	\stdClass $room,
	$room_type = null,
	int $offset = null
): string {
	ob_start();

	$edit_actions = array(
		array(
			__( 'Edit in WordPress' ),
			get_site_url() . '/wp-admin/post.php?post=' . esc_textarea( $room->id ) . '&action=edit',
			'myvideoroom-dashicons dashicons-wordpress',
			array( 'target' => '_blank' ),
		),
	);

	// Add any extra options.
	$edit_actions = \apply_filters( 'myvideoroom_sitevideo_edit_actions', $edit_actions, $room->id );

	if ( MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $room_type ) {
		$settings_url   = \add_query_arg( array( 'room_id' => $room->id ), \esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ) );
		$edit_actions[] = array(
			__( 'View settings' ),
			$settings_url,
			'myvideoroom-dashicons dashicons-admin-generic myvideoroom-sitevideo-settings',
		);

		$delete_nonce   = wp_create_nonce( 'delete_room_' . $room->id );
		$delete_url     = '#';
		$edit_actions[] = array(
			__( 'Delete Room' ),
			$delete_url,
			'myvideoroom-dashicons dashicons-dismiss myvideoroom-sitevideo-delete ',
			array(
				'data-nonce'     => $delete_nonce,
				'data-room-name' => $room->display_name,
			),
		);
	}
	// ---

	$regenerate_nonce = wp_create_nonce( 'regenerate_room_' . $room->id );
	$regenerate_url   = \add_query_arg(
		array(
			'room_id'  => $room->id,
			'action'   => 'regenerate',
			'_wpnonce' => $regenerate_nonce,
		),
		\esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
	);

	?>
	<tr class="active mvr-table-mobile" data-room-id="<?php echo esc_attr( $room->id ); ?>">
	<td>
	<?php echo '<span id="namechange_' . esc_attr( $room->id ) . '-' . esc_attr( $offset ) . '"class=" column-primary myvideoroom-mobile-table-row-adjust">' . esc_html( $room->display_name ) . '</span>'; ?>
		<?php echo '<i class="myvideoroom-dashicons-small mvr-icons dashicons-edit-large myvideoroom-edit-title-trigger" title="' . \esc_html__( 'Change Page Display Name', 'myvideoroom' ) . '" data-id="' . esc_attr( $room->id ) . '" data-offset="' . esc_attr( $offset ) . '"></i>'; ?>
		<input type="text"
			id="nameinput_<?php echo esc_attr( $room->id ) . '-' . esc_attr( $offset ); ?>"
			minlength="3"
			maxlength="48"
			data-id="<?php echo esc_attr( $room->id ); ?>"
			data-offset="<?php echo esc_attr( $offset ); ?>"
			value="<?php echo esc_attr( $room->display_name ); ?>"
			class="myvideoroom-input-name-trigger myvideoroom-input-restrict-alphanumeric"
			style="display: none;"
		>
		<input type="button"
			id="namebutton_<?php echo esc_attr( $room->id ) . '-' . esc_attr( $offset ); ?>"
			data-id="<?php echo esc_attr( $room->id ); ?>"
			data-offset="<?php echo esc_attr( $offset ); ?>"
			class="myvideoroom-roomname-submit-form"
			value="Current"
			style="display: none;"
			disabled
		>
	</td>
		<td class="column-description myvideoroom-mobile-table-row-adjust">
			<?php
			if ( $room->url ) {
				echo '<a id="urlchange_' . esc_attr( $room->id ) . '-' . esc_attr( $offset ) . '" href="' . esc_url_raw( $room->url ) . '" target="_blank">' . esc_url_raw( $room->url ) . '</a>
				<i class="myvideoroom-dashicons-small mvr-icons dashicons-edit-large myvideoroom-edit-page-trigger" title="' . \esc_html__( 'Change Page URL', 'myvideoroom' ) . '" data-id="' . esc_attr( $room->id ) . '" data-offset="' . esc_attr( $offset ) . '"></i>';
			} else {
				echo '<a href="' . esc_url_raw( $regenerate_url ) . '">' . esc_html__( 'Regenerate room', 'myvideoroom' ) . '</a><i class="myvideoroom-dashicons dashicons-image-rotate"></i>';
			}
			?>
			<input type="text"
			id="urlinput_<?php echo esc_attr( $room->id ) . '-' . esc_attr( $offset ); ?>"
			minlength="3"
			maxlength="24"
			data-id="<?php echo esc_attr( $room->id ); ?>"
			data-offset="<?php echo esc_attr( $offset ); ?>"
			value="<?php echo esc_attr( $room->post_title ); ?>"
			class="myvideoroom-input-url-trigger myvideoroom-input-restrict-alphanumeric"
			style="display: none;"
		>
		<input type="button"
			id="button_<?php echo esc_attr( $room->id ) . '-' . esc_attr( $offset ); ?>"
			data-id="<?php echo esc_attr( $room->id ); ?>"
			data-offset="<?php echo esc_attr( $offset ); ?>"
			class="myvideoroom-roomslug-submit-form"
			value="Current"
			style="display: none;"
			disabled
		>
		</td>
		<td>
			<code class="myvideoroom-shortcode-example-inline">
				<?php

					$shortcode        = '[' . MVRSiteVideo::SHORTCODE_SITE_VIDEO . ' id="' . $room->id . '"]';
					$shortcode_filter = apply_filters( 'myvideoroom_room_manager_shortcode_display', $shortcode, $room->room_type, $room->id, $room );
					echo esc_html( $shortcode_filter );

				?>
			</code>
		</td>
		<td class="plugin-title column-primary">
			<?php
			if ( MVRSiteVideo::ROOM_NAME_SITE_VIDEO === $room->room_type ) {
				// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped upstream to render HTML.
				echo apply_filters( 'myvideoroom_conference_room_type_column_field', $room->type, $room ); 
			} else {
				esc_html_e( '(Switch not Room)', 'myvideoroom' );
			}

			?>
		</td>
		<td>
		<?php
		foreach ( $edit_actions as $action ) {
			?>
			<a href="<?php echo esc_url( $action[1] ); ?>"
				class="myvideoroom-icons <?php echo esc_attr( $action[2] ); ?>"
				data-room-id="<?php echo esc_attr( $room->id ); ?>"
				title="<?php echo esc_attr( $action[0] ); ?>"
				<?php
				foreach ( $action[3] ?? array() as $key => $value ) {
					echo esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
				}
				foreach ( $action[4] ?? array() as $key => $value ) {
					echo esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
				}
				?>
				></a>
				<?php
		}
		?>
		</td>
	</tr>


	<?php

	return ob_get_clean();
};
