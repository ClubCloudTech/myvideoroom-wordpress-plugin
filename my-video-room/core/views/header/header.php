<?php
/**
 * Outputs the header for admin pages
 *
 * @package MyVideoRoomPlugin\Core\Views\header.php
 */

return function (
	array $messages = array()
): string {
	ob_start();
	wp_enqueue_script( 'myvideoroom-admin-tabs' );
	wp_enqueue_style( 'mvr-extras' );
	?>
	<div class="mvr-outer-box-wrap">
	<table style="width:100%">
		<tr>
			<th class="mvr-visualiser-image-left">
				<img src="<?php echo esc_url( plugins_url( '/../../../assets/img/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>"
					alt="My Video Room Extras" width="120" height="120" />
			</th>
			<th class="mvr-visualiser-image">
				<h1 class="mvr-header-config-title">
					<?php echo esc_html__( 'Video Module Settings', 'myvideoroom' ); ?></h1>
					Switch to : <a class="mvr-menu-header-item-switch"
			href="/wp-admin/admin.php?page=my-video-room"><?php echo esc_html__( 'Main Settings', 'myvideoroom' ); ?></a>
			</th>
		</tr>
		<tr>
			<td>
				<ul>
					<?php
					foreach ( $messages as $message ) {
						echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
					}
					?>
				</ul>
			</td>
		</tr>
	</table>
	<nav class="mvr-header-menu">
		<?php

		// Action Hook for Additional Tabs.
		do_action( 'mvr_module_submenu_add' );
		?>

	</nav>
</div>

	<ul>
		<?php
		foreach ( $messages as $message ) {
			echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
		}
		?>
	</ul>
	<?php
	return ob_get_clean();
};
