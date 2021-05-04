<?php
/**
 * Renders the a Dynamic Shortcode visualisation system to make it easier for admins to visualise a room.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomPlugin\Views
 */

use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser;
use MyVideoRoomPlugin\Visualiser\UserVideoPreference;

return function (
	array $available_layouts,
	array $available_receptions,
	UserVideoPreference $current_user_setting = null,
	string $room_name,
	int $id_index = 0,
	string $video_reception_url = null
): string {
	ob_start();

	?>

	<?php
	if ( $room_name ) {
		echo '<strong>';
		printf(
			/* translators: %s is the text user supplied room name */
			esc_html__( 'Room %s created.', 'myvideoroom' ),
			esc_html( str_replace( '-', ' ', $room_name ) )
		);
		echo '</strong>';
	}
	?>

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

	<form method="post" action="">
		<fieldset>
			<legend><?php echo esc_html__( 'Naming', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_visualiser_room_namee_<?php echo esc_attr( $id_index ); ?>">
					<?php echo esc_html__( 'Room Name', 'myvideoroom' ); ?>
			</label>
			<input type="text"
				   placeholder="<?php esc_html_e( 'Your Room Name', 'myvideoroom' ); ?>"
				   id="myvideoroom_visualiser_room_namee_<?php echo esc_attr( $id_index ); ?>"
				   name="myvideoroom_visualiser_room_name"
				   style="width: 65%; background: #e3e7e8;"
				   value="<?php echo esc_html( $room_name ); ?>"
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
					style=" width: 75%; "
			>
				<?php
				foreach ( $available_layouts as $available_layout ) {
					$slug = $available_layout->slug;

					if ( ! $slug ) {
						$slug = $available_layout->id;
					}

					if ( $current_user_setting && $current_user_setting->get_layout_id() === $slug ) {
						echo '<option value="' . esc_attr( $slug ) . '" selected>' . esc_html( $available_layout->name ) . '</option>';
					} else {
						echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $available_layout->name ) . '</option>';
					}
				}
				?>
			</select>
			<br />

			<label for="myvideoroom_visualiser_show_floorplan_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Disable guest floorplan', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				   class="myvideoroom-option-box"
				   name="myvideoroom_visualiser_show_floorplan_preference"
				   id="myvideoroom_visualiser_show_floorplan_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->get_show_floorplan_setting() ? 'checked' : ''; ?>
			/>
			<em><?php echo esc_html__( '(automatically turns on reception)', 'myvideoroom' ); ?></em>
		</fieldset>

		<fieldset>
			<legend><?php echo esc_html__( 'Guest Settings', 'myvideoroom' ); ?></legend>
			<label for="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Enable guest reception?', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				   class="myvideoroom-option-box"
				   name="myvideoroom_visualiser_reception_enabled_preference"
				   id="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				   <?php echo $current_user_setting && $current_user_setting->is_reception_enabled() ? 'checked' : ''; ?>
			/>
			<br />

			<label for="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Reception Appearance', 'myvideoroom' ); ?>
			</label>
			<select class="myvideoroom_visualiser_reception_id_preference"
				name="myvideoroom_visualiser_reception_id_preference"
				id="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>"
				style="    width: 25%;  ">
				<?php
				if ( ! $current_user_setting || ! $current_user_setting->get_reception_id() ) {
					echo '<option value="" selected disabled> --- </option>';
				}

				foreach ( $available_receptions as $available_reception ) {
					$slug = $available_reception->slug;

					if ( ! $slug ) {
						$slug = $available_reception->id;
					}

					if ( $current_user_setting
						&& $current_user_setting->get_reception_id() === $slug
					) {
						echo '<option value="' . esc_attr( $slug ) . '" selected>' . esc_html( $available_reception->name ) . '</option>';
					} else {
						echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $available_reception->name ) . '</option>';
					}
				}
				?>
			</select>
			<br />

			<label for="myvideoroom_visualiser_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php echo esc_html__( 'Customize Reception Waiting Room Video', 'myvideoroom' ); ?>
			</label>
			<input type="checkbox"
				   name="myvideoroom_visualiser_reception_video_enabled_preference"
				   id="myvideoroom_visualiser_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				   <?php echo $current_user_setting && $current_user_setting->get_reception_video_enabled_setting() ? 'checked' : ''; ?>
			/>
			<br />

			<label for="myvideoroom_extras_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
				style="display: inline">
				<?php echo esc_html__( 'Video URL', 'myvideoroom' ); ?>:
			</label>
			<input type="text"
				   id="myvideoroom_extras_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
				   name="myvideoroom_visualiser_reception_waiting_video_url"
				   style="width: 75%; background: #e3e7e8; display: inline;"
				   value="<?php echo esc_url( $video_reception_url ); ?>"
			/>

			<?php wp_nonce_field( 'myvideoroom_extras_update_user_video_preference', 'nonce' ); ?>

		</fieldset>

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
