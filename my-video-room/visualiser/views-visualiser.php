<?php
/**
 * Renders the a Dynamic Shortcode visualisation system to make it easier for admins to visualise a room.
 *
 * @param string|null $current_user_setting
 * @param array $available_layouts
 *
 * @package MyVideoRoomExtrasPlugin\Views
 */

use MyVideoRoomExtrasPlugin\Entity\UserVideoPreference;
use MyVideoRoomExtrasPlugin\Factory;



return function (
	array $available_layouts,
	array $available_receptions,
	UserVideoPreference $current_user_setting = null,
	string $room_name,
	int $id_index = 0,
	int $user_id = null,
	string $video_reception_url = null
): string {
	ob_start();

	?>
	<div id="video-host-wrap" style="border: 3px solid #969696;
				background: #ebedf1;
				padding: 12px;
				margin: 5px;">
				<table style="width:100%">
				<tr>
					<th style="width:80%" ><h1 style="    text-align-last: center;">Shortcode Visualizer for Room:
			<?php
				$output = str_replace( '-', ' ', $room_name );
				echo esc_html( ucwords( $output ) );
			?>
			</h1></th>
					<th style="width:20%" >
						<img
								src="<?php echo esc_url( plugins_url( '/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>"
								alt="My Video Room Extras"
								width="120"
								height="120"
						/>
					</th>
				</tr>
				</table>
				<p>Please select your preferred configuration of Room name - Reception and other settings, to see your Room layouts and Shortcode Design.</p>
				<hr>
				<table style="width:100%">
				<form method="post" action="">
			<tr>
				<th style="width:27%" >Naming</th>
				<th style="width:25%" >Room Layout</th>
				<th style="width:47%">Guest Settings</th>
			</tr>
			<tr>

					<td>
						<label for="myvideoroom_visualiser_room_name">Room Name</label>
						<input	type="text"
								id="myvideoroom_visualiser_room_name"
								name="myvideoroom_visualiser_room_name"
								style= "    width: 65%;    background: #e3e7e8; "
								value="<?php echo esc_html( $room_name ); ?>">
					</td>
					<td>
							<select
								class="myvideoroom_visualiser_layout_id_preference"
								name="myvideoroom_visualiser_layout_id_preference"
								id="myvideoroom_visualiser_layout_id_preference_<?php echo esc_attr( $id_index ); ?>"
								style= " width: 75%; ">
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
						<p style="display: inline"><b>Disable Guest Floorplan</b></p>

						<input
						type="checkbox"
						class="myvideoroom_visualiser_show_floorplan_preference"
						name="myvideoroom_visualiser_show_floorplan_preference"
						id="myvideoroom_visualiser_show_floorplan_preference_<?php echo esc_attr( $id_index ); ?>"
						<?php echo $current_user_setting && $current_user_setting->get_show_floorplan_setting() ? 'checked' : ''; ?>
					/>
					(Automatically turns on Reception)
					</td>
					<td>
					<label for="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<p style ="display: inline"><b>Enable Reception ?</b></p>
					</label>
						<input
							type="checkbox"
							class="myvideoroom_visualiser_reception_enabled_preference"
							name="myvideoroom_visualiser_reception_enabled_preference"
							id="myvideoroom_visualiser_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
							<?php echo $current_user_setting && $current_user_setting->is_reception_enabled() ? 'checked' : ''; ?>
						/>

						<label for="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>">
						<b>Reception Appearance</b>
					</label>
					<select
							class="myvideoroom_visualiser_reception_id_preference"
							name="myvideoroom_visualiser_reception_id_preference"
							id="myvideoroom_visualiser_reception_id_preference_<?php echo esc_attr( $id_index ); ?>"
							style= "    width: 25%;  "
					>
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
					<input
						type="checkbox"
						class="myvideoroom_visualiser_reception_video_enabled_preference"
						name="myvideoroom_visualiser_reception_video_enabled_preference"
						id="myvideoroom_visualiser_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
						<?php echo $current_user_setting && $current_user_setting->get_reception_video_enabled_setting() ? 'checked' : ''; ?>
					/>
					<label for="myvideoroom_visualiser_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>">
					<p style ="display: inline"><b>Customize Reception Waiting Room Video</b></p><br>
					</label>

					<label for="myvideoroom_extras_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?> " style ="display: inline">
					Video URL:
					</label>
					<input
								type="text"
								id="myvideoroom_visualiser_reception_waiting_video_url"
								name="myvideoroom_visualiser_reception_waiting_video_url"
								style= "    width: 75%;    background: #e3e7e8; display: inline;"
								value="<?php echo esc_url( $video_reception_url ); ?>">

					</td>

					<?php wp_nonce_field( 'myvideoroom_extras_update_user_video_preference', 'nonce' ); ?>
					<tr><input type="submit" name="submit" id="submit" class="button button-primary" value="Generate Room and Shortcode"  /></tr>
				</form>

			</tr>

		</table>

	</div>
	<?php
	return ob_get_clean();
};
