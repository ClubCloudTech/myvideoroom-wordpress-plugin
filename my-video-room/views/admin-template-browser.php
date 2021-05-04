<?php
/**
 * Renders The Room Template Browser
 *
 * @package MyVideoRoomPlugin\Views
 */

return function (): string {
	ob_start();
	?>
		<h2><?php esc_html_e( 'Room Template Library', 'myvideoroom' ); ?></h2>

		<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
			<ul>
				<li>
					<a class="nav-tab nav-tab-active" href="#myvideoroom-how-to-use-templates">
						<?php esc_html_e( 'Using Templates', 'myvideoroom' ); ?>
					</a>
				</li>

				<li>
					<a class="nav-tab" href="#myvideoroom-layouts">
						<?php esc_html_e( 'Video Room Templates', 'myvideoroom' ); ?>
					</a>
				</li>

				<li>
					<a class="nav-tab" href="#myvideoroom-receptions">
						<?php esc_html_e( 'Reception Templates', 'myvideoroom' ); ?>
					</a>
				</li>
			</ul>
		</nav>

		<article id="myvideoroom-how-to-use-templates">
			<h2><?php echo esc_html__( 'How to Use Templates', 'myvideoroom' ); ?></h2>
			<p>
				<?php
				esc_html_e(
					'Templates are the visual representation of your room. They allow your guests to understand the type of
                meeting they are in. You can see a good representation of available templates for both Reception, and Video Rooms in the Room, and
                Reception Templates tab. We are adding more templates all the time, and coming soon you will be 
                able to make your own designs.  '
				);
				?>
			</p>

			<div class="view">
				<h3>Host View</h3>
				<img src="<?php echo esc_url( plugins_url( '/img/host-view.png', realpath( __DIR__ . '/' ) ) ); ?>" alt="My Video Room Host View" />
			</div>

			<div class="view">
				<h3>Guest View</h3>
				<img src="<?php echo esc_url( plugins_url( '/img/guest-view.png', realpath( __DIR__ . '/' ) ) ); ?>" alt="My Video Room Guest View" />
			</div>

			<p>
				<?php
				esc_html_e(
					'You can also select no template for your guests. This will mean MyVideoRoom will render a meeting much
                like other packages, with a reception being turned on for your guest to wait in, whilst you arrive. You 
                can select your reception template,	and even put on a video stream for them whilst they wait. '
				);
				?>
			</p>
		</article>

		<article id="myvideoroom-layouts">
			<h2><?php echo esc_html__( 'Video Room Templates', 'myvideoroom' ); ?></h2>
			<p>
			<?php
			esc_html_e(
				'MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout
                areas, layouts and scenarios. The basis of a Video Meeting is to select a room template for your meeting,
                 and use it to drag in guests from receptions you can also remove anyone from the meeting at any time by
                 clicking on their close meeting X by their	picture. Coming soon you will be able to make your own designs.'
			);
			?>
			</p>

			<iframe class="myvideoroom-admin-template-browser" src="https://rooms.clubcloud.tech/views/layout?embed=true"></iframe>
		</article>

		<article id="myvideoroom-receptions">
			<h2><?php echo esc_html__( 'Using Receptions', 'myvideoroom' ); ?></h2>
			<p>
			<?php
			esc_html_e(
				'Reception Templates are used to show your guest a waiting area before they are allowed to join a room.
                MyVideoRoom	allows you to customise the layout, and also the video option of what you would like your guest to see
                whilst you wait. Below are currently, available reception templates. Not all templates can display video. Whilst your guest
                is waiting, they will be in the reception area. To begin the meeting you can drag their icon into a seating position in your
                room layout and your meeting will begin.'
			);
			?>
			</p>

			<iframe class="myvideoroom-admin-template-browser" src="https://rooms.clubcloud.tech/views/reception?embed=true"></iframe>
		</article>
	<?php

	return ob_get_clean();
};
