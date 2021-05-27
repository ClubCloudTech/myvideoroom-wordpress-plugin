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
 * @return string
 */
return function (): string {
	ob_start();

	$post_url = \add_query_arg(
		array(
			'action'   => null,
			'_wpnonce' => null,
			'confirm'  => null,
			'room_id'  => null,
		),
		\esc_url_raw( \wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) )
	);

	$html_library = Factory::get_instance( HTML::class, array( 'site-conference-center-new-room' ) );

	?>
	<h3><?php esc_html_e( 'Add a Conference Room ', 'my-video-room' ); ?></h3>
	<p>
		<?php
		esc_html_e(
			'Use this section to add a Conference Room to your site. It will remain available permanently, and can be configured to your needs.',
			'my-video-room'
		);
		?>
	</p>

	<form method="post" action="<?php echo \esc_url_raw( $post_url ); ?>">
		<label for="<?php echo esc_attr( $html_library->get_id( 'title' ) ); ?>">
			<?php esc_html_e( 'Room Display Name ', 'my-video-room' ); ?>
		</label>

		<input type="text"
			id="<?php echo esc_attr( $html_library->get_id( 'title' ) ); ?>"
			name="<?php echo esc_attr( $html_library->get_field_name( 'title' ) ); ?>"
			aria-describedby="<?php echo \esc_attr( $html_library->get_description_id( 'title' ) ); ?>"
		>
		<p id="<?php echo \esc_attr( $html_library->get_description_id( 'title' ) ); ?>">
			<?php
			esc_html_e(
				'Please select a name for your room. This name will be on the Page itself, headers, and menus.',
				'my-video-room'
			);
			?>
		</p>

		<hr />

		<label for="<?php echo esc_attr( $html_library->get_id( 'slug' ) ); ?>">
			<?php esc_html_e( 'Room URL Link ', 'my-video-room' ); ?>
		</label>

		<input type="text"
			id="<?php echo esc_attr( $html_library->get_id( 'slug' ) ); ?>"
			name="<?php echo esc_attr( $html_library->get_field_name( 'slug' ) ); ?>"
			aria-describedby="<?php echo \esc_attr( $html_library->get_description_id( 'slug' ) ); ?>"
			class="myvideoroom-input-restrict-alphanumeric"
			maxlength="64"
			value=""
		>

		<p id="<?php echo \esc_attr( $html_library->get_description_id( 'slug' ) ); ?>">
			<?php
			printf(
			/* translators: %s is the url for the room */
				esc_html__(
					'Please select an address for your room. It will be created at %s',
					'my-video-room'
				),
				esc_url( get_site_url() ) . '/ [ Your Room URL/Address ]'
			)
			?>
		</p>

		<hr />

		<p>
			<?php
			esc_html_e(
				'Once your room is created, you can edit its look and feel in your page editor, just ensure the shortcode remains in the page',
				'my-video-room'
			);
			?>
		</p>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( \MyVideoRoomPlugin\Library\HttpPost::class )->create_form_submit(
			'add_room',
			esc_html__( 'Add Room', 'my-video-room' )
		);
		?>
	</form>

	<?php

	return ob_get_clean();
};
