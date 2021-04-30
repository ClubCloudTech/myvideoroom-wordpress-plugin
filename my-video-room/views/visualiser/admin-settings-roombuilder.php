<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Views\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Admin;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser as VisualiserShortcodeRoomVisualiser;
use MyVideoRoomPlugin\Visualiser\VisualiserHelpers;

return function (
	string $active_tab,
	array $tabs,
	array $messages = array()
): string {

	wp_enqueue_script( 'mvr-frametab' );
	wp_enqueue_style( 'visualiser' );
	$render = require __DIR__ . '/header.php';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	echo $render( $active_tab, $tabs );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	ob_start();
	?>

<hr>
	<ul class="menu" >
	<a class="cc-menu-header" href="javascript:activateTab( 'page24' )">Help & Getting Started</a>	
	<a class="cc-menu-header" href="javascript:activateTab( 'page21' )">Visual Room Builder</a>
	<a class="cc-menu-header" href="javascript:activateTab( 'page22' )">Video Security Level</a>
	<a class="cc-menu-header" href="javascript:activateTab( 'page25' )">Licensing</a>
	<a class="cc-menu-header" href="javascript:activateTab( 'page23' )">Detailed Shortcode Reference</a>
	</ul>
<br>
			<div id="tabCtrl" class="tabCtrl-header">
					<div id="page21" class ="cc-tab-items-none">
						<a name="visualiser"></a>
						<?php
						// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
						echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->visualiser_worker( 'Your Room' );
						?>
					</div>

					<div id="page22" class="cc-tab-items-none">
						<div class="outer-box-wrap">
						<h1>Default Security Level</h1>
						<p>By default you have two shortcodes generated for your pages by the room builder. One is for the Host, and one for guest.
							This setting configures, who the Video Engine will treat as a Host in case you haven't provided a Host Shortcode. 
							By default, the Application will take <?php echo esc_html( Factory::get_instance( VisualiserHelpers::class )->name_format( get_bloginfo( 'name' ) ) ); ?>
							Administrators Group as its default host. You can override that section below, and add additional WordPress roles to your Host permissions Matrix. </p>

							<p>More advanced Security is Available from the MyVideoRoom Extras plugin.</p>

							<?php
							// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself. 
							echo Factory::get_instance(Admin::class)->create_settings_admin_page();
							?>
						</p>
						</div>
					</div>

					<div id="page23" class="cc-tab-items-none">
						<h2>Complete Information about Shortcodes for Advanced Users</h2>
						<p>The MyVideoRoom platform works entirely on the generation of Host or Guest Shortcodes, (or sometimes combined for both cases). The following 
							section allows you to see the parameters in Shortcode design to plan your custom and advanced Video deployments.
						</p>
						<?php
						$render = require __DIR__ . '/../admin-reference.php';
					// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself. 
							echo $render();
						?>

					</div>

					<div id="page24" class="cc-tab-items-block">
					<?php
					$render = require __DIR__ . '/view-template-browser.php';
					// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself. 
					echo $render();
					?>
					</div>
					<div id="page25" class="cc-tab-items-none">
					<a id="license"></a>
					<div class="outer-box-wrap">	
					<h1>Licensing and Activation</h1>
						<p>The MyVideoRoom plugin requires an active account on the MyVideoRoom service to enable the video functionality. You can get your license
							key <a href="https://myvideoroom.net/join-us/"> Here </a>. The service is a reliable, dedicated encrypted platform that never shares 
							any data with anyone. The service allows for unlimited users to share the Video platform on one account, and is limited only by how
							many users you want online at a time (concurrently).
						</p>
						<?php
						// phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped  - Ignored as function does escaping in itself. 
						echo Factory::get_instance(Admin::class)->create_admin_page();
						?>
						</div>
					</div>
			</div> 
	<?php

	return ob_get_clean();
};

