<?php
/**
 * Renders The Help/Getting Started Area.
 *
 * @package MyVideoRoomPlugin\Views
 */

use MyVideoRoomPlugin\Plugin;

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

	<ol class="three-step-getting-started">
		<li>
			<h4><?php echo esc_html__( 'Activate the plugin', 'myvideoroom' ); ?></h4>

			<?php
			printf(
			/* translators: %s is the text "MyVideoRoom by ClubCloud" and links to the MyVideoRoom Website */
				esc_html__( 'Visit %s to Get Your License Key. Then activate your subscription', 'myvideoroom' ),
				'<a href="https://clubcloud.tech/pricing/">' .
				esc_html__( 'MyVideoRoom by ClubCloud', 'myvideoroom' ) . '</a>'
			);
			?>
		</li>

		<li>
			<h4><?php echo esc_html__( 'Design your room', 'myvideoroom' ); ?></h4>

			<?php
			printf(
			/* translators: %s is the text "Visual Room Builder" and links to the Room Builder Section */
				esc_html__( 'Use the %s to plan your room interactively.', 'myvideoroom' ),
				'<a href="' . esc_url( menu_page_url( 'my-video-room-builder', false ) ) . '">' .
				esc_html__( 'Visual Room Builder', 'myvideoroom' ) .
				'</a>'
			);
			?>
			<br>
			<?php
			printf(
			/* translators: %s is the text "Templates" and links to the Template Section */
				esc_html__( 'Learn about using %s for Video Rooms and Receptions to find something you like.', 'myvideoroom' ),
				'<a href="' . esc_url( menu_page_url( 'my-video-room-templates', false ) ) . '">' .
				esc_html__( 'Templates', 'myvideoroom' ) .
				'</a>'
			);
			?>
		</li>

		<li>
			<h4><?php echo esc_html__( 'Set permissions', 'myvideoroom' ); ?></h4>

			<?php
			printf(
			/* translators: %s is the text "Room Permissions" and links to the Security Section */
				esc_html__( 'Visit the %s area to plan how you want to give access to your rooms.', 'myvideoroom' ),
				'<a href="' . esc_url( menu_page_url( 'my-video-room-permissions', false ) ) . '">' .
				esc_html__( 'Room Permission', 'myvideoroom' ) . '</a>'
			);
			?>
		</li>
	</ol>

	<p>
		<?php
		printf(
			/* translators: %s is the text "My Video Room Pricing" and links to the https://clubcloud.tech/pricing */
			esc_html__( 'To get started you will need an subscription to the MyVideoRoom service. See %s for more details', 'myvideoroom' ),
			'<a href="https://clubcloud.tech/pricing">' .
			esc_html__( 'My Video Room Pricing', 'myvideoroom' ) . '</a>'
		);
		?>
	</p>

	<form method="post" action="options.php">
		<?php
		if ( get_option( Plugin::SETTING_PRIVATE_KEY ) ) {
			$placeholder = __( '(hidden)', 'myvideoroom' );
		} else {
			$placeholder = __( '(Provided by ClubCloud)', 'myvideoroom' );
		}
		?>

		<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<label for="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>">
			<?php esc_html_e( 'My Video Room Activation Key', 'myvideoroom' ); ?>
		</label>
		<input
			class="activation-key"
			type="text"
			name="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
			value="<?php echo esc_attr( get_option( Plugin::SETTING_ACTIVATION_KEY ) ); ?>"
			placeholder="<?php echo esc_html( $placeholder ); ?>"
			id="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
		/>

		<?php
		submit_button(
			esc_html__( 'Activate', 'myvideoroom' ),
			'primary',
			'submit',
			false
		);
		?>
	</form>


	<?php
	return ob_get_clean();
};
