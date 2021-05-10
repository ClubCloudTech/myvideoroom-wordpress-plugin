<?php
/**
 * Renders the a Dynamic Shortcode visualisation system to make it easier for admins to visualise a room.
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

use MyVideoRoomPlugin\Library\AdminNavigation;
use MyVideoRoomPlugin\Library\Endpoints;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

/**
 * Output the settings page for the Room Builder
 *
 * @param array $available_layouts The list of available layouts.
 * @param array $available_receptions The list of available receptions.
 * @param ?AppShortcodeConstructor $app_config The selected config.
 * @param int $id_index A unique id to generate unique id names
 *
 * @return string
 */
return function (
	array $available_layouts,
	array $available_receptions,
	AppShortcodeConstructor $app_config = null,
	int $id_index = 0
): string {
	ob_start();

	?>
	<h2><?php esc_html_e( 'Visual room builder and shortcode generator', 'myvideoroom' ); ?></h2>

	<p class="myvideoroom-explainer-text">
		<?php
		echo esc_html__(
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
			<legend><?php esc_html_e( 'Naming', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_room_builder_room_name_<?php echo esc_attr( $id_index ); ?>">
					<?php esc_html_e( 'Room Name', 'myvideoroom' ); ?>
			</label>
			<input type="text"
				placeholder="<?php esc_html_e( 'Your Room Name (optional)', 'myvideoroom' ); ?>"
				id="myvideoroom_room_builder_room_name_<?php echo esc_attr( $id_index ); ?>"
				name="myvideoroom_room_builder_room_name"
				value="<?php echo esc_html( $app_config ? $app_config->get_name() : '' ); ?>"
				aria-describedby="myvideoroom_room_builder_room_name_<?php echo esc_attr( $id_index ); ?>_description"
			/>
			<br />
			<em id="myvideoroom_room_builder_room_name_<?php echo esc_attr( $id_index ); ?>_description">
				<?php
				esc_html_e(
					'The name of the room. All video rooms on the same website that share a name will share the 
					same video group. Defaults to the site name',
					'myvideoroom'
				);
				?>
			</em>
		</fieldset>

		<fieldset>
			<legend><?php echo esc_html__( 'Room Layout', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_room_builder_layout_id_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Layout', 'myvideoroom' ); ?>
			</label>
			<select class="myvideoroom_room_builder_layout_id_preference"
				name="myvideoroom_room_builder_layout_id_preference"
				id="myvideoroom_room_builder_layout_id_preference_<?php echo esc_attr( $id_index ); ?>"
				aria-describedby="myvideoroom_room_builder_layout_id_preference_<?php echo esc_attr( $id_index ); ?>_description"
			>
				<?php
				if ( ! $app_config || ! $app_config->get_layout() ) {
					echo '<option value="" selected disabled>— Select —</option>';
				}

				foreach ( $available_layouts as $available_layout ) {
					$slug = $available_layout->slug;

					if ( ! $slug ) {
						$slug = $available_layout->id;
					}

					if ( $app_config && $app_config->get_layout() === $slug ) {
						echo '<option value="' . esc_attr( $slug ) . '" selected>' . esc_html( $available_layout->name ) . '</option>';
					} else {
						echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $available_layout->name ) . '</option>';
					}
				}
				?>
			</select>
			<br />
			<em id="myvideoroom_room_builder_layout_id_preference_<?php echo esc_attr( $id_index ); ?>_description">
				<?php
				$layouts_page   = menu_page_url( AdminNavigation::PAGE_SLUG_ROOM_TEMPLATES, false );
				$layouts_target = '';

				if ( ! $layouts_page ) {
					$layouts_page   = Factory::get_instance( Endpoints::class )->get_rooms_endpoint() . '/views/layouts';
					$layouts_target = ' target="_blank"';
				}

				printf(
					/* translators: %s is a link to the templates admin page */
					esc_html__(
						'The layout of the room, determines the background image, and the number of seats and 
					        seat groups. See the %s page for a list of available room layouts and more details.',
						'myvideoroom'
					),
					'<a href="' . esc_url( $layouts_page ) . '"' . esc_attr( $layouts_target ) . '>' .
					esc_html__( 'templates', 'myvideoroom' ) .
					'</a>'
				);
				?>
			</em>
			<br />

			<label for="myvideoroom_room_builder_disable_floorplan_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Disable guest floorplan', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				name="myvideoroom_room_builder_disable_floorplan_preference"
				id="myvideoroom_room_builder_disable_floorplan_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo ! $app_config || ! $app_config->is_floorplan_enabled() ? 'checked' : ''; ?>
				aria-describedby="myvideoroom_room_builder_disable_floorplan_preference_<?php echo esc_attr( $id_index ); ?>_description"
			/>
			<br />
			<em id="myvideoroom_room_builder_disable_floorplan_preference_<?php echo esc_attr( $id_index ); ?>_description">
				<?php
				echo esc_html__(
					'Prevents guests from seeing the floorplan, and selecting their own seats. Will
				         automatically enable the reception',
					'myvideoroom'
				);
				?>
			</em>
		</fieldset>

		<fieldset>
			<legend><?php echo esc_html__( 'Room Permissions', 'myvideoroom' ); ?></legend>

			<label for="myvideoroom_room_builder_room_permissions_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Use WordPress roles to determine room permissions', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				name="myvideoroom_room_builder_room_permissions_preference"
				id="myvideoroom_room_builder_room_permissions_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo ! $app_config || $app_config->is_host() === null ? 'checked' : ''; ?>
				aria-describedby="myvideoroom_room_builder_room_permissions_preference_<?php echo esc_attr( $id_index ); ?>_description"
			/>
			<br />
			<em id="myvideoroom_room_builder_room_permissions_preference_<?php echo esc_attr( $id_index ); ?>_description">
				<?php

				esc_html_e(
					'When selected the permission of hosts and guests will be determined by the global 
					        settings. This means that you only need to only have one page, with a single shortcode. If 
					        you want a more customised control, then you can disable this option, instead creating two 
					        separate pages, each with their own shortcodes. It is your responsibility to then manage 
					        access to each page. '
				);

				$permissions_page = menu_page_url( AdminNavigation::PAGE_SLUG_PERMISSIONS, false );

				if ( $permissions_page ) {
					printf(
					/* translators: %s is a link to the room permissions admin page */
						esc_html__(
							'You can customise the global host settings in the %s page',
							'myvideoroom'
						),
						'<a href="' . esc_url( $permissions_page ) . '">' .
						esc_html__( 'room permissions', 'myvideoroom' ) .
						'</a>'
					);

				}
				?>
			</em>
		</fieldset>

		<fieldset>
			<legend><?php echo esc_html__( 'Guest Settings', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_room_builder_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Enable guest reception?', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				name="myvideoroom_room_builder_reception_enabled_preference"
				id="myvideoroom_room_builder_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo ! $app_config || $app_config->is_reception_enabled() ? 'checked' : ''; ?>
				aria-describedby="myvideoroom_room_builder_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>_description"
			/>
			<br />
			<em id="myvideoroom_room_builder_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>_description">
				<?php
				echo esc_html__(
					'The guest reception prevents guests from taking their own seats, and instead puts them 
				        into a waiting room from where the host can drag them into a seat. Disabling this option will 
				        also enable the guest floorplan',
					'myvideoroom'
				);
				?>
			</em>

			<div class="reception-settings">
				<label for="myvideoroom_room_builder_reception_id_preference_<?php echo esc_attr( $id_index ); ?>">
					<?php echo esc_html__( 'Reception Appearance', 'myvideoroom' ); ?>
				</label>
				<select class="myvideoroom_room_builder_reception_id_preference"
					name="myvideoroom_room_builder_reception_id_preference"
					id="myvideoroom_room_builder_reception_id_preference_<?php echo esc_attr( $id_index ); ?>"
					aria-describedby="myvideoroom_room_builder_reception_id_preference_<?php echo esc_attr( $id_index ); ?>_description"
				>
					<?php
					if ( ! $app_config || ! $app_config->get_reception_id() ) {
						echo '<option value="" selected disabled>— Select —</option>';
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
							<option value="<?php echo esc_attr( $slug ); ?>"
								data-has-video="<?php echo esc_attr( $video ); ?>"
								<?php echo esc_attr( $selected ); ?>
							>
								<?php echo esc_html( $available_reception->name ); ?>
							</option>
						<?php
					}
					?>
				</select>
				<br />
				<em id="myvideoroom_room_builder_reception_id_preference_<?php echo esc_attr( $id_index ); ?>_description">
					<?php

					$receptions_page   = menu_page_url( AdminNavigation::PAGE_SLUG_ROOM_TEMPLATES, false );
					$receptions_target = '';

					if ( ! $receptions_page ) {
						$receptions_page   = Factory::get_instance( Endpoints::class )->get_rooms_endpoint() . '/views/receptions';
						$receptions_target = ' target="_blank"';
					}

					printf(
					/* translators: %s is a link to the templates admin page */
						esc_html__(
							'The design of the reception. Some recetion additionally will show a background video.
			                For a full list of available receptions see the %s page',
							'myvideoroom'
						),
						'<a href="' . esc_url( $receptions_page ) . '"' . esc_attr( $receptions_target ) . '>' .
						esc_html__( 'templates', 'myvideoroom' ) .
						'</a>'
					);
					?>
				</em>
				<br />

				<div class="custom-video-settings">
					<label for="myvideoroom_room_builder_reception_custom_video_preference_<?php echo esc_attr( $id_index ); ?>">
						<?php echo esc_html__( 'Customize Reception Waiting Room Video', 'myvideoroom' ); ?>
					</label>
					<input type="checkbox"
						name="myvideoroom_room_builder_reception_custom_video_preference"
						id="myvideoroom_room_builder_reception_custom_video_preference_<?php echo esc_attr( $id_index ); ?>"
						<?php echo $app_config && $app_config->get_reception_video() ? 'checked' : ''; ?>
					/>
					<br />

					<div class="custom-video-url">
						<label for="myvideoroom_room_builder_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>">
							<?php echo esc_html__( 'Video URL', 'myvideoroom' ); ?>:
						</label>
						<input type="text"
							id="myvideoroom_room_builder_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
							name="myvideoroom_room_builder_reception_waiting_video_url"
							<?php
							if ( $app_config ) {
								echo 'value="' . esc_attr( $app_config->get_reception_video() ) . '"'; }
							?>
							aria-describedby="myvideoroom_room_builder_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>_description"
						/>
						<br />
						<em id="myvideoroom_room_builder_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>_description">
							<?php
								esc_html_e(
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

		<?php wp_nonce_field( 'build_shortcode', 'myvideoroom_roombuilder_nonce' ); ?>
		<input type="submit"
			name="submit"
			id="submit"
			class="button button-primary"
			value="<?php echo esc_html__( 'Generate Room and Shortcode', 'myvideoroom' ); ?>"
		/>
	</form>

	<?php
	return ob_get_clean();
};
