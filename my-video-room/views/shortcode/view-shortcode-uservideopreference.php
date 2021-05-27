<?php
/**
 * Renders the form for changing the User Video preferences.
 *
 * @param string|null $current_user_setting
 * @param array       $available_layouts
 *
 * @package MyVideoRoomPlugin\Core\Views\Shortcode
 */

use MyVideoRoomPlugin\Entity\UserVideoPreference;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\UserVideoPreference as UserVideoPreferenceDAO;

return function (
	array $available_layouts,
	array $available_receptions,
	?UserVideoPreference $current_user_setting,
	string $room_name,
	int $id_index = 0,
	int $user_id = null
): string {
	ob_start();

	?>
	<div id="video-host-wrap" class="mvr-nav-settingstabs-outer-wrap">
		<h1><?php esc_html_e( 'Video Host Settings for ', 'my-video-room' ); ?>
			<?php
			$output = str_replace( '-', ' ', $room_name );
			echo esc_html( ucwords( $output ) );
			?>
		</h1>
		<?php
		// room permissions info.
		$output              = null;
		$reception_enabled   = true;
		$floorplan_enabled   = true;
		$reception_video_url = null;

		$saved_preferences = Factory::get_instance( UserVideoPreferenceDAO::class )->get_by_id( $user_id, $room_name );

		if ( $saved_preferences ) {
			$reception_enabled   = $saved_preferences->is_reception_enabled();
			$floorplan_enabled   = $saved_preferences->is_floorplan_enabled();
			$reception_video_url = trim( $saved_preferences->get_reception_video_url_setting() );
		}

		if ( $reception_enabled || $floorplan_enabled ) {
			$output .= '<p class="mvr-main-button-notice" title="' . esc_html__( 'Your Guests will see the Reception Template of your choice and will not be admitted into the room until you drag their icon in.', 'myvideoroom' ) . '">' . esc_html__( 'Reception Enabled', 'myvideoroom' ) . '</p>';
		}
		if ( $floorplan_enabled ) {
			$output .= '<p class="mvr-main-button-notice" title="' . esc_html__( 'Your Guests will not see the Image of the Room Floorplan and only have a classic Video Experience', 'myvideoroom' ) . '">' . esc_html__( 'Guest Template Disabled', 'myvideoroom' ) . '</p>';
		}
		if ( ! $reception_enabled && ! $floorplan_enabled ) {
			$output .= '<p class="mvr-main-button-notice" title="' . esc_html__( 'Your Guests can view the room and see the presence of other participants. They still require you to be in the room to join fully.', 'myvideoroom' ) . '">' . esc_html__( 'Unrestricted Entry', 'myvideoroom' ) . '</p>';
		}
		// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Elements above are already safe.
		echo '<div class="mvr-button-table"> ' . $output . ' </div>';
		?>
		<hr>
		<form method="post" action="">
			<input name="myvideoroom_user_room_name" type="hidden" value="<?php echo esc_attr( $room_name ); ?>" />

			<h2 class="mvr-title-header">
				<label for="myvideoroom_user_layout_id_preference_<?php echo esc_attr( $id_index ); ?>">
					<?php esc_html_e( 'Video layout setting:', 'my-video-room' ); ?>
				</label>
			</h2>
			<div class="mvr-video-template mvr-title-header"><i class="dashicons mvr-icons dashicons-editor-help"></i>
			</div>

			<div class="mvr-hide mvr-template-table">
				<div class="mvr-template-table" style="float:left">
					<h2><?php esc_html_e( 'Lifelike Rooms', 'my-video-room' ); ?></h2>
					<?php
					esc_html_e(
						'You can pick your room template to suit the type of meeting you have. Including whether to have breakout areas, seating position, seniority, and 
									live room placement. ',
						'my-video-room'
					);
					?>
					<img src="<?php echo esc_url( plugins_url( '/../../img/video-template2.png', __FILE__ ) ); ?>">
				</div>
				<div class="mvr-template-table" style="float:right">
					<img src="<?php echo esc_url( plugins_url( '/../../img/video-template1.png', __FILE__ ) ); ?>">

				</div>
			</div>
			<select
				class="mvr-roles-multiselect mvr-select-box"
				name="myvideoroom_user_layout_id_preference"
				id="myvideoroom_user_layout_id_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php
				if ( ! $current_user_setting || ! $current_user_setting->get_layout_id() ) {
					echo '<option value="" selected disabled> --- </option>';
				}
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
			<p class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Use this setting to control the layout of the room you and your guests will see. There are lots of templates to chose from, and more are being added monthly.', 'my-video-room' ); ?>
			</p>
			<label for="myvideoroom_user_show_floorplan_preference_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Disable Interactive Floorplan:', 'my-video-room' ); ?></strong>
			</label>
			<input
				type="checkbox"
				class="myvideoroom_user_show_floorplan_preference"
				name="myvideoroom_user_show_floorplan_preference"
				id="myvideoroom_user_show_floorplan_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->is_floorplan_enabled() ? 'checked' : ''; ?>
			/>
			<p class="mvr-preferences-paragraph">
				<?php
				esc_html_e(
					'Disable Floorplan and use classic video without templates. Please note if you select this,
							the reception will automatically be turned on and users will be held in reception until you allow them in.',
					'my-video-room'
				);
				?>
			</p>
			<hr />
			<h2 class="mvr-title-header">
				<label for="myvideoroom_user_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
					class="mvr-preferences-paragraph">
					<i class="dashicons mvr-icons dashicons-lock"></i><?php esc_html_e( 'Enable Reception', 'my-video-room' ); ?>
				</label>
			</h2>
			<input
				type="checkbox"
				class="myvideoroom_user_reception_enabled_preference"
				name="myvideoroom_user_reception_enabled_preference"
				id="myvideoroom_user_reception_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->is_reception_enabled() ? 'checked' : ''; ?>
			/>
			<p class="mvr-preferences-paragraph">
				<?php
				esc_html_e(
					'Enable this if you want to have guests wait in a secure location that you must allow into your space,
						or disable if you want people to pop in or out of your room. This setting is automatically applied if you
						chose the "Disable Floorplan" feature which automatically turns on Reception.',
					'my-video-room'
				);
				?>
				<br>
			</p>

			<br>

			<label for="myvideoroom_user_reception_id_preference_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Reception Appearance', 'my-video-room' ); ?>
			</label>
			<div class="mvr-template-table mvr-reception-image mvr-title-header"><i
					class="dashicons mvr-icons dashicons-editor-help"></i></div>

			<div class="mvr-hide">
				<div class="mvr-template-table" style="float:left">
					<h2><?php esc_html_e( 'Interactive Receptions', 'my-video-room' ); ?></h2>
					<?php
					esc_html_e(
						'A Reception can be styled in many ways, and is what your guest will see whilst they wait for you. You can even pick a video for them
							to watch whilst they wait.',
						'my-video-room'
					);
					?>
				</div>
				<div class="mvr-template-table" style="float:right">
					<img class="mvr-template-image"
						src="<?php echo esc_url( plugins_url( '/../../img/reception-view.png', __FILE__ ) ); ?>">
				</div>

			</div>

			<select
				class="mvr-roles-multiselect mvr-select-box"
				name="myvideoroom_user_reception_id_preference"
				id="myvideoroom_user_reception_id_preference_<?php echo esc_attr( $id_index ); ?>">
				<?php
				if ( ! $current_user_setting || ! $current_user_setting->get_reception_id() ) {
					echo '<option value="" selected disabled> --- Please Select ---</option>';
				}

				foreach ( $available_receptions as $available_reception ) {
					$slug = $available_reception->slug;

					if ( ! $slug ) {
						$slug = $available_reception->id;
					}

					if ( $current_user_setting && $current_user_setting->get_reception_id() === $slug ) {
						echo '<option value="' . esc_attr( $slug ) . '" selected>' . esc_html( $available_reception->name ) . '</option>';
					} else {
						echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $available_reception->name ) . '</option>';
					}
				}
				?>
			</select>
			<br><br>
			<p class="mvr-preferences-paragraph">
				<?php
				esc_html_e(
					'Use this setting to decide what you want your Video Space reception to look like. This will be shown for all guests while they wait for
						admission into the room. The enable reception setting must be turned on for this setting to take effect.',
					'my-video-room'
				);
				?>
			</p><br>
			<label for="myvideoroom_user_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<strong><?php esc_html_e( 'Enable Custom Video for Reception :', 'my-video-room' ); ?></strong>
			</label>
			<div class="mvr-template-table mvr-custom-video mvr-title-header"><i
					class="dashicons mvr-icons dashicons-editor-help"></i></div>

			<div class="mvr-hide">
				<div class="mvr-template-table" style="float:left">
					<h2><?php esc_html_e( 'Choose your Reception Video Screening', 'my-video-room' ); ?></h2>
					<?php esc_html_e( 'Select the content you want your guest to view whilst they wait for you.', 'my-video-room' ); ?>
					<img class="mvr-template-image"
						src="<?php echo esc_url( plugins_url( '/../../img/sitevideoreception.png', __FILE__ ) ); ?>">
				</div>
				<div class="mvr-template-table" style="float:right">
					<img src="<?php echo esc_url( plugins_url( '/../../img/video-reception.png', __FILE__ ) ); ?>">
				</div>
			</div>

			<input
				type="checkbox"
				class="myvideoroom_user_reception_video_enabled_preference"
				name="myvideoroom_user_reception_video_enabled_preference"
				id="myvideoroom_user_reception_video_enabled_preference_<?php echo esc_attr( $id_index ); ?>"
				<?php echo $current_user_setting && $current_user_setting->is_reception_video_enabled() ? 'checked' : ''; ?>
			/>
			<br>
			<label for="myvideoroom_user_reception_waiting_video_url_<?php echo esc_attr( $id_index ); ?>"
				class="mvr-preferences-paragraph">
				<?php esc_html_e( 'Your Video URL:', 'my-video-room' ); ?>
			</label>
			<input
				type="text"
				id="myvideoroom_user_reception_waiting_video_url"
				name="myvideoroom_user_reception_waiting_video_url"
				class="mvr-roles-multiselect mvr-select-box"
				value="<?php echo esc_url( $reception_video_url ); ?>">

			<br><br>
			<p class="mvr-preferences-paragraph">
				<?php
				esc_html_e(
					'This setting controls whether you want your guests to see a video or movie channel if Reception is enabled. Enter a url in the form of https://youvideoservice.com/yourvideofolder/video.mp4 - and this video will be displayed to your guests in your Dynamic reception areas if you have enabled a guest reception template option that can show video.',
					'my-video-room'
				);
				?>
			</p>
			<hr>

			<input type="hidden" name="myvideoroom_room_name" value="<?php echo esc_attr( $room_name ); ?>" />
			<input type="hidden" name="myvideoroom_user_id" value="<?php echo esc_attr( $user_id ); ?>" />

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( \MyVideoRoomPlugin\Library\HttpPost::class )->create_form_submit(
				'update_user_video_preference',
				\esc_html__( 'Save changes', 'myvideoroom' )
			);
			?>
		</form>
	</div>
	<?php
	return ob_get_clean();
};
