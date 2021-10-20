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
	<div class="myvideoroom-menu-settings-header ">
		<div class="myvideoroom-header-table-left-reduced">
			<h1 class="myvideoroom-separation-header"><i
					class="myvideoroom-header-dashicons dashicons-admin-settings"></i><?php esc_html_e( 'MyVideoRoom Plugin Settings', 'myvideoroom' ); ?>
			</h1><br>
		</div>
		<div class="myvideoroom-header-table-right-wide">
		<h3 class="myvideoroom-settings-offset">
			<img style=" margin-top: -10px;" class="myvideoroom-logo-image" src="<?php echo esc_url( plugins_url( '../../img/mvr-imagelogo.png', __FILE__ ) ); ?>" alt="MyVideoroom Logo">
		</h3>
		
		</div>
	</div>
	<div class="">
		<div class="myvideoroom-header-table-left">
			<div class="overview">
				<strong>
					<?php \esc_html_e( 'Welcome to a world of interactive video', 'myvideoroom' ); ?>
				</strong>

				<em>
					<?php \esc_html_e( 'MyVideoRoom by ClubCloud, video with themed rooms, made simple.', 'myvideoroom' ); ?>
				</em>
				<div>
					<p class="notice notice-<?php echo \esc_attr( $activation_status->get_type() ); ?>">
						<?php echo \esc_html( $activation_status->get_message() ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="myvideoroom-header-table-right">
			<img class=""
				src="<?php echo \esc_url( \plugins_url( '/img/screen-1.png', \realpath( __DIR__ . '/../' ) ) ); ?>"
				alt="" />
		</div>
	</div>

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
				href="<?php \esc_url( \menu_page_url( $page_settings->get_slug() ) ); ?>">
				<?php
				if ( $page_settings->get_icon() ?? false ) {
					?>
				<i class="myvideoroom-dashicons dashicons-<?php echo \esc_attr( $page_settings->get_icon() ); ?>">
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

		<li>
			<a class="nav-tab nav-tab-active nav-separate" href="<?php \esc_url( $module_url ); ?>">
				<?php echo \esc_html( $module->get_name() ); ?>
			</a>
		</li>

			<?php
		}

		?>
	</ul>
</nav>

	<?php
	return \ob_get_clean();

};
