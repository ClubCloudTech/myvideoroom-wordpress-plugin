<?php
/**
 * Renders the a Dynamic Shortcode visualisation system to make it easier for admins to visualise a room.
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

namespace MyVideoRoomPlugin\Module\RoomBuilder;

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\Endpoints;
use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\RoomPermissions;

/**
 * Output the settings page for the Room Builder
 *
 * @param array                    $available_layouts    The list of available layouts.
 * @param array                    $available_receptions The list of available receptions.
 * @param ?AppShortcodeConstructor $app_config           The selected config.
 *
 * @return string
 */
return function (
	array $available_layouts,
	array $available_receptions,
	AppShortcodeConstructor $app_config = null
): string {
	\ob_start();

	$html_lib = Factory::get_instance( HTML::class, array( 'room_builder' ) );

	?>
	<h2><?php \esc_html_e( 'Visual room builder and shortcode generator', 'myvideoroom' ); ?></h2>

	<p class="myvideoroom-explainer-text">
		<?php
		echo \esc_html__(
			' Use this tool to explore and create your preferred configuration of MyVideoRoom, including layouts, 
			receptions, permissions, and other settings. The preview is interactive and allows you to drag users in and 
			out of the reception, and to see the output for both hosts and guests. The tool will output the shortcodes 
			that you can then copy and paste into your page or post',
			'myvideoroom'
		)
		?>
	</p>

	<hr />

	<form class="myvideoroom-room-builder-settings" method="post" action="">
		<fieldset>
			<legend><?php echo \esc_html__( 'Room Permissions', 'myvideoroom' ); ?></legend>

			<?php
			$room_permissions = ( new RoomPermissions() )->get_room_permission_options( $app_config );

			foreach ( $room_permissions as $option ) {
				$slug = 'room_permissions_preference_' . $option->get_key();

				?>
				<input type="radio"
					name="<?php echo \esc_attr( $html_lib->get_field_name( 'room_permissions_preference' ) ); ?>"
					id="<?php echo \esc_attr( $html_lib->get_id( $slug ) ); ?>"
					value="<?php echo \esc_attr( $option->get_key() ); ?>"
					<?php echo $option->is_selected() ? 'checked' : ''; ?>
					aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( $slug ) ); ?>"
				/>
				<label for="<?php echo \esc_attr( $html_lib->get_id( $slug ) ); ?>">
					<?php echo \esc_html( $option->get_label() ); ?>
				</label>
				<em id="<?php echo \esc_attr( $html_lib->get_description_id( $slug ) ); ?>">
					<?php
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $option->get_description();
					?>
				</em>
				<br />
				<?php
			}
			?>
		</fieldset>

		<?php \do_action( 'myvideoroom_roombuilder_permission_section' ); ?>

		<div class="room-settings">
			<fieldset>
				<legend><?php \esc_html_e( 'Naming', 'myvideoroom' ); ?></legend>
				<label for="<?php echo \esc_attr( $html_lib->get_id( 'room_name' ) ); ?>">
					<?php \esc_html_e( 'Room Name', 'myvideoroom' ); ?>
				</label>
				<input type="text"
					placeholder="<?php \esc_html_e( 'Your Room Name (optional)', 'myvideoroom' ); ?>"
					id="<?php echo \esc_attr( $html_lib->get_id( 'room_name' ) ); ?>"
					name="<?php echo \esc_attr( $html_lib->get_field_name( 'room_name' ) ); ?>"
					value="<?php echo \esc_html( $app_config ? $app_config->get_name() : '' ); ?>"
					aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( 'room_name' ) ); ?>"
				/>
				<br />
				<em id="<?php echo \esc_attr( $html_lib->get_description_id( 'room_name' ) ); ?>">
					<?php
					\esc_html_e(
						'The name of the room. All video rooms on the same website that share a name will share the 
						same video group. Defaults to the site name',
						'myvideoroom'
					);
					?>
				</em>
			</fieldset>

			<fieldset>
				<legend><?php echo \esc_html__( 'Room Layout', 'myvideoroom' ); ?></legend>
				<label for="<?php echo \esc_attr( $html_lib->get_id( 'layout_id_preference' ) ); ?>">
					<?php echo \esc_html__( 'Layout', 'myvideoroom' ); ?>
				</label>
				<select class="myvideoroom_room_builder_layout_id_preference"
					name="<?php echo \esc_attr( $html_lib->get_field_name( 'layout_id_preference' ) ); ?>"
					id="<?php echo \esc_attr( $html_lib->get_id( 'layout_id_preference' ) ); ?>"
					aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( 'layout_id_preference' ) ); ?>"
				>
					<?php
					if ( ! $app_config || ! $app_config->get_layout() ) {
						echo '<option value="" selected disabled>— ' . \esc_html__( 'Select', 'myvideoroom' ) . ' —</option>';
					}

					foreach ( $available_layouts as $available_layout ) {
						$slug = $available_layout->slug;

						if ( ! $slug ) {
							$slug = $available_layout->id;
						}

						if ( $app_config && $app_config->get_layout() === $slug ) {
							echo '<option value="' . \esc_attr( $slug ) . '" selected>' . \esc_html( $available_layout->name ) . '</option>';
						} else {
							echo '<option value="' . \esc_attr( $slug ) . '">' . \esc_html( $available_layout->name ) . '</option>';
						}
					}
					?>
				</select>
				<br />
				<em id="<?php echo \esc_attr( $html_lib->get_description_id( 'layout_id_preference' ) ); ?>">
					<?php
					$layouts_page   = \menu_page_url( PageList::PAGE_SLUG_ROOM_TEMPLATES, false );
					$layouts_target = '';

					if ( ! $layouts_page ) {
						$layouts_page   = Factory::get_instance( Endpoints::class )->get_rooms_endpoint() . '/views/layouts';
						$layouts_target = ' target="_blank"';
					}

					\printf(
					/* translators: %s is a link to the templates admin page */
						\esc_html__(
							'The layout of the room, determines the background image, and the number of seats and 
								seat groups. See the %s page for a list of available room layouts and more details.',
							'myvideoroom'
						),
						'<a href="' . \esc_url( $layouts_page ) . '"' . \esc_attr( $layouts_target ) . '>' .
						\esc_html__( 'templates', 'myvideoroom' ) .
						'</a>'
					);
					?>
				</em>
				<br />

				<label for="<?php echo \esc_attr( $html_lib->get_id( 'disable_floorplan_preference' ) ); ?>">
					<?php \esc_html_e( 'Disable guest floorplan', 'myvideoroom' ); ?>
				</label>
				<input type="checkbox"
					name="<?php echo \esc_attr( $html_lib->get_field_name( 'disable_floorplan_preference' ) ); ?>"
					id="<?php echo \esc_attr( $html_lib->get_id( 'disable_floorplan_preference' ) ); ?>"
					<?php echo ! $app_config || ! $app_config->is_floorplan_enabled() ? 'checked' : ''; ?>
					aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( 'disable_floorplan_preference' ) ); ?>"
				/>
				<br />
				<em id="<?php echo \esc_attr( $html_lib->get_description_id( 'disable_floorplan_preference' ) ); ?>">
					<?php
					\esc_html_e(
						'Prevents guests from seeing the floorplan, and selecting their own seats. Will
							 automatically enable the reception',
						'myvideoroom'
					);
					?>
				</em>
			</fieldset>

			<fieldset>
				<legend><?php \esc_html_e( 'Guest Settings', 'myvideoroom' ); ?></legend>
				<label for="<?php echo \esc_attr( $html_lib->get_id( 'reception_enabled_preference' ) ); ?>">
					<?php \esc_html_e( 'Enable guest reception?', 'myvideoroom' ); ?>
				</label>
				<input type="checkbox"
					name="<?php echo \esc_attr( $html_lib->get_field_name( 'reception_enabled_preference' ) ); ?>"
					id="<?php echo \esc_attr( $html_lib->get_id( 'reception_enabled_preference' ) ); ?>"
					<?php echo ! $app_config || $app_config->is_reception_enabled() ? 'checked' : ''; ?>
					aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( 'reception_enabled_preference' ) ); ?>"
				/>
				<br />
				<em id="<?php echo \esc_attr( $html_lib->get_description_id( 'reception_enabled_preference' ) ); ?>">
					<?php
					\esc_html_e(
						'The guest reception prevents guests from taking their own seats, and instead puts them 
							into a waiting room from where the host can drag them into a seat. Disabling this option will 
							also enable the guest floorplan',
						'myvideoroom'
					);
					?>
				</em>

				<div class="reception-settings">
					<label for="<?php echo \esc_attr( $html_lib->get_id( 'reception_id_preference' ) ); ?>">
						<?php \esc_html_e( 'Reception Appearance', 'myvideoroom' ); ?>
					</label>
					<select class="myvideoroom_room_builder_reception_id_preference"
						name="<?php echo \esc_attr( $html_lib->get_field_name( 'reception_id_preference' ) ); ?>"
						id="<?php echo \esc_attr( $html_lib->get_id( 'reception_id_preference' ) ); ?>"
						aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( 'reception_id_preference' ) ); ?>"
					>
						<?php
						if ( ! $app_config || ! $app_config->get_reception_id() ) {
							echo '<option value="" selected disabled>— ' . \esc_html__( 'Select', 'myvideoroom' ) . ' —</option>';
						}

						foreach ( $available_receptions as $available_reception ) {
							$slug = $available_reception->slug;

							if ( ! $slug ) {
								$slug = $available_reception->id;
							}

							$selected = '';
							$video    = 'false';

							if ( $app_config && $app_config->get_reception_id() === $slug
							) {
								$selected = ' selected';
							}

							if ( $available_reception->video ) {
								$video = 'true';
							}

							?>
							<option value="<?php echo \esc_attr( $slug ); ?>"
								data-has-video="<?php echo \esc_attr( $video ); ?>"
								<?php echo \esc_attr( $selected ); ?>
							>
								<?php echo \esc_html( $available_reception->name ); ?>
							</option>
							<?php
						}
						?>
					</select>
					<br />
					<em id="<?php echo \esc_attr( $html_lib->get_description_id( 'reception_id_preference' ) ); ?>">
						<?php

						$receptions_page   = \menu_page_url( PageList::PAGE_SLUG_ROOM_TEMPLATES, false );
						$receptions_target = '';

						if ( ! $receptions_page ) {
							$receptions_page   = Factory::get_instance( Endpoints::class )->get_rooms_endpoint() . '/views/receptions';
							$receptions_target = ' target="_blank"';
						}

						\printf(
						/* translators: %s is a link to the templates admin page */
							\esc_html__(
								'The design of the reception. Some recetion additionally will show a background video.
								For a full list of available receptions see the %s page',
								'myvideoroom'
							),
							'<a href="' . \esc_url( $receptions_page ) . '"' . \esc_attr( $receptions_target ) . '>' .
							\esc_html__( 'templates', 'myvideoroom' ) .
							'</a>'
						);
						?>
					</em>
					<br />

					<div class="custom-video-settings">
						<label
							for="<?php echo \esc_attr( $html_lib->get_id( 'reception_custom_video_preference' ) ); ?>">
							<?php \esc_html_e( 'Customize Reception Waiting Room Video', 'myvideoroom' ); ?>
						</label>
						<input type="checkbox"
							name="<?php echo \esc_attr( $html_lib->get_field_name( 'reception_custom_video_preference' ) ); ?>"
							id="<?php echo \esc_attr( $html_lib->get_id( 'reception_custom_video_preference' ) ); ?>"
							<?php echo $app_config && $app_config->get_reception_video() ? 'checked' : ''; ?>
						/>
						<br />

						<div class="custom-video-url">
							<label for="<?php echo \esc_attr( $html_lib->get_id( 'reception_waiting_video_url' ) ); ?>">
								<?php \esc_html_e( 'Video URL', 'myvideoroom' ); ?>:
							</label>
							<input type="text"
								id="<?php echo \esc_attr( $html_lib->get_id( 'reception_waiting_video_url' ) ); ?>"
								name="<?php echo \esc_attr( $html_lib->get_field_name( 'reception_waiting_video_url' ) ); ?>"
								<?php
								if ( $app_config ) {
									echo 'value="' . esc_attr( $app_config->get_reception_video() ) . '"';
								}
								?>
								aria-describedby="<?php echo \esc_attr( $html_lib->get_description_id( 'reception_waiting_video_url' ) ); ?>"
							/>
							<br />
							<em id="<?php echo \esc_attr( $html_lib->get_description_id( 'reception_waiting_video_url' ) ); ?>">
								<?php
								\esc_html_e(
									'Allow customisation of the video shown in the reception. Can either provide a full
										url to a playable video, or instead pass the 11 character YouTube video ID.',
									'myvideoroom'
								)
								?>
							</em>
						</div>
					</div>
				</div>
			</fieldset>
		</div>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Factory::get_instance( HttpPost::class )->create_form_submit(
			'show_roombuilder_preview',
			\esc_html__( 'Preview room and shortcode', 'myvideoroom' )
		);
		?>
	</form>

	<?php
	return \ob_get_clean();
};
