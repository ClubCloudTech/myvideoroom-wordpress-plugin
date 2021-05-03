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

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
	$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ?? 'my-video-room-global' ) );

	$pages = array(
		'my-video-room-global'      => __( 'General Settings', 'myvideoroom' ),
		'my-video-room-roombuilder' => __( 'Visual Room Builder', 'myvideoroom' ),
		'my-video-room-security'    => __( 'Video Security', 'myvideoroom' ),
		'my-video-room-templates'   => __( 'Room Templates', 'myvideoroom' ),
		'my-video-room'             => __( 'Shortcode Reference', 'myvideoroom' ),
		'my-video-room-helpgs'      => __( 'Help and Getting Started', 'myvideoroom' ),
	);

	?>
	<header>
		<h1 class="myvideoroom-header-config-title">
			<?php esc_html_e( 'My Video Room Settings and Configuration', 'myvideoroom' ); ?>
		</h1>
		<img src="<?php echo esc_url( plugins_url( '../img/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>" alt="My Video Room" />

		<ul>
			<?php
			foreach ( $messages as $message ) {
				echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
			}
			?>
		</ul>
	</header>

	<nav class="nav-tab-wrapper">
		<?php
		foreach ( $pages as $page_slug => $page_title ) {
			$class = 'nav-tab';

			if ( $current_page === $page_slug ) {
				$class .= ' nav-tab-active'; }

			?>
				<a class="<?php echo esc_attr( $class ); ?>" href="/wp-admin/admin.php?page=<?php echo esc_attr( $page_slug ); ?>">
				<?php echo esc_html( $page_title ); ?>
				</a>
			<?php
		}
		?>
	</nav>

	<?php
	return ob_get_clean();

};
