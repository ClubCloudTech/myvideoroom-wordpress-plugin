<?php
/**
 * Renders The Help and Getting Started Area.
 *
 * @package MyVideoRoomPlugin\Views
 */

return function (): string {
	ob_start();

	?>
	<h2><?php echo esc_html__( 'Help / Getting Started ', 'myvideoroom' ); ?></h2>

	<strong><?php echo esc_html__( 'Welcome to a World of Interactive Video', 'myvideoroom' ); ?></strong>
	<p>
	<?php
		esc_html_e(
			'MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout
            areas, layouts and scenarios. The basis of a Video Meeting is to select a room template for your meeting, and use it to
            drag in guests from receptions you can also remove anyone from the meeting at any time by clicking on their 
            close meeting X by their picture.'
		);
	?>
	</p>

	<main>
		<img src="<?php echo esc_url( plugins_url( '/img/screen-1.png', realpath( __DIR__ . '/../' ) ) ); ?>" alt="" />

		<p>
			<strong><?php esc_html_e( 'Video like you are there' ); ?></strong>
		</p>

		<p>
			<?php esc_html_e( 'Introducing MyVideoRoom by ClubCloud, themed room based video made simple' ); ?>
		</p>
	</main>

	<ol class="three-step-getting-started">
		<li>
			<h4><?php echo esc_html__( 'Activate', 'myvideoroom' ); ?></h4>

			<?php
			printf(
				/* translators: %s is the text "MyVideoRoom by ClubCloud" and links to the MyVideoRoom Website */
				esc_html__( 'Visit %s to Get Your License Key.', 'myvideoroom' ),
				'<a href="https://clubcloud.tech/pricing/">' .
				esc_html__( 'MyVideoRoom by ClubCloud', 'myvideoroom' ) . '</a>'
			);
			?>
			<br />

			<?php
			printf(
				/* translators: %s is the text "Activate" and links to the Activation tab */
				esc_html__( '%s Your License Key.', 'myvideoroom' ),
				'<a href="/wp-admin/admin.php?page=my-video-room-global">' .
				esc_html__( 'Activate', 'myvideoroom' ) . '</a>'
			);
			?>
		</li>

		<li>
			<h4><?php echo esc_html__( 'Design Your Room', 'myvideoroom' ); ?></h4>

			<?php
			printf(
				/* translators: %s is the text "Visual Room Builder" and links to the Room Builder Section */
				esc_html__( 'Use the %s to plan your room interactively.', 'myvideoroom' ),
				'<a href="/wp-admin/admin.php?page=my-video-room-roombuilder">' .
					esc_html__( 'Visual Room Builder', 'myvideoroom' ) .
				'</a>'
			);
			?>
			<br>
			<?php
			printf(
				/* translators: %s is the text "Templates" and links to the Template Section */
				esc_html__( 'Learn about using %s for Video Rooms and Receptions to find something you like.', 'myvideoroom' ),
				'<a href="/wp-admin/admin.php?page=my-video-room-templates">' .
					esc_html__( 'Templates', 'myvideoroom' ) .
				'</a>'
			);
			?>
		</li>

		<li>
			<h4><?php echo esc_html__( 'Set Security', 'myvideoroom' ); ?></h4>

			<?php
			printf(
				/* translators: %s is the text "Video Security" and links to the Security Section */
				esc_html__( 'Visit the %s area to plan how you want to give access to your rooms.', 'myvideoroom' ),
				'<a href="/wp-admin/admin.php?page=my-video-room-security">' .
				esc_html__( 'Video Security', 'myvideoroom' ) . '</a>'
			);
			?>
		</li>
	</ol>

	<?php
	return ob_get_clean();
};
