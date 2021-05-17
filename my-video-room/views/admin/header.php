<?php
/**
 * Outputs the header for admin pages
 *
 * @package MyVideoRoomPlugin\Header
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Module\Module;
use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Render the header
 *
 * @param \MyVideoRoomPlugin\Admin\Page[] $pages             A list of pages to show in the admin menu.
 * @param Notice                          $activation_status The activation status message
 * @param string                          $current_page_slug The current page slug
 * @param ?Module                         $module            The currently selected module
 */
return function (
	array $pages,
	Notice $activation_status,
	string $current_page_slug,
	Module $module = null
): string {
	\ob_start();
	?>

	<header>
		<h1 class="myvideoroom-header-config-title">
			<?php \esc_html_e( 'MyVideoRoom Settings and Configuration', 'myvideoroom' ); ?>
		</h1>

		<div class="overview">
			<strong>
				<?php echo \esc_html__( 'Welcome to a world of interactive video', 'myvideoroom' ); ?>
			</strong>

			<em>
				<?php \esc_html_e( 'MyVideoRoom by ClubCloud, video with themed rooms, made simple.' ); ?>
			</em>

			<p class="notice notice-<?php echo \esc_attr( $activation_status->get_type() ); ?>">
				<?php echo \esc_html( $activation_status->get_message() ); ?>
			</p>
		</div>

		<img src="<?php echo \esc_url( \plugins_url( '/img/screen-1.png', \realpath( __DIR__ . '/../' ) ) ); ?>" alt="" />
	</header>

	<nav class="nav-tab-wrapper">
		<ul>
			<?php
			foreach ( $pages as $page_settings ) {
				$class = 'nav-tab';

				if ( $current_page_slug === $page_settings->get_slug() && ! $module ) {
					$class .= ' nav-tab-active';
				}

				?>
				<li>
					<a class="<?php echo \esc_attr( $class ); ?>"
						href="<?php \esc_url( \menu_page_url( $page_settings->get_slug() ) ); ?>"
					>
						<?php
						if ( $page_settings->get_icon() ?? false ) {
							?>
							<i class="dashicons dashicons-<?php echo \esc_attr( $page_settings->get_icon() ); ?>">
				<span class="screen-reader-text">
							<?php echo \esc_html( $page_settings->get_title() ); ?>
				</span>
							</i>
							<?php
						} else {
							echo \esc_html( $page_settings->get_title() );
						}
						?>
					</a>
				</li>
				<?php
			}

			if ( $module ) {
				$module_url = \add_query_arg(
					array( 'module' => $module->get_slug() ),
					\menu_page_url( PageList::PAGE_SLUG_MODULES, false )
				);

				?>

<<<<<<< HEAD
				<li>
					<a class="nav-tab nav-tab-active nav-separate" href="<?php \esc_url( $module_url ); ?>">
						<?php echo \esc_html( $module->get_name() ); ?>
					</a>
				</li>
=======
			<li>
				<a class="nav-tab nav-tab-active nav-separate" href="<?php esc_url( $module_url ); ?>">
				<?php echo esc_html( $module->get_name() ); ?>
				</a>
			</li>
>>>>>>> security integration and test fix

				<?php
			}

			?>
		</ul>
	</nav>

	<?php
	return \ob_get_clean();

};
