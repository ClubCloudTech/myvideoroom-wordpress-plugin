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
use MyVideoRoomPlugin\Visualiser\MyVideoRoomApp;
use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser as VisualiserShortcodeRoomVisualiser;

return function (
	string $active_tab,
	array $tabs,
	array $messages = array()
): string {
	
	wp_enqueue_script('frametab');
	$render = require __DIR__ . '/header.php';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	echo $render( $active_tab, $tabs, $messages );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	ob_start();

	?>

<hr>
		<ul class="menu" >

				<a class="cc-menu-header" href="javascript:activateTab( 'page21' )" style=" 
					display: block; 
					float: left;
					border: 1px solid #ccc;
					border-bottom: none;
					margin-left: .5em;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;
					">Visual Room Builder</a>


		<a class="cc-menu-header" href="javascript:activateTab( 'page24' )" style="
					display: block; 
					float: left;
					border: 1px solid #ccc;
					border-bottom: none;
					margin-left: .5em;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;
					">Available Templates</a>

				<a class="cc-menu-header" href="javascript:activateTab( 'page22' )" style="
					display: block; 
					float: left;
					border: 1px solid #ccc;
					border-bottom: none;
					margin-left: .5em;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;

					">Installed Shortcodes</a>

				<a class="cc-menu-header" href="javascript:activateTab( 'page23' )" style="
					display: block; 
					float: left;
					border: 1px solid #ccc;
					border-bottom: none;
					margin-left: .5em;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;
					">All Shortcodes</a>
		</ul>
<br>
			<div id="tabCtrl"
				style="
				background: #e0e0e0;  
				display: block;
				float: left;
				width: 95%;
				border: 1px solid #ccc;
				padding: 5px 10px;
				font-size: 14px;
				line-height: 1.71428571;
				font-weight: 600;
				background: #e5e5e5;
				color: #555;
				text-decoration: none;">

				<div id="page21" style="
					display: block; 
					float: left;
					border: 1px solid #ccc;
					width: 90%;
					margin: 20px 20px 20px 20px;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;">

					<?php
					// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
					echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->visualiser_worker( 'Your Room' );
					?>

				</div>

				<div id="page22" style="
					display: none; 
					float: left;
					border: 1px solid #ccc;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;
					">

					<h2>Installed Shortcodes</h2>
					<p>This section shows only available shortcodes that are installed in active modules. To view all shortcodes please click on the View All Tab</p>


				</div>

				<div id="page23" style="
					display: none; 
					float: left;
					border: 1px solid #ccc;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;
					">

				<h2>All Shortcodes</h2>
				<p>This section shows all available shortcodes that are possible with the plugin in all modules. To view just installed shortcodes please click on the Installed Tab</p>

			</div>

			<div id="page24" style="
					display: none; 
					float: left;
					width: inherit;
					border: 1px solid #ccc;
					padding: 5px 10px;
					font-size: 14px;
					line-height: 1.71428571;
					font-weight: 600;
					background: #e5e5e5;
					color: #555;
					text-decoration: none;
					">

					<?php
					// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
					echo Factory::get_instance( VisualiserShortcodeRoomVisualiser::class )->display_room_template_browser();
					?>

			</div>


	</div> 
	<?php

	return ob_get_clean();
};

