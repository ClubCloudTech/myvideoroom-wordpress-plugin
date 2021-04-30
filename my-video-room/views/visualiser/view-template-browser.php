<?php
/**
 * Renders The Room Template Browser
 *
 * @package MyVideoRoomPlugin\Views
 */

return function (): string {
	ob_start();
	wp_enqueue_script( 'mvr-frametab' );
	wp_enqueue_style( 'visualiser' );
	wp_enqueue_style( 'visualiser-override', plugins_url( '../css/visualiser.css', __FILE__ ), array(), '2.1.11', 'all' );
	?>
		<div class="outer-box-wrap">
				<table style="width:100%">
				<tr>
					<th class="cc-header-table"><h1 class ="cc-heading-head-top">Help / Getting Started </h1>
					</th>
					<th class="cc-visualiser-image" >
						<img src="<?php echo esc_url( plugins_url( '../img/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>"
						alt="My Video Room Extras" width="90"	height="90"/>
					</th>
				</tr>
				</table>

			<ul style="display: flex;    justify-content: space-between;    width: 50%;">
				<a class="cc-sub-menu-header-block" href="javascript:activateTab2( 'page0' )" >Welcome to MyVideoRoom</a>
				<a class="cc-sub-menu-header-block" href="javascript:activateTab2( 'page1' )" >Video Room Templates</a>
				<a class="cc-sub-menu-header-block" href="javascript:activateTab2( 'page2' )" >Reception Templates</a>
				<a class="cc-sub-menu-header-block" href="javascript:activateTab2( 'page3' )" >Using Templates</a>
			</ul>

			<div id="tabCtrl2">
						<div id="page0" class="cc-sub-menu-header-block"><h2>Welcome to a World of Interactive Video</h2>
						<p>MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout areas, layouts 
						and scenarios. The basis of a Video Meeting is to select a room template for your meeting, and use it to drag in guests from receptions
						you can also remove anyone from the meeting at any time by clicking on their close meeting X by their picture. <b>coming soon</b> you will be able to make your own designs. </p>
						<img src="<?php echo esc_url( plugins_url( '../img/welcome.png', realpath( __DIR__ . '/' ) ) ); ?>">

						<table style="width:100%">
				<tr>
					<th class="cc-header-table-3"><h1 class ="cc-heading-head-top">Activate</h1>
					<img src="<?php echo esc_url( plugins_url( '../img/11.png', realpath( __DIR__ . '/' ) ) ); ?>"
					alt="Design Your Room" width="90"	height="90"/>
					</th>
					<th class="cc-header-table-3">
					<h1 class ="cc-heading-head-top">Design Your Room</h1>	
					<img src="<?php echo esc_url( plugins_url( '../img/21.png', realpath( __DIR__ . '/' ) ) ); ?>"
						alt="Design Your Room" width="90"	height="90"/>
					</th>
					<th class="cc-header-table-3">
					<h1 class ="cc-heading-head-top">Set Security</h1>
						<img src="<?php echo esc_url( plugins_url( '../img/31.png', realpath( __DIR__ . '/' ) ) ); ?>"
						alt="Set Security" width="90"	height="90"/>
					</th>
				</tr>
				<tr class="cc-td-head-top">
					<td>Visit MyVideoRoom by ClubCloud to <b>Get</b> Your License Key <a href="https://clubcloud.tech/pricing/">Here</a><br>
						Activate Your License Key <a href="javascript:activateTab( 'page25' )">Here<a><br><br>
					</td>
					<td>
						Use the <a href="javascript:activateTab( 'page21' )">Visual Room Builder</a> to Plan your Room Interactively.<br>
						Learn about using <a href="javascript:activateTab( 'page3' )">Templates </a> for <a href="javascript:activateTab( 'page1' )">Video Rooms </a>
						and <a href="javascript:activateTab( 'page2' )">Receptions </a> to find something you like.<br><br>
					</td>
					<td>
						Visit the  <a href="javascript:activateTab( 'page22' )">Video Security </a> area to plan how you want to give access to your rooms. <br>
						Explore the Advanced  <a href="javascript:activateTab( 'page23' )">Shortcode Documentation </a>  for Customisable Scenarios. 
					</td>
				</tr>
				</table>


						</div>

						<div id="page1" class="cc-sub-menu-header"><h2>Room Layouts</h2>
						<p>MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout areas, layouts 
						and scenarios. The basis of a Video Meeting is to select a room template for your meeting, and use it to drag in guests from receptions
						you can also remove anyone from the meeting at any time by clicking on their close meeting X by their picture. <b>coming soon</b> you will be able to make your own designs. </p>

						<iframe src="https://rooms.clubcloud.tech/views/layout?tag[]=basic&tag[]=premium&embed=tru" width="100%" height="1600px" frameborder="0" scrolling="yes" > </iframe>
						</div>

						<div id="page2" class="cc-sub-menu-header"><h2>Using Receptions</h2>
						Reception Templates are used to show your guest a waiting area before they are allowed to join a room. MyVideoRoom 
						allows you to customise the layout, and also the video option of what you would like your guest to see whilst you wait.
						Below are currently, available reception templates. Not all templates can display video. Whilst your guest is waiting, they
						will be in the reception area. To begin the meeting you can drag their icon into a seating position in your room layout and your
						meeting will begin.
						<iframe src="https://rooms.clubcloud.tech/views/reception?tag[]=basic&tag[]=premium&embed=true" width="100%" height="1600px" frameborder="0" scrolling="yes" > </iframe>

						</div>

						<div id="page3" class="cc-sub-menu-header"><h2>How to Use Templates</h2>
						<p>Templates are the visual representation of your room. They allow your guests to understand the type of meeting they are in. You can see
						a good representation of available templates for both Reception, and Video Rooms in the Room, and Reception Templates tab. We are adding
						more templates all the time, and <b>coming soon</b> you will be able to make your own designs. </p>
						<img src="<?php echo esc_url( plugins_url( '../../img/receptions.png', __FILE__ ) ); ?>"
						alt="My Video Room Extras" width="500" />
						<p>You can also select no template for your guests. This will mean MyVideoRoom will render a meeting much like other packages, with a reception 
						being turned on for your guest to wait in, whilst you arrive. You can select your reception template, and even put on a video stream for them whilst 
						they wait. </p>

						</div>
				</div>
	</div>				
	<?php

	return ob_get_clean();
};
