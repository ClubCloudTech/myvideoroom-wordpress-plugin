<?php
/**
 * Renders The Getting Started Area.
 *
 * @package MyVideoRoomPlugin\Views
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Admin\PageList;
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
	$index    = \wp_rand( 331, 434000 );
	$html_lib = Factory::get_instance( HTML::class, array( 'room_builder' ) );

	?>
<!-- Module Header -->
<div class="myvideoroom-menu-settings">
	<div class="myvideoroom-header-table-left-reduced">
		<img class="myvideoroom-logo-image"
			src="<?php echo esc_url( plugins_url( '../../img/mvr-imagelogo.png', __FILE__ ) ); ?>"
			alt="MyVideoroom Logo">
		<h1> <?php esc_html_e( 'Getting started', 'myvideoroom' ); ?>
		</h1>
	</div>
	<div class="myvideoroom-header-table-right-wide">
		<a href="<?php echo \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ); ?>">
			<i class="myvideoroom-header-dashicons dashicons-admin-plugins"></i></a>
	</div>
</div>

<div class="myvideoroom-feature-outer-table myvideoroom-clear">

	<div id="module-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
		<h2><?php esc_html_e( 'Welcome', 'myvideoroom' ); ?></h2>
		<div id="parentmodule<?php echo esc_attr( $index++ ); ?>">
		</div>
	</div>

	<div class="myvideoroom-feature-table-large">

		<?php
				esc_html_e(
					'Welcome to MyVideoRoom, your gateway to using the power of Video in any WordPress site, and to do amazing things. Our meetings are encrypted and secure, with everything under your complete control. We have built MVR to be modular and our team is working hard to integrate with the most popular plugins like Elementor, BuddyPress, and WooCommerce, with more being added.',
					'myvideoroom'
				);
		?>
		<br><br>
		<?php
				esc_html_e(
					'To get started, take a tour of our modules and plugins, and visit room manager to configure your meeting rooms, and their settings. It takes minutes to have the power of Video to your site and your users. Thank you for supporting us, we appreciate you taking the time to get to know our plugin.',
					'myvideoroom'
				);
		?>
		<br><br>
		<img class="" src="<?php echo esc_url( plugins_url( '/../../admin/img/mvrteam.jpg', __FILE__ ) ); ?>"
			alt="The Team" title="<?php esc_html_e( 'Thank you for Your Support', 'myvideoroom' ); ?>">

	</div>
</div>
<!-- License Key -->
<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
	<div class="myvideoroom-feature-outer-table">
		<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Your Key', 'myvideoroom' ); ?></h2>
		</div>
		<div class="myvideoroom-feature-table-large">
			<p>
				<?php
				\printf(
				/* translators: %s is the text "MyVideoRoom Pricing" and links to the https://clubcloud.tech/pricing */
					\esc_html__(
						'Visit %s for more information on purchasing an activation key to use MyVideoRoom, or your activation key will have been emailed to you after you ordered your subscription. Your Support matters to us, and helps us provide secure, encrypted dedicated video servers that understand your WordPress site and plugins.',
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
				<input class="activation-key" type="text"
					name="<?php echo \esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
					placeholder="<?php echo \esc_html( $placeholder ); ?>"
					id="<?php echo \esc_attr( $html_lib->get_id( 'activation-key' ) ); ?>" />

				<?php
				\submit_button(
					\esc_html( $submit_text ),
					'primary',
					'submit',
					false
				);
				?>
			</form>
		</div>
	</div>

	<!-- Getting Started -->
	<div id="video-host-wrap_<?php echo esc_attr( $index++ ); ?>" class="mvr-nav-settingstabs-outer-wrap">
		<div class="myvideoroom-feature-outer-table">
			<div id="feature-state<?php echo esc_attr( $index++ ); ?>" class="myvideoroom-feature-table-small">
				<h2><?php esc_html_e( 'Getting Started', 'myvideoroom' ); ?></h2>
			</div>
			<div class="myvideoroom-feature-table-large">
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
				<img src="<?php echo esc_url( plugins_url( '../../img/key3.png', __FILE__ ) ); ?>" id="intro-imgs"
					width=15% style="padding:0px 60px 0px 60px; max-width:145px;" />
				<img src="<?php echo esc_url( plugins_url( '../../img/modules3.png', __FILE__ ) ); ?>" id="intro-imgs"
					width=15% style="padding:0px 60px 0px 60px; max-width:145px; " />
				<img src="<?php echo esc_url( plugins_url( '../../img/start-video2.png', __FILE__ ) ); ?>"
					id="intro-imgs" width=15% style="padding:0px 60px 0px 60px; max-width:145px; " />
				<style>
				@media (max-width: 710px) {
					#intro-imgs {
						display: none;
					}
				}
				</style>

			</div>
		</div>

		<?php
		return \ob_get_clean();
};
