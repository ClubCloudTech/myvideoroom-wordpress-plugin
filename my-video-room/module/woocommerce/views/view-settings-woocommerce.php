<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Module\Security\Views\view-settings-woocommerce.php
 */

/**
 * Render the admin page for WooCommerce
 *
 * @param string $active_tab
 * @param array  $tabs
 * @param array  $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Module\Security\Security;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

return function () {
	ob_start();

	?>
	<div id="outer" class="mvr-admin-page-wrap">
	<h1>WooCommerce Integration</h1>
	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
		<ul>
			<li>
				<a class="nav-tab nav-tab-active" href="#defaultperms">
					<?php esc_html_e( 'Default Permissions', 'my-video-room' ); ?>
				</a>
			</li>

			<li>
				<a class="nav-tab" href="#basketintegration">
					<?php esc_html_e( 'Basket Integration', 'my-video-room' ); ?>
				</a>
			</li>

		</ul>
	</nav>
	<br><br>
	<div id="video-host-wrap" class="mvr-admin-page-wrap">
		<article id="defaultperms">
			<p>
				<?php
				esc_html_e(
					'This screen lets you configure WooCommerce Integration options to MyVideoRoom. You can configure store and basket integration options below.',
					'my-video-room'
				);
				?>
			</p>

		</article>
		<article id="basketintegration">
			<br>
			<?php
			esc_html_e(
				'This panel controls all basket integration setting allowing assisted in-call buying',
				'my-video-room'
			);
			?>
			<br><br>
			<?php Factory::get_instance( ModuleConfig::class )->module_activation_button( WooCommerce::MODULE_WOOCOMMERCE_BASKET_ID ); ?>
		</article>
	</div>

	<?php
	return ob_get_clean();
};
