<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\add-new-room.php
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
	$html_library = Factory::get_instance( HTML::class, array( 'site-conference-center-new-room' ) );

	?>
<h3><?php esc_html_e( 'Add a Conference Room ', 'my-video-room' ); ?></h3>
<p>
	<?php
		esc_html_e(
			'Use this section to add a Conference Room to your site. It will remain available permanently, and can be configured as you wish',
			'my-video-room'
		);
	?>
</p>

<label for="<?php echo esc_attr( $html_library->get_id( 'title' ) ); ?>">
	<?php esc_html_e( 'Room Display Name ', 'my-video-room' ); ?>
	<i id="room-name-icon" class="myvideoroom-dashicons mvr-icons dashicons-saved" title="Room Name is OK"
		style="display:none"></i>
</label>

<input type="text" id="room-display-name" name="<?php echo esc_attr( $html_library->get_field_name( 'title' ) ); ?>"
	aria-describedby="<?php echo \esc_attr( $html_library->get_description_id( 'title' ) ); ?>">
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
	<i id="room-link-icon" class="myvideoroom-dashicons mvr-icons dashicons-saved" title="URL is OK"
		style="display:none"></i>
</label>

<input type="text" id="room-url-link" minlength="3" maxlength="24" value=""
	class="myvideoroom-input-new-trigger myvideoroom-input-restrict-alphanumeric">
<input type="button" id="button_add_new" class="myvideoroom-roomname-submit-form" value="Enter Room Name"
	style="display:none;" disabled>

<p id="<?php echo \esc_attr( $html_library->get_description_id( 'slug' ) ); ?>">
	<?php
			printf(
			/* translators: %s is the url for the room */
				esc_html__(
					'Please select an address for your room. It will be created at %s',
					'my-video-room'
				),
				'<div id="update_url_newroom" class=""> ' . esc_url( get_site_url() ) . '/ [ Your Room URL/Address ]</div>'
			)
	?>
</p>
	<?php

	return ob_get_clean();
};
