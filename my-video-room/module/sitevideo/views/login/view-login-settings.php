<?php
/**
 * Outputs the configuration settings for the login page
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Login\View-Login-settings.php
 */

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\LoginForm;


/**
 * Render the admin page
 *
 * @param ?string $details_section Optional details section.
 *
 * @return string
 */
return function (): string {

	ob_start();
	?>
<!-- Module Header -->
<div class="myvideoroom-menu-settings">
	<div class="myvideoroom-header-table-left">
		<h1><i
				class="myvideoroom-header-dashicons dashicons-admin-network"></i><?php esc_html_e( 'Login Tab Settings', 'myvideoroom' ); ?>
		</h1>
	</div>
	<div class="myvideoroom-header-table-right">
		<h3 class="myvideoroom-settings-offset"><i data-target=""
				class="myvideoroom-header-dashicons dashicons-admin-settings "
				title="<?php esc_html_e( 'Settings', 'myvideoroom' ); ?>"></i>
		</h3>
	</div>
</div>
<!-- Module State and Description Marker -->
<div class="myvideoroom-feature-outer-table">
	<div id="module-state" class="myvideoroom-feature-table-small">
		<h2><?php esc_html_e( 'What This Does', 'myvideoroom' ); ?></h2>
		<div id="parentmodule">

		</div>
	</div>
	<div class="myvideoroom-feature-table-large">
		<h2><?php esc_html_e( 'Customize Login Tabs', 'myvideoroom' ); ?></h2>
		<p style>
			<?php
			echo \sprintf(
			/* translators: %s is the text "Modules" and links to the Module Section */
				\esc_html__(
					'This setting defines the login tabs that will be shown to signed out users of %s rooms or other modules where a sign in box is shown to the user to allow them to login. You can use the default WordPress login system, or add a shortcode from your own Login solution or plugin.',
					'myvideoroom'
				),
				'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ) . '">' .
				\esc_html__( 'Room Manager', 'myvideoroom' ) .
				'</a>'
			)
			?>
		</p>

	</div>
</div>
<!-- How it Works Marker -->
<div class="myvideoroom-feature-outer-table">
	<div id="module-state" class="myvideoroom-feature-table-small">
		<h2><?php esc_html_e( 'How it Works', 'myvideoroom' ); ?></h2>
		<div id="parentmodule">

		</div>
	</div>
	<div class="myvideoroom-feature-table-large">
		<div class="mvr-flex">
			<div class="myvideoroom-table-split-left myvideoroom-split-padding ">
				<img class="" src="	<?php echo esc_url( plugins_url( '/../../../../img/login.png', __FILE__ ) ); ?>"
					alt="Powered by MyVideoRoom">
			</div>
			<div class="myvideoroom-table-split-right myvideoroom-split-padding">
				<p> <?php esc_html_e( 'The Welcome Center contains a login page for users, as do other pages like the personal video room reception. Use this setting to change what application handles the login event', 'myvideoroom' ); ?>
				</p>
				<p> <?php esc_html_e( 'Your site can use the default WordPress login experience or if you have your own plugin for authentication, you can put the shortcode for its login form here, and it will be applied in our login pages', 'myvideoroom' ); ?>
				</p>
			</div>
			<div class="myvideoroom-clear"></div>
		</div>

	</div>
</div>
<!-- Settings Area -->
<div class="myvideoroom-feature-outer-table">
	<div id="module-state" class="myvideoroom-feature-table-small">
		<h2><?php esc_html_e( 'Setting', 'myvideoroom' ); ?></h2>
		<div id="parentmodule">

		</div>
	</div>
	<div class="myvideoroom-feature-table-large">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --internally created sanitised function
		echo Factory::get_instance( LoginForm::class )->get_login_settings_page();
		?>

	</div>
</div>


	<?php

		return ob_get_clean();
};
