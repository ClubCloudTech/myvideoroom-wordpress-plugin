<?php
/**
 * Renders The Getting Started Area.
 *
 * @package MyVideoRoomPlugin\Views
 */

use MyVideoRoomPlugin\Plugin;

/**
 * Render the getting started page
 */
return function (): string {
	ob_start();

	?>
	<h2><?php echo esc_html__( 'Getting started with MyVideoRoom', 'myvideoroom' ); ?></h2>
	<p>
	<?php
		esc_html_e(
			'MyVideoRooms are more than just meetings. There are physical representations of real rooms with 
			breakout areas, layouts and scenarios. The basis of a MyVideoRoom Meeting is to select a room template for 
			your meeting, and use it to drag in guests from receptions. You can also remove anyone from the meeting at 
			any time by clicking on the × symbol next to their picture.'
		);
	?>
	</p>

	<ol class="three-step-getting-started">
		<li>
			<h4><?php echo esc_html__( 'Activate the plugin', 'myvideoroom' ); ?></h4>

			<?php
			printf(
				/* translators: %s is the text "MyVideoRoom by ClubCloud" and links to the MyVideoRoom Website */
				esc_html__(
					'Visit %s to Get Your License Key. Then enter your key below to activate your subscription',
					'myvideoroom'
				),
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
				esc_html__( 'Use the visual %s to plan your room interactively.', 'myvideoroom' ),
				'<a href="' . esc_url( menu_page_url( 'my-video-room-builder', false ) ) . '">' .
				esc_html__( 'room builder', 'myvideoroom' ) .
				'</a>'
			);
			?>
			<br>
			<?php
			printf(
				/* translators: %s is the text "templates" and links to the Template Section */
				esc_html__( 'Learn about using %s for video layouts and receptions to find something you like.', 'myvideoroom' ),
				'<a href="' . esc_url( menu_page_url( 'my-video-room-templates', false ) ) . '">' .
				esc_html__( 'templates', 'myvideoroom' ) .
				'</a>'
			);
			?>
		</li>

		<li>
			<h4><?php echo esc_html__( 'Set permissions', 'myvideoroom' ); ?></h4>

			<?php
			printf(
				/* translators: %s is the text "Room Permissions" and links to the Permissions Section */
				esc_html__( 'Visit the %s page to plan how you want to give access to your rooms.', 'myvideoroom' ),
				'<a href="' . esc_url( menu_page_url( 'my-video-room-permissions', false ) ) . '">' .
				esc_html__( 'room permission', 'myvideoroom' ) . '</a>'
			);
			?>
		</li>
	</ol>

	<p>
		<?php
		printf(
			/* translators: %s is the text "MyVideoRoom Pricing" and links to the https://clubcloud.tech/pricing */
			esc_html__(
				'Visit %s for more information on purchasing an activation key to use MyVideoRoom.',
				'myvideoroom'
			),
			'<a href="https://clubcloud.tech/pricing">' .
			esc_html__( 'MyVideoRoom pricing', 'myvideoroom' ) . '</a>'
		);
		?>
	</p>

	<form method="post" action="options.php">
		<?php
		if ( get_option( Plugin::SETTING_PRIVATE_KEY ) ) {
			$submit_text = __( 'Update', 'myvideoroom' );
			$placeholder = '∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗';
		} else {
			$submit_text = __( 'Activate', 'myvideoroom' );
			$placeholder = __( '(enter your activation key here)', 'myvideoroom' );
		}
		?>

		<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<label for="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>">
			<?php esc_html_e( 'Your activation key', 'myvideoroom' ); ?>
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
			esc_html( $submit_text ),
			'primary',
			'submit',
			false
		);
		?>
	</form>


	<?php
	return ob_get_clean();
};