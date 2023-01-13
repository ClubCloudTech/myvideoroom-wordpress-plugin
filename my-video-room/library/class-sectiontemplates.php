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
	 * @param Array   $header       The Header of the Shortcode.
	 * @param array   $inbound_tabs Inbound object with tabs.
	 * @param ?int    $user_id      User ID for passing to other Filters.
	 * @param ?string $room_name    Room Name for passing to other Filters.
	 * @param bool    $host_status  Whether user is a host.
	 * @param bool    $ajax_window_flag Marks the Page is being rendered from an Ajax window where you want to limit what plugins/tabs run.
	 *
	 * @return ?string The completed Template.
	 */
	public function shortcode_template_wrapper( array $header, array $inbound_tabs, int $user_id = null, string $room_name = null, bool $host_status = null, bool $ajax_window_flag = null ): ?string {

		Factory::get_instance( SessionState::class )->register_room_presence( $room_name, $host_status, $user_id );
		$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
		$tabs         = apply_filters( 'myvideoroom_main_template_render', $inbound_tabs, $user_id, $room_name, $host_status, $header, $ajax_window_flag );

		$render = include __DIR__ . '/../module/sitevideo/views/template/maintemplate.php';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template already escaped.
		return $render( $header, $tabs, $html_library, $room_name );

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
				return '<span title ="' . esc_html__( 'Video Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-video-alt3 myvideoroom-dashicons-override"></span>';
			case self::TAB_VIDEO_ROOM_SETTINGS:
				return '<span title ="' . esc_html__( 'Room Reception, and Layout Settings', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-admin-generic myvideoroom-dashicons-override"></span>';
			case self::TAB_ROOM_PERMISSIONS:
				return '<span title ="' . esc_html__( 'Room Security and Permissions', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-lock myvideoroom-dashicons-override"></span>';
			case self::TAB_STOREFRONT:
				return '<span title ="' . esc_html__( 'Room Builtin Storefront', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-store myvideoroom-dashicons-override"></span>';
			case self::TAB_SHOPPING_BASKET:
				return '<span title ="' . esc_html__( 'Your Shopping Basket with AutoSync to Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-cart myvideoroom-dashicons-override"></span>';
			case self::TAB_INFO_WELCOME:
				return '<span title ="' . esc_html__( 'Welcome and Information', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-nametag myvideoroom-dashicons-override"></span>';
			case self::TAB_INFO_RECEPTION:
				return '<span title ="' . esc_html__( 'Main Room Reception Centre', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-clipboard myvideoroom-dashicons-override"></span>';
			case self::TAB_HOST_ROOM_SETTINGS:
				return '<span title ="' . esc_html__( 'Control Room Hosts', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-businessman myvideoroom-dashicons-override"></span>';
			case self::BUTTON_REFRESH:
				return '<span title ="' . esc_html__( 'Reconnect to Basket', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-update-alt myvideoroom-dashicons-override" ></span>';
			case self::BUTTON_SHARE_BASKET:
				return '<span title ="' . esc_html__( 'Share Your Basket with the Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-share-alt2 myvideoroom-dashicons-override"></span><span title ="' . esc_html__( 'Share Your Basket with the Room', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-cart"></span>';
			case self::BUTTON_REQUEST_BASKET:
				return '<span title ="' . esc_html__( 'Request Control of the Room Basket', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-format-status myvideoroom-dashicons-override"></span><span title ="' . esc_html__( 'Request Control of the Room Basket', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-cart"></span>';
			case 'accept':
				return '<span title ="' . esc_html__( 'Accept', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-thumbs-up myvideoroom-dashicons-override"></span>';
			case 'reject':
				return '<span title ="' . esc_html__( 'Reject', 'myvideoroom' ) . '" class="myvideoroom-dashicons dashicons-thumbs-down myvideoroom-dashicons-override"></span>';
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

		return \ob_get_clean();
	}

}
