<?php
/**
 * Renders the a Dynamic Shortcode visualisation system to make it easier for admins to visualise a room.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomPlugin\Views
 */

use MyVideoRoomPlugin\Visualiser\AppShortcodeConstructor;

return function (
	array $available_layouts,
	array $available_receptions,
	AppShortcodeConstructor $app_config = null,
	int $id_index = 0
): string {
	ob_start();

	?>
	<h2><?php esc_html_e( 'Visual Room Builder', 'myvideoroom' ); ?></h2>

	<p class="myvideoroom-explainer-text">
		<?php
		echo esc_html__(
			'To get started you can select your room design. Use this tool to explore and select your preferred configuration of Room name - Reception and other settings. The tool is interactive and you can drag users into and out of 
            Reception to to see your Room layouts and Shortcode	Design.',
			'myvideoroom'
		)
		?>
	</p>

	<hr />

	<?php
	if ( $app_config ) {
		?>
		<ul>
			<?php
			echo '<li class="notice notice-success is-dismissible"><p>';

			if ( $app_config->get_name() ) {
				printf(
				/* translators: %s is the text user supplied room name */
					esc_html__( 'Configuration for room %s created.', 'myvideoroom' ),
					esc_html( str_replace( '-', ' ', $app_config->get_name() ) )
				);
			} else {
				esc_html_e( 'Configuration for room created.', 'myvideoroom' );
			}

			echo '</p></li>';
			?>
		</ul>
		<?php
	}
	?>

	<form class="myvideoroom-visualiser-settings" method="post" action="">
		<fieldset>
			<legend><?php echo esc_html__( 'Naming', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_visualiser_room_namee_<?php echo esc_attr( $id_index ); ?>">
					<?php echo esc_html__( 'Room Name', 'myvideoroom' ); ?>
			</label>
			<input type="text"
				placeholder="<?php esc_html_e( 'Your Room Name', 'myvideoroom' ); ?>"
				id="myvideoroom_visualiser_room_namee_<?php echo esc_attr( $id_index ); ?>"
				name="myvideoroom_visualiser_room_name"
				value="<?php echo esc_html( $app_config && $app_config->get_name() ); ?>"
			/>
		</fieldset>

		<fieldset>
			<legend><?php echo esc_html__( 'Room Layout', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_visualiser_layout_id_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Layout', 'myvideoroom' ); ?>
			</label>
			<select class="myvideoroom_visualiser_layout_id_preference"
				name="myvideoroom_visualiser_layout_id_preference"
				id="myvideoroom_visualiser_layout_id_preference_<?php echo esc_attr( $id_index ); ?>"
			>
				<?php
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

			<label for="myvideoroom_visualiser_disable_floorplan_preference<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Disable guest floorplan', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				name="myvideoroom_visualiser_disable_floorplan_preference"
				id="myvideoroom_visualiser_disable_floorplan_preference<?php echo esc_attr( $id_index ); ?>"
				<?php echo ! $app_config || ! $app_config->is_floorplan_enabled() ? 'checked' : ''; ?>
			/>
			<em><?php echo esc_html__( '(automatically turns on reception)', 'myvideoroom' ); ?></em>
		</fieldset>

		<fieldset>
			<legend><?php echo esc_html__( 'Guest Settings', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Enable guest reception?', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				name="myvideoroom_visualiser_reception_enabled_preference"
				id="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo ! $app_config || $app_config->is_reception_enabled() ? 'checked' : ''; ?>
			/>

			<div class="reception-settings">
				<label for="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>">
					<?php echo esc_html__( 'Reception Appearance', 'myvideoroom' ); ?>
				</label>
				<select class="myvideoroom_visualiser_reception_id_preference"
					name="myvideoroom_visualiser_reception_id_preference"
					id="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>"
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

						if ( $app_config && $app_config->get_reception_id() === $slug
						) {
							echo '<option value="' . esc_attr( $slug ) . '" selected>' . esc_html( $available_reception->name ) . '</option>';
						} else {
							echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $available_reception->name ) . '</option>';
						}
					}
					?>
				</select>
				<br />

				<label for="myvideoroom_visualiser_reception_custom_video_preference_<?php echo esc_attr( $id_index ); ?>">
					<?php echo esc_html__( 'Customize Reception Waiting Room Video', 'myvideoroom' ); ?>
				</label>
				<input type="checkbox"
					name="myvideoroom_visualiser_reception_custom_video_preference"
					id="myvideoroom_visualiser_reception_custom_video_preference_<?php echo esc_attr( $id_index ); ?>"
					<?php echo $app_config && $app_config->get_reception_video() ? 'checked' : ''; ?>
				/>
				<br />

				<div class="custom-video-settings">
					<label for="myvideoroom_visualiser_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>">
						<?php echo esc_html__( 'Video URL', 'myvideoroom' ); ?>:
					</label>
					<input type="text"
						id="myvideoroom_visualiser_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
						name="myvideoroom_visualiser_reception_waiting_video_url"
						<?php
						if ( $app_config ) {
							echo 'value="' . esc_attr( $app_config->get_reception_video() ) . '"'; }
						?>
					/>
				</div>
			</div>
		</fieldset>

		<?php wp_nonce_field( 'myvideoroom_visualiser_nonce', 'nonce' ); ?>
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
