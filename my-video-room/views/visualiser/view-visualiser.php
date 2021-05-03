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
	<h3 class="myvideoroom-heading-head-top">
		<?php
		if ( ShortcodeRoomVisualiser::DEFAULT_ROOM_NAME !== $room_name ) {
			$output = str_replace( '-', ' ', $room_name );
			$output = 'Room ' . $output . ' created';

			echo esc_html( ucwords( $output ) );
		}
		?>
	</h3>

	<p class="myvideoroom-explainer-text">
		<?php
		echo esc_html__(
			'To get started you can select your room design. Use this tool to explore and select your preferred configuration of Room name - Reception and other settings. The tool is interactive and you can drag users into and out of 
            Reception to to see your Room layouts and Shortcode	Design.',
			'myvideoroom'
		)
		?>
	</p>
	<hr>
	<form method="post" action="">
		<table style="width:100%">
			<tr>
				<th style="width:27%"><?php echo esc_html__( 'Naming', 'myvideoroom' ); ?></th>
				<th style="width:25%"><?php echo esc_html__( 'Room Layout', 'myvideoroom' ); ?></th>
				<th style="width:47%"><?php echo esc_html__( 'Guest Settings', 'myvideoroom' ); ?></th>
			</tr>
			<tr>

				<td class="myvideoroom-td-head-top">
					<label for="myvideoroom_visualiser_room_name"><?php echo esc_html__( 'Room Name', 'myvideoroom' ); ?></label>
					<input type="text"
						   id="myvideoroom_visualiser_room_name"
						   name="myvideoroom_visualiser_room_name"
						   style="width: 65%; background: #e3e7e8;"
						   value="<?php echo esc_html( $room_name ); ?>"
					/>
				</td>
				<td class="myvideoroom-td-head-top">
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

						if ( $current_user_setting
							&& $current_user_setting->get_layout_id() === $slug
						) {
							echo '<option value="' . esc_attr( $slug ) . '" selected>' . esc_html( $available_layout->name ) . '</option>';
						} else {
							echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $available_layout->name ) . '</option>';
						}
					}
					?>
					</select>

					<br>
					<p style="display: inline"><b><?php echo esc_html__( 'Disable Guest Floorplan', 'myvideoroom' ); ?></b></p>

					<input type="checkbox"
						   class="myvideoroom-option-box"
						   name="myvideoroom_visualiser_show_floorplan_preference"
						   id="myvideoroom_visualiser_show_floorplan_preference_<?php echo esc_attr( $id_index ); ?>"
						   <?php echo $current_user_setting && $current_user_setting->get_show_floorplan_setting() ? 'checked' : ''; ?>
					/>
					<br>
					<?php echo esc_html__( '(Automatically turns on Reception)', 'myvideoroom' ); ?>
				</td>
				<td class="myvideoroom-td-head-top">
					<label for="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
						<p style="display: inline"><b><?php echo esc_html__( 'Enable Reception ?', 'myvideoroom' ); ?></b></p>
					</label>

					<input type="checkbox"
						   class="myvideoroom-option-box"
						   name="myvideoroom_visualiser_reception_enabled_preference"
						   id="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
						   <?php echo $current_user_setting && $current_user_setting->is_reception_enabled() ? 'checked' : ''; ?>
					/>

					<label for="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>">
						<b><?php echo esc_html__( 'Reception Appearance', 'myvideoroom' ); ?></b>
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
					<br>

					<input type="checkbox"
						   class="myvideoroom-option-box"
							name="myvideoroom_visualiser_reception_video_enabled_preference"
							id="myvideoroom_visualiser_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
							<?php echo $current_user_setting && $current_user_setting->get_reception_video_enabled_setting() ? 'checked' : ''; ?>
					/>

					<label for="myvideoroom_visualiser_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
						<p style="display: inline"><b><?php echo esc_html__( 'Customize Reception Waiting Room Video', 'myvideoroom' ); ?></b></p><br>
					</label>

					<label for="myvideoroom_extras_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?> "
						style="display: inline">
						<?php echo esc_html__( 'Video URL', 'myvideoroom' ); ?>:
					</label>

					<input type="text"
						   id="myvideoroom_visualiser_reception_waiting_video_url"
						   name="myvideoroom_visualiser_reception_waiting_video_url"
						   style="width: 75%; background: #e3e7e8; display: inline;"
						   value="<?php echo esc_url( $video_reception_url ); ?>"
					/>
					<?php wp_nonce_field( 'myvideoroom_extras_update_user_video_preference', 'nonce' ); ?>
				</td>
			</tr>

			<tr>
				<td>
					<input type="submit"
						   name="submit"
						   id="submit"
						   class="button button-primary"
						   value="<?php echo esc_html__( 'Generate Room and Shortcode', 'myvideoroom' ); ?>"
					/>
				</td>
			</tr>
		</table>
	</form>


	<?php
	return ob_get_clean();
};
