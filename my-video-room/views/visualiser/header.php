<?php
/**
 * Outputs the header for admin pages
 *
 * @package MyVideoRoomPlugin\Header
 */

return function (
	array $messages = array()
): string {
	ob_start();
	wp_enqueue_script( 'mvr-frametab' );
	wp_enqueue_style( 'visualiser' );
	?>
	<table style="width:100%">
			<tr>
					<th class="mvr-visualiser-image-left" >
						<img
								src="<?php echo esc_url( plugins_url( '../img/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>"
								alt="My Video Room Extras"
								width="120"
								height="120"/>
					</th>
					<th class="mvr-visualiser-image">
						<h1><?php echo esc_html__( 'My Video Room Settings and Configuration', 'myvideoroom' ); ?></h1>
					</th>
			</tr>
	</table>
	<ul>
		<?php
		foreach ( $messages as $message ) {
			echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
		}
		?>
	</ul>
	<ul class="menu" >
		<a class="mvr-menu-header" href="/wp-admin/admin.php?page=my-video-room-global"><?php echo esc_html__( 'General Settings', 'myvideoroom' ); ?></a>
		<a class="mvr-menu-header" href="/wp-admin/admin.php?page=my-video-room-roombuilder"><?php echo esc_html__( 'Visual Room Builder', 'myvideoroom' ); ?></a>
		<a class="mvr-menu-header" href="/wp-admin/admin.php?page=my-video-room-security"><?php echo esc_html__( 'Video Security', 'myvideoroom' ); ?></a>
		<a class="mvr-menu-header" href="/wp-admin/admin.php?page=my-video-room-templates"><?php echo esc_html__( 'Room Templates', 'myvideoroom' ); ?></a>
		<a class="mvr-menu-header" href="/wp-admin/admin.php?page=my-video-room"><?php echo esc_html__( 'Shortcode Reference', 'myvideoroom' ); ?></a>
		<a class="mvr-menu-header" href="/wp-admin/admin.php?page=my-video-room-helpgs"><?php echo esc_html__( 'Help and Getting Started', 'myvideoroom' ); ?></a>
	</ul>
	<?php
	return ob_get_clean();
};


