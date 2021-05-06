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

use MyVideoRoomPlugin\Admin;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Modules;

/**
 * Render the header
 *
 * @param array $pages The list of pages.
 * @param array $messages A list of messages to show.
 */
return function (
	array $pages,
	array $messages = array()
): string {
	ob_start();

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
	$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ?? 'my-video-room' ) );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
	$action = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );

	$module = '';
	if ( 'my-video-room-modules' === $current_page && Admin::MODULE_ACTION_DEACTIVATE !== $action ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$module = sanitize_text_field( wp_unslash( $_GET['module'] ?? '' ) );
	}

	?>

	<ul>
		<?php
		foreach ( $messages as $message ) {
			echo '<li class="notice ' . esc_attr( $message['type'] ) . ' is-dismissible"><p>' . esc_html( $message['message'] ) . '</p></li>';
		}
		?>
	</ul>

	<header>
		<div>
			<h1 class="myvideoroom-header-config-title">
				<?php esc_html_e( 'MyVideoRoom Settings and Configuration', 'myvideoroom' ); ?>
			</h1>

			<strong><?php echo esc_html__( 'Welcome to a world of interactive video', 'myvideoroom' ); ?></strong>
		</div>

		<aside class="card tag-line">
			<img src="<?php echo esc_url( plugins_url( '/img/screen-1.png', realpath( __DIR__ . '/../' ) ) ); ?>" alt="" />

			<p>
				<strong><?php esc_html_e( 'Video like you are there.' ); ?></strong><br />
				<br />
				<?php esc_html_e( 'MyVideoRoom by ClubCloud, video with themed rooms, made simple.' ); ?>
			</p>
		</aside>
	</header>

	<nav class="nav-tab-wrapper">
		<ul>
		<?php
		foreach ( $pages as $page_slug => $page_settings ) {
			$class = 'nav-tab';

			if ( $current_page === $page_slug && ! $module ) {
				$class .= ' nav-tab-active'; }

			?>
				<li>
					<a class="<?php echo esc_attr( $class ); ?>"
						href="<?php esc_url( menu_page_url( $page_slug ) ); ?>"
					>
					<?php
					if ( $page_settings['title_icon'] ?? false ) {
						echo '<span class="dashicons dashicons-' . esc_attr( $page_settings['title_icon'] ) . '"></span>';
					} else {
						echo esc_html( $page_settings['title'] );
					}
					?>
					</a>
				</li>
			<?php
		}

		if ( $module ) {

			$modules = Factory::get_instance( Modules::class )->get_modules();

			?>

			<li>
				<a class="nav-tab nav-tab-active nav-separate"
					href="<?php esc_url( menu_page_url( 'my-video-room-modules' ) ); ?>&module=<?php echo esc_html( $module ); ?>"
				>
					<?php echo esc_html( $modules[ $module ]->get_name() ); ?>
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
