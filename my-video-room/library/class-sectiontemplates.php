<?php
/**
 * Display section templates
 *
 * @package MyVideoRoomPlugin\Library\SectionTemplates.php
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\DAO\SessionState;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class SectionTemplate
 */
class SectionTemplates {

	const TAB_VIDEO_ROOM          = 'Video Room';
	const TAB_VIDEO_ROOM_SETTINGS = 'Video Room Settings';
	const TAB_ROOM_PERMISSIONS    = 'Room Permissions';
	const TAB_STOREFRONT          = 'Storefront';
	const TAB_SHOPPING_BASKET     = 'Shopping Basket';
	const TAB_INFO_WELCOME        = 'Welcome';
	const TAB_INFO_RECEPTION      = 'Site Reception Centre';
	const TAB_HOST_ROOM_SETTINGS  = 'Room Hosts';
	const BUTTON_REFRESH          = 'Refresh';
	const BUTTON_SHARE_BASKET     = 'Share Basket';
	const BUTTON_REQUEST_BASKET   = 'Request Basket';


	/**
	 * Render a Template to Automatically Wrap the Video Shortcode with additional tabs to add more functionality
	 *  Used to add Admin Page for each Room for Hosts, Returns Header and Shortcode if no additional pages passed in
	 *
	 * @param Array|string $header       The Header of the Shortcode.
	 * @param array        $inbound_tabs Inbound object with tabs.
	 * @param ?int         $user_id      User ID for passing to other Filters.
	 * @param ?string      $room_name    Room Name for passing to other Filters.
	 * @param bool         $host_status  Whether user is a host.
	 *
	 * @return string The completed Formatted Template.
	 */
	public function shortcode_template_wrapper( $header, array $inbound_tabs, int $user_id = null, string $room_name = null, bool $host_status = null ): string {
		Factory::get_instance( SessionState::class )->register_room_presence( $room_name, $host_status, $user_id );

		ob_start();
		// Randomizing Pages by Header to avoid page name conflicts if multiple frames.
		$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
		$tabs         = apply_filters( 'myvideoroom_main_template_render', $inbound_tabs, $user_id, $room_name, $host_status, $header );

		?>

<div class="mvr-nav-shortcode-outer-wrap" style="max-width: 1250px;">
	<div id="roominfo" 
	data-room-name="<?php echo esc_attr( $room_name ); ?>"
	data-logged-in="<?php echo esc_attr( is_user_logged_in() ); ?>"
	>
	</div>
	<div class="mvr-header-section">
		<div id="mvr-notification-icons" class="myvideoroom-header-table-left">
			<?php //phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
				echo $header['template_icons'];
			?>
		</div>
		<div id="mvr-header-table-right" class="myvideoroom-header-table-right">
			<p class="mvr-header-title mvr-header-align">
				<?php //phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
							echo $header['name_output']. ' ' . $header['module_name'];
				?>
			</p>
		</div>

	</div>
	<div id="mvr-notification-master" class="mvr-nav-shortcode-outer-wrap-clean mvr-notification-master">
		<?php
							$output = \apply_filters( 'myvideoroom_notification_master', '', $room_name );
							// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
							//echo $output;
		?>
		<div id="mvr-postbutton-notification" class="mvr-notification-align"></div>
	</div>

	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper myvideoroom-side-tab">
		<ul class="mvr-ul-style-side-menu">
			<?php
					$active = ' nav-tab-active';

			foreach ( $tabs as $menu_output ) {
				$tab_display_name = $menu_output->get_tab_display_name();
				$tab_slug         = $menu_output->get_tab_slug();
				$object_id        = $menu_output->get_element_id();
				?>
			<li>
				<a class="nav-tab<?php echo esc_attr( $active ); ?>" 
											<?php
											if ( $object_id ) {
												echo 'id = "' . esc_attr( $object_id ) . '" ';
											}
											?>
											href="#<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
					<?php
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon is created by escaped function.
					echo $tab_display_name;
					?>
				</a>
			</li>

				<?php
				$active = null;
			}
			?>
	</ul>
	</nav>
	<div id="mvr-above-article-notification"></div>

			<?php
			foreach ( $tabs as $article_output ) {

				$function_callback = $article_output->get_function_callback();
				$tab_slug          = $article_output->get_tab_slug();
				?>
		<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>" class="myvideoroom-content-tab mvr-article-separation">
					<?php

					if ( WooCommerce::SETTING_SHOPPING_BASKET !== $tab_slug ) {
					// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
						echo $function_callback;
					}

					?>
		</article>
					<?php
					if ( WooCommerce::SETTING_SHOPPING_BASKET === $tab_slug ) {
						?>
		<article id="<?php echo \esc_textarea( WooCommerce::SETTING_SHOPPING_BASKET ); ?>" class="mvr-article-separation">
						<?php
						// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
						echo $function_callback; 
						?>

		</article>
						<?php
					}
			}
			?>
</div>
			<?php
			return \ob_get_clean();
	}

	/**
	 * Returns Icons for Template Menus. (Horizontal or Vertical Menus)
	 *
	 * @param string $input_type The type of Icon to return.
	 * @return string
	 */
	public function template_icon_switch( string $input_type ) {

		switch ( $input_type ) {
			case self::TAB_VIDEO_ROOM:
				return '<span title ="' . esc_html__( 'Video Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-video-alt3"></span>';
			case self::TAB_VIDEO_ROOM_SETTINGS:
				return '<span title ="' . esc_html__( 'Room Reception, and Layout Settings', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-admin-generic"></span>';
			case self::TAB_ROOM_PERMISSIONS:
				return '<span title ="' . esc_html__( 'Room Security and Permissions', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-lock"></span>';
			case self::TAB_STOREFRONT:
				return '<span title ="' . esc_html__( 'Room Builtin Storefront', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-store"></span>';
			case self::TAB_SHOPPING_BASKET:
				return '<span title ="' . esc_html__( 'Your Shopping Basket with AutoSync to Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-cart"></span>';
			case self::TAB_INFO_WELCOME:
				return '<span title ="' . esc_html__( 'Welcome and Information', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-nametag"></span>';
			case self::TAB_INFO_RECEPTION:
				return '<span title ="' . esc_html__( 'Main Room Reception Centre', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-clipboard"></span>';
			case self::TAB_HOST_ROOM_SETTINGS:
				return '<span title ="' . esc_html__( 'Control Room Hosts', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-businessman"></span>';
			case self::BUTTON_REFRESH:
				return '<span title ="' . esc_html__( 'Reconnect to Basket', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-update-alt" ></span>';
			case self::BUTTON_SHARE_BASKET:
				return '<span title ="' . esc_html__( 'Share Your Basket with the Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-share-alt2"></span><span title ="' . esc_html__( 'Share Your Basket with the Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-cart"></span>';
			case self::BUTTON_REQUEST_BASKET:
				return '<span title ="' . esc_html__( 'Request Control of the Room Basket', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-format-status"></span><span title ="' . esc_html__( 'Request Control of the Room Basket', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-cart"></span>';
			case 'accept':
				return '<span title ="' . esc_html__( 'Accept', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-thumbs-up"></span>';
			case 'reject':
				return '<span title ="' . esc_html__( 'Reject', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-thumbs-down"></span>';
		}
	}
	/**
	 * Welcome Template.
	 *
	 * @return string
	 */
	public function welcome_template(): string {

		ob_start();

		?>

<div class="mvr-nav-settingstabs-outer-wrap myvideoroom-welcome-page ">

		<?php
				//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( MVRSiteVideoViews::class )->render_picture_page();
		?>
	<div class="mvr-flex">
		<div class="mvr-powered-by mvr-clear mvr-left">
			<a href="https://clubcloud.tech"
				title="<?php echo esc_html_e( 'Get MyVideoRoom for your website', 'myvideoroom' ); ?>" target="_blank">
				<img class="myvideoroom-product-image" src="
				<?php echo esc_url( plugins_url( '/../img/mvr-imagelogo.png', __FILE__ ) ); ?>" alt="Powered by MyVideoRoom">
			</a>
		</div>
		<div class="mvr-powered-by mvr-clear mvr-right">
		<?php /*phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped*/echo Factory::get_instance( TemplateIcons::class )->format_button_icon( 'forgetme' ); ?>
		</div>
	</div>

</div>
		<?php

		return ' ';
	}

}
