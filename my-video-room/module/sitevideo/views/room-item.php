<?php
/**
 * Shows a single row in the conference center rooms table
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
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
	$room_type = null
): string {
	ob_start();

	$edit_actions = array(
		array(
			__( 'Edit in WordPress' ),
			get_site_url() . '/wp-admin/post.php?post=' . esc_textarea( $room->id ) . '&action=edit',
			'dashicons dashicons-wordpress',
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
			'dashicons dashicons-admin-generic myvideoroom-sitevideo-settings',
		);

		$delete_nonce   = wp_create_nonce( 'delete_room_' . $room->id );
		$delete_url     = \add_query_arg(
			array(
				'room_id'  => $room->id,
				'confirm'  => null,
				'action'   => 'delete',
				'_wpnonce' => $delete_nonce,
			),
			\esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
		);
		$edit_actions[] = array(
			__( 'Delete room' ),
			$delete_url,
			'dashicons dashicons-dismiss myvideoroom-sitevideo-delete',
			array( 'data-nonce' => $delete_nonce ),
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
		<td class="plugin-title column-primary myvideoroom-mobile-table-row-adjust"><?php echo esc_html( $room->display_name ); ?></td>
		<td class="column-description myvideoroom-mobile-table-row-adjust">
			<?php
			if ( $room->url ) {
				echo '<a href="' . esc_url_raw( $room->url ) . '" target="_blank">' . esc_url_raw( $room->url ) . '</a>';
			} else {
				echo '<a href="' . esc_url_raw( $regenerate_url ) . '">' . esc_html__( 'Regenerate room', 'myvideoroom' ) . '<i class="dashicons dashicons-image-rotate"></i></a>';
			}
			?>
		</td>
		<td>
			<code class="myvideoroom-shortcode-example-inline">
				<?php

				$shortcode = '[' . MVRSiteVideo::SHORTCODE_SITE_VIDEO . ' id="' . $room->id . ']';

				$shortcode_filter = apply_filters( 'myvideoroom_room_manager_shortcode_display', $shortcode, $room->room_type, $room->id, $room );
				echo esc_html( $shortcode_filter );
				?>
			</code>
		</td>
		<td class="plugin-title column-primary">
			<?php echo apply_filters( 'myvideoroom_conference_room_type_column_field', $room->type, $room ); ?></td>
		<td>
			<?php
			foreach ( $edit_actions as $action ) {
				?>
				<a href="<?php echo esc_url( $action[1] ); ?>"
					class="mvr-icons <?php echo esc_attr( $action[2] ); ?>"
					data-room-id="<?php echo esc_attr( $room->id ); ?>"
					title="<?php echo esc_attr( $action[0] ); ?>"
					<?php
					foreach ( $actions[3] ?? array() as $key => $value ) {
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
