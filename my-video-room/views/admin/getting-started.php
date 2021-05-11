<?php
/**
 * Renders The Getting Started Area.
 *
 * @package MyVideoRoomPlugin\Views
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\ValueObject\GettingStarted;

/**
 * Render the getting started page
 *
 * @param GettingStarted $getting_started_steps Text to show the getting started steps
 */
return function (
	GettingStarted $getting_started_steps
): string {
	\ob_start();

	$html_lib = Factory::get_instance( HTML::class, array( 'room_builder' ) );

	?>
	<h2><?php echo \esc_html__( 'Getting started with MyVideoRoom', 'myvideoroom' ); ?></h2>
	<p>
		<?php
		\esc_html_e(
			'MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout areas, layouts and scenarios. The basis of a MyVideoRoom Meeting is to select a room template for your meeting, and use it to drag in guests from receptions. You can also remove anyone from the meeting at any time by clicking on the × symbol next to their picture.'
		);
		?>
	</p>

	<ol class="getting-started-steps">
		<?php
		foreach ( $getting_started_steps->get_steps() as $step ) {
			?>
			<li>
				<h4><?php echo \esc_html( $step->get_title() ); ?></h4>

				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
				echo $step->get_description();
				?>
			</li>
			<?php
		}

		?>
	</ol>

	<p>
		<?php
		\printf(
		/* translators: %s is the text "MyVideoRoom Pricing" and links to the https://clubcloud.tech/pricing */
			\esc_html__(
				'Visit %s for more information on purchasing an activation key to use MyVideoRoom.',
				'myvideoroom'
			),
			'<a href="https://clubcloud.tech/pricing">' .
			\esc_html__( 'MyVideoRoom pricing', 'myvideoroom' ) . '</a>'
		);
		?>
	</p>

	<form method="post" action="options.php">
		<?php
		if ( \get_option( Plugin::SETTING_PRIVATE_KEY ) ) {
			$submit_text = \esc_html__( 'Update', 'myvideoroom' );
			$placeholder = '∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗∗';
		} else {
			$submit_text = \esc_html__( 'Activate', 'myvideoroom' );
			$placeholder = \esc_html__( '(enter your activation key here)', 'myvideoroom' );
		}
		?>

		<?php \settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<label for="<?php echo \esc_attr( $html_lib->get_id( 'activation-key' ) ); ?>">
			<?php \esc_html_e( 'Your activation key', 'myvideoroom' ); ?>
		</label>
		<input
			class="activation-key"
			type="text"
			name="<?php echo \esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
			placeholder="<?php echo \esc_html( $placeholder ); ?>"
			id="<?php echo \esc_attr( $html_lib->get_id( 'activation-key' ) ); ?>"
		/>

		<?php
		\submit_button(
			\esc_html( $submit_text ),
			'primary',
			'submit',
			false
		);
		?>
	</form>


	<?php
	return \ob_get_clean();
};
