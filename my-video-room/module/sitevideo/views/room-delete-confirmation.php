<?php
/**
 * Outputs a delete confirmation
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

/**
 * Renders a delete confirmation
 *
 * @param \stdClass $room_object The room to delete.
 */
return function (
	\stdClass $room_object
): string {
	$delete_nonce = wp_create_nonce( 'delete_room_confirmation_' . $room_object->post_id );
	$delete_url   = \add_query_arg(
		array(
			'room_id'  => $room_object->post_id,
			'action'   => 'delete',
			'confirm'  => 'true',
			'_wpnonce' => $delete_nonce,
		),
		\esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
	);

	$cancel_url = \add_query_arg(
		array(
			'room_id'  => $room_object->post_id,
			'action'   => null,
			'confirm'  => null,
			'_wpnonce' => null,
		),
		\esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
	);

	ob_start();
	?>

	<form method="post" action="<?php echo esc_url_raw( $delete_url ); ?>">
		<p>
			<?php
			printf(
			/* translators: %s is the name of the room */
				esc_html__( 'Are you sure you want to delete %s', 'myvideoroom' ),
				'<strong>' . esc_html( $room_object->room_name ) . '</strong>'
			);
			?>
		</p>
		<br />
		<br />

		<input
			class="button button-primary"
			type="submit"
			value="<?php esc_html_e( 'Yes, delete room', 'myvideoroom' ); ?>"
		/>
		<a href="<?php echo esc_url_raw( $cancel_url ); ?>">
			<input type="text" class="button button-primary negative"
				value="<?php esc_html_e( 'Cancel', 'myvideoroom' ); ?>">
		</a>
	</form>


	<?php
	return ob_get_clean();
};
