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



use MyVideoRoomPlugin\ShortcodeVisualiser;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Setup\RoomAdmin;


return function (
	string $active_tab,
	array $tabs,
	array $messages = array()
): string {

	$render = require __DIR__ . '/header.php';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped. 
	echo $render( $active_tab, $tabs, $messages );
	ob_start();

	?>
	<script type="text/javascript">
	function activateTab(pageId) {
		var tabCtrl = document.getElementById( 'tabCtrl' );
		var pageToActivate = document.getElementById(pageId);
	for (var i = 0; i < tabCtrl.childNodes.length; i++) {
			var node = tabCtrl.childNodes[i];
			if (node.nodeType == 1) { /* Element */
				node.style.display = (node == pageToActivate) ? 'block' : 'none';
			}
		}
	}

	</script>

<hr>
		<ul class="menu" >

				<a class="cc-menu-header" href="javascript:activateTab( 'page1' )" style=" 
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


		<a class="cc-menu-header" href="javascript:activateTab( 'page4' )" style="
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

				<a class="cc-menu-header" href="javascript:activateTab( 'page2' )" style="
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

				<a class="cc-menu-header" href="javascript:activateTab( 'page3' )" style="
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

				<div id="page1" style="
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
					<h2>Room Builder Information</h2>


					<?php
					// phpcs:ignore -- Visualiser worker generates content and is output safely at its level. 
					echo Factory::get_instance( ShortcodeVisualiser::class )->visualiser_worker( SiteDefaults::USER_ID_SITE_DEFAULTS, 'Your Room' );
					?>

				</div>

				<div id="page2" style="
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

				<div id="page3" style="
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

			<div id="page4" style="
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

			</div>


	</div> 
	<?php

	return ob_get_clean();
};

