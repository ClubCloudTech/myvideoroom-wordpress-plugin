<?php
/**
 * Renders The Room Template Browser
 *
 * @package MyVideoRoomPlugin\Views
 */

declare( strict_types=1 );

/**
 * Show the available layouts and receptions
 *
 * @param array   $available_layouts    The list of available layouts.
 * @param array   $available_receptions The list of available receptions.
 * @param integer $id_index             A unique number to ensure uniqueness of ids.
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;

return function (
	array $available_layouts = array(),
	array $available_receptions = array()
): string {
	$html_lib = Factory::get_instance( HTML::class, array( 'template-browser' ) );

	ob_start();
	?>
		<h2><?php esc_html_e( 'Room template and reception design library', 'myvideoroom' ); ?></h2>

		<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
			<ul>
				<li>
					<a class="nav-tab nav-tab-active" href="#<?php echo esc_attr( $html_lib->get_id( 'usage' ) ); ?>">
						<?php esc_html_e( 'Using Templates', 'myvideoroom' ); ?>
					</a>
				</li>

				<li>
					<a class="nav-tab" href="#<?php echo esc_attr( $html_lib->get_id( 'layouts' ) ); ?>">
						<?php esc_html_e( 'Video Room Templates', 'myvideoroom' ); ?>
					</a>
				</li>

				<li>
					<a class="nav-tab" href="#<?php echo esc_attr( $html_lib->get_id( 'receptions' ) ); ?>">
						<?php esc_html_e( 'Reception Templates', 'myvideoroom' ); ?>
					</a>
				</li>
			</ul>
		</nav>

		<article id="<?php echo esc_attr( $html_lib->get_id( 'usage' ) ); ?>">
			<h2><?php echo esc_html__( 'How to use MyVideoRoom templates', 'myvideoroom' ); ?></h2>
			<p>
				<?php
				esc_html_e(
					'Templates are the visual representation of your room. They allow your guests to understand the type of meeting they are in. You can see a good representation of available templates for both reception, and video rooms, and reception templates tab. We are adding more templates all the time, and coming soon you will be able to make your own designs.',
					'myvideoroom'
				);
				?>
			</p>

			<div class="view">
				<h3><?php echo esc_html__( 'Host View', 'myvideoroom' ); ?></h3>
				<img src="<?php echo esc_url( plugins_url( '/img/host-view.png', realpath( __DIR__ . '/../' ) ) ); ?>" alt="MyVideoRoom Host View" />
			</div>

			<div class="view">
				<h3><?php echo esc_html__( 'Guest View', 'myvideoroom' ); ?></h3>
				<img src="<?php echo esc_url( plugins_url( '/img/guest-view.png', realpath( __DIR__ . '/../' ) ) ); ?>" alt="MyVideoRoom Guest View" />
			</div>

			<p>
				<?php
				esc_html_e(
					'You can also disable the layout for your guests. This will mean MyVideoRoom will render a meeting much like other packages, with a reception being turned on for your guest to wait in, whilst you arrive. You can select your reception template,	and even put on a video stream for them whilst they wait. ',
					'myvideoroom'
				);
				?>
			</p>
		</article>

		<article id="<?php echo esc_attr( $html_lib->get_id( 'layouts' ) ); ?>">
			<h2><?php echo esc_html__( 'Video room templates', 'myvideoroom' ); ?></h2>
			<p>
			<?php
			esc_html_e(
				'MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout areas, layouts and scenarios. The basis of a video meeting is to select a room template for your meeting, and use it to drag in guests from receptions you can also remove anyone from the meeting at any time by clicking on their × symbol next to their picture.',
				'myvideoroom'
			);
			?>
			</p>

			<p>
			<?php
			esc_html_e(
				'We\'re currently working on functionality to enable you to upload your own room layouts and reception designs. We\'ll let you know when this feature is ready',
				'myvideoroom'
			);
			?>
			</p>

			<ul>
			<?php
			foreach ( $available_layouts as $available_layout ) {
			    // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase - data from external source
				$seat_groups       = $available_layout->seatGroups;
				$total_seat_groups = count( $seat_groups );
				$total_seats       = array_sum(
					array_map(
						fn( $seat_group ) => count( $seat_group->seats ),
						$seat_groups
					)
				);

				?>
				<li class="card layout-card">
					<h3 class="title"><?php echo esc_html( $available_layout->name ); ?></h3>
					Slug: <em><?php echo esc_html( $available_layout->slug ); ?></em>
					<br />

					Seat Groups: <?php echo esc_html( $total_seat_groups ); ?>
					Seats: <?php echo esc_html( $total_seats ); ?>
					<br />

					<img
						src="https://rooms.clubcloud.tech/layouts/<?php echo esc_html( $available_layout->id . '/' . str_replace( '.', '.thumb.', $available_layout->image ) ); ?>"
						alt="<?php echo esc_html( $available_layout->name ); ?>"
					/>
				</li>
			<?php } ?>
			</ul>
		</article>

		<article id="<?php echo esc_attr( $html_lib->get_id( 'receptions' ) ); ?>">
			<h2><?php echo esc_html__( 'Using Receptions', 'myvideoroom' ); ?></h2>
			<p>
			<?php
			esc_html_e(
				'Reception templates are used to show your guest a waiting area before they are allowed to join a room. MyVideoRoom allows you to customise the layout, and also the video option of what you would like your guest to see whilst you wait. Below are currently, available reception templates. Not all templates can display video. Whilst your guest is waiting, they will be in the reception area. To begin the meeting you can drag their icon into a seating position in your room layout and your meeting will begin.',
				'myvideoroom'
			);
			?>
			</p>

			<ul>
				<?php
				foreach ( $available_receptions as $available_reception ) {
					$has_video = __( 'yes', 'myvideoroom' );

					if ( $available_reception->video ) {
						$has_video = __( 'no', 'myvideoroom' );
					}

					?>
					<li class="card reception-card">
						<h3 class="title"><?php echo esc_html( $available_reception->name ); ?></h3>
						Slug: <em><?php echo esc_html( $available_reception->slug ); ?></em>
						<br />

						Video: <?php echo esc_html( $has_video ); ?>
						<br />

						<img src="https://rooms.clubcloud.tech/receptions/<?php echo esc_html( $available_reception->id . '/' . str_replace( '.', '.thumb.', $available_reception->image ) ); ?>"
							alt="<?php echo esc_html( $available_reception->name ); ?>"
						/>
					</li>
				<?php } ?>
			</ul>

		</article>
	<?php

	return ob_get_clean();
};
