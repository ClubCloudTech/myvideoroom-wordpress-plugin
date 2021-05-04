<?php
/**
 * Outputs the header for admin pages
 *
 * @package MyVideoRoomPlugin\Header
 */

/**
 * Render the admin header
 *
 * @param array $pages    A list of pages to show in the admin menu. Takes the form: slug => [title=:string, callback=:callback][]
 * @param array $messages An list of messages to show. Takes the form: [type=:string, message=:string][]
 */
return function (
	array $pages,
	array $messages = array()
): string {
	ob_start();

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
	$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ?? 'my-video-room-global' ) );

	?>

	<ul>
		<?php
		foreach ( $messages as $message ) {
			echo '<li class="notice ' . esc_attr( $message['type'] ) . ' is-dismissible"><p>' . esc_html( $message['message'] ) . '</p></li>';
		}
		?>
	</ul>

	<header>
		<h1 class="myvideoroom-header-config-title">
			<?php esc_html_e( 'My Video Room Settings and Configuration', 'myvideoroom' ); ?>
		</h1>
		<img src="<?php echo esc_url( plugins_url( '/img/logo.png', realpath( __DIR__ . '/' ) ) ); ?>" alt="My Video Room" />
	</header>

	<nav class="nav-tab-wrapper">
		<ul>
		<?php
		foreach ( $pages as $page_slug => $page_settings ) {
			$class = 'nav-tab';

			if ( $current_page === $page_slug ) {
				$class .= ' nav-tab-active'; }

			?>
				<li>
					<a class="<?php echo esc_attr( $class ); ?>" href="/wp-admin/admin.php?page=<?php echo esc_attr( $page_slug ); ?>">
					<?php echo esc_html( $page_settings['title'] ); ?>
					</a>
				</li>
			<?php
		}
		?>
		</ul>
	</nav>

	<?php
	return ob_get_clean();

};
