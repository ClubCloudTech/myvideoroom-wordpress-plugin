<?php
/**
 * Renders The Room Template Browser
 *
 * @package MyVideoRoomPlugin\Views
 */

return function (): string {
	ob_start();
	wp_enqueue_script( 'frametab' );
	wp_enqueue_style( 'visualiser' );
	?>
		<div class="outer-box-wrap">
		<table style="width:100%">
				<tr>
					<th style="width:80%" ><h1 class ="cc-heading-head-top">Room Template Browser </h1>
					</th>
					<th class="cc-visualiser-image" >
						<img src="<?php echo esc_url( plugins_url( '../img/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>"
						alt="My Video Room Extras" width="90"	height="90"/>
					</th>
				</tr>
				</table>
		
			<ul class="menu" style="display: flex;    justify-content: space-between;    width: 50%;">
				<a class="cc-menu-header" href="javascript:activateTab2( 'page1' )" >Video Room Templates</a>
				<a class="cc-menu-header" href="javascript:activateTab2( 'page2' )" >Reception Templates</a>
				<a class="cc-menu-header" href="javascript:activateTab2( 'page3' )" >Using Templates</a>
		</ul>

		<div id="tabCtrl2">
			<div id="page1" style="display: block; "><iframe src="https://rooms.clubcloud.tech/views/layout?tag[]=basic&tag[]=premium&embed=tru" width="100%" height="1600px" frameborder="0" scrolling="yes" > </iframe>
			</div>

			<div id="page2" style="display: none;"><iframe src="https://rooms.clubcloud.tech/views/reception?tag[]=basic&tag[]=premium&embed=true" width="100%" height="1600px" frameborder="0" scrolling="yes" > </iframe>
			</div>

			<div id="page3" style="display: none;">
				<h1>How to Use Templates</h1>
				<p> Templates can be used as arguments into any shortcode you build manually with [clubvideo], or in drop down boxes of Menus of Club Cloud Video Extras</p>
			</div>
		</div>
	</div>

	<?php

	return ob_get_clean();
};
