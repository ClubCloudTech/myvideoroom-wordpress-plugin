<?php
/**
 * Renders The Help and Getting Started Area.
 *
 * @package MyVideoRoomPlugin\Views
 */

return function (): string {
	ob_start();
	wp_enqueue_script( 'mvr-frametab' );
	wp_enqueue_style( 'visualiser' );
	?>

	<div class="mvr-outer-box-wrap">
		<table style="width:100%">
			<tr>
				<th class="mvr-header-table">
					<h1 class="mvr-heading-head-top"><?php echo esc_html__( 'Help / Getting Started ', 'myvideoroom' ); ?></h1>
				</th>

			</tr>
		</table>

		<ul class ="mvr-menu-ul">
			<a class="mvr-sub-menu-header-block" href="javascript:activateTab2( 'page0' )"><?php echo esc_html__( 'Welcome to MyVideoRoom', 'myvideoroom' ); ?></a>
		</ul>

		<div id="tabCtrl2">
			<div id="page0" class="mvr-tab-align">
				<h2><?php echo esc_html__( 'Welcome to a World of Interactive Video', 'myvideoroom' ); ?></h2>
				<p>
				<?php
				esc_html_e(
					'MyVideoRooms are more than just meetings. There are physical representations of real rooms with breakout
					areas, layouts and scenarios. The basis of a Video Meeting is to select a room template for your meeting, and use it to
					drag in guests from receptions you can also remove anyone from the meeting at any time by clicking on their 
					close meeting X by their picture.'
				);
				?>

				</p>
				<img src="<?php echo esc_url( plugins_url( '../img/welcome.png', realpath( __DIR__ . '/' ) ) ); ?>">

				<table style="width:100%">
					<tr>
						<th class="mvr-header-table-3">
							<h1 class="mvr-heading-head-top"><?php echo esc_html__( 'Activate', 'myvideoroom' ); ?></h1>
							<img src="<?php echo esc_url( plugins_url( '../img/11.png', realpath( __DIR__ . '/' ) ) ); ?>"
								alt="Design Your Room" width="90" height="90" />
						</th>
						<th class="mvr-header-table-3">
							<h1 class="mvr-heading-head-top"><?php echo esc_html__( 'Design Your Room', 'myvideoroom' ); ?></h1>
							<img src="<?php echo esc_url( plugins_url( '../img/21.png', realpath( __DIR__ . '/' ) ) ); ?>"
								alt="Design Your Room" width="90" height="90" />
						</th>
						<th class="mvr-header-table-3">
							<h1 class="mvr-heading-head-top"><?php echo esc_html__( 'Set Security', 'myvideoroom' ); ?></h1>
							<img src="<?php echo esc_url( plugins_url( '../img/31.png', realpath( __DIR__ . '/' ) ) ); ?>"
								alt="Set Security" width="90" height="90" />
						</th>
					</tr>
					<tr class="mvr-td-head-top">
						<td>
						<?php
						printf(   /* translators: %s is the text "Visual Room Builder" and links to the Room Builder Section */
							esc_html__( 'Visit MyVideoRoom by ClubCloud to Get Your License Key %s.', 'myvideoroom' ),
							'<a href="https://clubcloud.tech/pricing/">' .
							esc_html__( 'Here', 'myvideoroom' ) . '</a>'
						);
						?>
						<br>
						<?php
						printf(   /* translators: %s is the text "Visual Room Builder" and links to the Room Builder Section */
							esc_html__( 'Activate Your License Key %s.', 'myvideoroom' ),
							'<a href="/wp-admin/admin.php?page=my-video-room-global">' .
							esc_html__( 'Here', 'myvideoroom' ) . '</a>'
						);
						?>
						</td>	
						<td>
							<?php
							printf(   /* translators: %s is the text "Visual Room Builder" and links to the Room Builder Section */
								esc_html__( 'Use the %s to plan your room interactively.', 'myvideoroom' ),
								'<a href="/wp-admin/admin.php?page=my-video-room-roombuilder">' .
								esc_html__( 'Visual Room Builder', 'myvideoroom' ) . '</a>'
							);
							?>
						<br>
							<?php
							printf(   /* translators: %s is the text "Templates" and links to the Template Section */
								esc_html__( 'Learn about using %s for Video Rooms and Receptions to find something you like.', 'myvideoroom' ),
								'<a href="/wp-admin/admin.php?page=my-video-room-templates">' .
								esc_html__( 'Templates', 'myvideoroom' ) . '</a>'
							);
							?>
							</a> 
								<br><br>
						</td>
						<td>
							<?php
							printf(   /* translators: %s is the text "Video Security" and links to the Security Section */
								esc_html__( 'Visit the %s area to plan how you want to give access to your rooms.', 'myvideoroom' ),
								'<a href="/wp-admin/admin.php?page=my-video-room-security">' .
								esc_html__( 'Video Security', 'myvideoroom' ) . '</a>'
							);
							?>
							<br>

						</td>
					</tr>
				</table>


			</div>


		</div>
	</div>
	<?php

	return ob_get_clean();
};
