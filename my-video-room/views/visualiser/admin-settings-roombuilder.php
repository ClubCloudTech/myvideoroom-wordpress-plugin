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

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser as VisualiserShortcodeRoomVisualiser;

return function (
	string $active_tab,
	array $tabs,
	array $messages = array()
): string {

	wp_enqueue_script( 'frametab' );
	wp_enqueue_style( 'visualiser' );
	$render = require __DIR__ . '/header.php';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	echo $render( $active_tab, $tabs );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	ob_start();
	?>

<hr>
	<ul class="menu" >
		<a class="cc-menu-header" href="javascript:activateTab( 'page21' )">Visual Room Builder</a>
		<a class="cc-menu-header" href="javascript:activateTab( 'page24' )">Available Templates</a>
		<a class="cc-menu-header" href="javascript:activateTab( 'page22' )">Installed Shortcodes</a>
		<a class="cc-menu-header" href="javascript:activateTab( 'page23' )">All Shortcodes</a>
	</ul>
<br>
			<div id="tabCtrl" class="tabCtrl-header">
					<div id="page21" class ="cc-tab-items-block">
						<?php
						// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
						echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->visualiser_worker( 'Your Room' );
						?>
					</div>

					<div id="page22" class="cc-tab-items-none">
						<h2>Installed Shortcodes</h2>
						<p>This section shows only available shortcodes that are installed in active modules. To view all shortcodes please click on the View All Tab</p>
					</div>

					<div id="page23" class="cc-tab-items-none">
						<h2>All Shortcodes</h2>
						<p>This section shows all available shortcodes that are possible with the plugin in all modules. To view just installed shortcodes please click on the Installed Tab</p>
					</div>

					<div id="page24" class="cc-tab-items-none">
					<?php
					// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
					echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->display_room_template_browser();
					?>
					</div>
			</div> 
	<?php

	return ob_get_clean();
};

