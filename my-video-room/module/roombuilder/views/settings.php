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
	$index = \wp_rand( 1, 9000 );

	$html_lib = Factory::get_instance( HTML::class, array( 'room_builder' ) );
	?>
<!-- Module Header -->
	<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left-reduced">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-welcome-widgets-menus"></i><?php esc_html_e( 'Room Design and Visualisation', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right-wide">
		</div>
	</div>

<!-- Navigation Header -->
	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
		<ul class="mvr-ul-style-top-menu">
			<li>
				<a class="nav-tab nav-tab-active" href="#<?php echo \esc_attr( $html_lib->get_id( 'designer' ) ); ?>">
					<?php \esc_html_e( 'Visual Room Designer', 'myvideoroom' ); ?>
				</a>
			</li>

			<li>
				<a class="nav-tab" href="#<?php echo \esc_attr( $html_lib->get_id( 'usage' ) ); ?>">
					<?php \esc_html_e( 'Using Templates', 'myvideoroom' ); ?>
				</a>
			</li>

			<li>
				<a class="nav-tab" href="#<?php echo \esc_attr( $html_lib->get_id( 'layouts' ) ); ?>">
					<?php \esc_html_e( 'Participant Templates', 'myvideoroom' ); ?>
				</a>
			</li>

			<li>
				<a class="nav-tab" href="#<?php echo \esc_attr( $html_lib->get_id( 'receptions' ) ); ?>">
					<?php \esc_html_e( 'Reception Templates', 'myvideoroom' ); ?>
				</a>
			</li>
		</ul>
	</nav>
<!-- 
	Room Designer 
-->
	<article id="<?php echo \esc_attr( $html_lib->get_id( 'designer' ) ); ?>">
	<div class="myvideoroom-menu-settings">
		<div class="myvideoroom-header-table-left">
			<h1><i
					class="myvideoroom-header-dashicons dashicons-welcome-view-site"></i><?php esc_html_e( 'Visual Room Designer and Custom Room Generator', 'myvideoroom' ); ?>
			</h1>
		</div>
		<div class="myvideoroom-header-table-right-wide">
		</div>
	</div>
<!-- Module State and Description Marker -->
	<div class="myvideoroom-feature-outer-table">
			<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
				<h2><?php esc_html_e( 'Description', 'myvideoroom' ); ?></h2>
			</div>
			<div class="myvideoroom-feature-table-large">
			<h2><?php esc_html_e( 'Create Custom Rooms and the Shortcodes to Use Them Anywhere', 'myvideoroom' ); ?></h2>	
				<p class="myvideoroom-explainer-text">
					<?php
					esc_html_e(
						'Use this tool to explore and create your preferred configuration of MyVideoRoom shortcode pairs, including layouts, receptions, permissions, and other settings. The preview is interactive and allows you to drag users in and out of the reception, and to see the output for both hosts and guests. The tool will output the shortcodes that you can then copy and paste into your page or post.',
						'myvideoroom'
					);
					?>
				</p>
				<p class="myvideoroom-explainer-text">
					<?php
					echo \esc_html__(
						'Note that automatically generated rooms like Personal Video Rooms, and Site Conference Rooms have their own security, reception, and other settings which must be set on a per room basis, and not in this tool.',
						'myvideoroom'
					)
					?>
				</p>
			</div>
		</div>
<!-- Shortcode Builder Section  -->		
		<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
						class="mvr-nav-settingstabs-outer-wrap">
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Explorer', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
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
					'The name of the room. All video rooms on the same website that share a name will share the same video group. Defaults to the site name',
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
				\printf(
				/* translators: %s is a link to the templates admin page */
					\esc_html__(
						'The layout of the room, determines the background image, and the number of seats and seat groups. See the %s on this page for a visual list of available room layouts and more details.',
						'myvideoroom'
					),
					\esc_html__( 'Template tabs', 'myvideoroom' )
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
					'Prevents guests from seeing the floorplan, and selecting their own seats. Will automatically enable the reception',
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
					'The guest reception prevents guests from taking their own seats, and instead puts them into a waiting room from where the host can drag them into a seat. Disabling this option will also enable the guest floorplan',
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
							'The design of the reception. Some receptions additionally will show a background video. For a full list of available receptions see the %s page',
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
								'Allow customisation of the video shown in the reception. Can either provide a full url to a playable video, or instead pass the 11 character YouTube video ID.',
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
							</div>
						</div>
	</article>
<!-- 
	Template Section  
-->

	<article id="<?php echo \esc_attr( $html_lib->get_id( 'usage' ) ); ?>">

<!-- Module Header -->				
				<div class="myvideoroom-menu-settings">
					<div class="myvideoroom-header-table-left">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-admin-page"></i><?php esc_html_e( 'How to use Templates', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right">
					</div>
				</div>

<!--  Description Marker -->
				<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Working with Templates', 'myvideoroom' ); ?></h2>
					</div>
					<div class="myvideoroom-feature-table-large">
						<p>
							<?php
							esc_html_e(
								'Templates are the visual representation of your room. They allow your guests to understand the type of meeting they are in. You can see a good representation of available templates for both reception, and video rooms, and reception templates tab. We are adding more templates all the time, and coming soon you will be able to make your own designs.',
								'myvideoroom'
							);
							?>
						</p>
					</div>
				</div>	
<!-- Participant Templates -->
		<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
					<div class="myvideoroom-feature-outer-table">
						<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
							class="myvideoroom-feature-table-small">
							<h2><?php esc_html_e( 'Participant Templates', 'myvideoroom' ); ?></h2>
						</div>
						<div class="myvideoroom-feature-table-large">
							<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
						<p>
							<?php
							esc_html_e(
								'Hosts enter a room by clicking on an available hotspot on the Room Template, until they click they are not joined the meeting, but they can see the icons of users that have already joined.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'Users can be admitted into a room if at least one Host has joined the room. Waiting users appear as icons in the top of the screen, which must be dragged into an available seat.',
								'myvideoroom'
							);
							?>
						</p>
							<img alt="MyVideoRoom Host View"
				src="<?php echo \esc_url( \plugins_url( '../img/host-view.png', \realpath( __DIR__ . '/../' ) ) ); ?>" />

							</div>
						</div>
					</div>

	<!-- Reception Templates -->
			<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
					<div class="myvideoroom-feature-outer-table">
						<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
							class="myvideoroom-feature-table-small">
							<h2><?php esc_html_e( 'Reception Templates', 'myvideoroom' ); ?></h2>
						</div>
						<div class="myvideoroom-feature-table-large">
							<div id="childmodule<?php echo esc_attr( $index++ ); ?>">
						<p>
							<?php
							esc_html_e(
								'If Enabled - the reception area is a holding area for guests who can not see other guests or the meeting until admitted. Hosts must enable reception for the room (or admins enforce it at site level) for the template to be shown.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							esc_html_e(
								'Some reception templates are video enabled, meaning you can put a custom video to play or streaming from any source to run inside your reception template whilst your guests wait. ',
								'myvideoroom'
							);
							?>
						</p>
						<img alt="MyVideoRoom Guest View" src="<?php echo \esc_url( \plugins_url( '../img/guest-view.png', \realpath( __DIR__ . '/../' ) ) ); ?>" />

							</div>
						</div>
					</div>

	</article>
<!-- 
	Participant Template Section
-->	
	<article id="<?php echo \esc_attr( $html_lib->get_id( 'layouts' ) ); ?>">

	<!-- Module Header -->				
			<div class="myvideoroom-menu-settings">
					<div class="myvideoroom-header-table-left">
						<h1><i
								class="myvideoroom-header-dashicons dashicons-cover-image"></i><?php esc_html_e( 'Participant Video Templates', 'myvideoroom' ); ?>
						</h1>
					</div>
					<div class="myvideoroom-header-table-right">
					</div>
				</div>

	<!--  Description Marker -->
			<div class="myvideoroom-feature-outer-table">
					<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
						<h2><?php esc_html_e( 'Working with Templates', 'myvideoroom' ); ?></h2>
					</div>
					<div class="myvideoroom-feature-table-large">
						<p>
							<?php
							esc_html_e(
								'Templates are the visual representation of your room. They allow your guests to understand the type of meeting they are in. You can see a good representation of available templates for both reception, and video rooms, and reception templates tab. We are adding more templates all the time, and coming soon you will be able to make your own designs.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							\esc_html_e(
								'MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout areas, layouts and scenarios. The basis of a video meeting is to select a room template for your meeting, and use it to drag in guests from receptions you can also remove anyone from the meeting at any time by clicking on their × symbol next to their picture.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
							\esc_html_e(
								'We\'re currently working on functionality to enable you to upload your own room layouts and reception designs. We\'ll let you know when this feature is ready',
								'myvideoroom'
							);
							?>
						</p>
					</div>
				</div>	
	<!-- Participant Explorer -->

					<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
						class="mvr-nav-settingstabs-outer-wrap">
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Participant Template Explorer', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
								</h2>
								<p><?php esc_html_e( 'These are the currently available templates. They are automatically synchronised by MyVideoRoom to your site, and your drop down boxes for templates, and this window are always up to date with the latest templates.', 'myvideoroom' ); ?>
								</p>
			<ul>
			<?php
			foreach ( $available_layouts as $available_layout ) {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase - data from external source
				$seat_groups       = $available_layout->seatGroups;
				$total_seat_groups = \count( $seat_groups );
				$total_seats       = \array_sum(
					\array_map(
						fn( $seat_group ) => \count( $seat_group->seats ),
						$seat_groups
					)
				);

				?>
				<li class="card layout-card">
					<h3 class="title"><?php echo \esc_html( $available_layout->name ); ?></h3>
					<?php \esc_html_e( 'Slug: ', 'myvideoroom' ); ?> <em><?php echo \esc_html( $available_layout->slug ); ?></em>
					<br />

					<?php \esc_html_e( 'Seat Groups: ', 'myvideoroom' ); ?> <?php echo \esc_html( $total_seat_groups ); ?>
					<?php \esc_html_e( 'Seats: ', 'myvideoroom' ); ?> <?php echo \esc_html( $total_seats ); ?>
					<br />

					<img
						src="https://rooms.clubcloud.tech/layouts/<?php echo \esc_html( $available_layout->id . '/' . \str_replace( '.', '.thumb.', $available_layout->image ) ); ?>"
						alt="<?php echo \esc_html( $available_layout->name ); ?>"
					/>
				</li>
			<?php } ?>
		</ul>
							</div>
						</div>
	</article>
<!-- 
	Reception Template Section
-->	
	<article id="<?php echo \esc_attr( $html_lib->get_id( 'receptions' ) ); ?>">

	<!-- Module Header -->				
		<div class="myvideoroom-menu-settings">
			<div class="myvideoroom-header-table-left">
				<h1><i class="myvideoroom-header-dashicons dashicons-businessperson"></i><?php esc_html_e( 'Reception Video Templates', 'myvideoroom' ); ?>
				</h1>
			</div>
			<div class="myvideoroom-header-table-right">
			</div>
		</div>
	<!--  Description Marker -->
		<div class="myvideoroom-feature-outer-table">
				<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
					<h2><?php esc_html_e( 'Working with Templates', 'myvideoroom' ); ?></h2>
				</div>
				<div class="myvideoroom-feature-table-large">
					<p>
						<?php
						esc_html_e(
							'Templates are the visual representation of your receptions. They allow you to customise your experience for your guests, and coming soon you will be able to make your own reception designs.',
							'myvideoroom'
						);
						?>
					</p>
					<p>
						<?php
						\esc_html_e(
							'Reception templates are used to show your guest a waiting area before they are allowed to join a room. MyVideoRoom allows you to customise the layout, and also the video option of what you would like your guest to see whilst you wait. Below are currently, available reception templates. Not all templates can display video. Whilst your guest is waiting, they will be in the reception area. To begin the meeting you can drag their icon into a seating position in your room layout and your meeting will begin.',
							'myvideoroom'
						);
						?>
					</p>

				</div>
			</div>	
	<!-- Participant Explorer -->

	<div id="video-host-wrap_<?php echo esc_textarea( $index++ ); ?>"
						class="mvr-nav-settingstabs-outer-wrap">
						<div class="myvideoroom-feature-outer-table">
							<div id="feature-state<?php echo esc_attr( $index++ ); ?>"
								class="myvideoroom-feature-table-small">
								<h2><?php esc_html_e( 'Reception Template Explorer', 'myvideoroom' ); ?></h2>
							</div>
							<div class="myvideoroom-feature-table-large">
								</h2>
								<p><?php esc_html_e( 'These are the currently available reception templates. They are automatically synchronised by MyVideoRoom to your site, and your drop down boxes for templates, and this window are always up to date with the latest templates.', 'myvideoroom' ); ?>
								</p>
								<ul>
			<?php
			foreach ( $available_receptions as $available_reception ) {
				$has_video = \esc_html__( 'yes', 'myvideoroom' );

				if ( $available_reception->video ) {
					$has_video = \esc_html__( 'no', 'myvideoroom' );
				}

				?>
				<li class="card reception-card">
					<h3 class="title"><?php echo \esc_html( $available_reception->name ); ?></h3>
					<?php \esc_html_e( 'Slug: ', 'myvideoroom' ); ?> <em><?php echo \esc_html( $available_reception->slug ); ?></em>
					<br />

					<?php \esc_html_e( 'Video: ', 'myvideoroom' ); ?> <?php echo \esc_html( $has_video ); ?>
					<br />

					<img
						src="https://rooms.clubcloud.tech/receptions/<?php echo \esc_html( $available_reception->id . '/' . \str_replace( '.', '.thumb.', $available_reception->image ) ); ?>"
						alt="<?php echo \esc_html( $available_reception->name ); ?>"
					/>
				</li>
			<?php } ?>
		</ul>
							</div>
						</div>

	</article>
	<?php

	return \ob_get_clean();
};
