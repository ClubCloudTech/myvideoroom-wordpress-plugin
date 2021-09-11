<?php
/**
 * Display section templates
 *
 * @package MyVideoRoomPlugin\Library\SectionTemplates.php
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\SiteDefaults;

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
		ob_start();
		// Randomizing Pages by Header to avoid page name conflicts if multiple frames.
		$html_library = Factory::get_instance( HTML::class, array( 'view-management' ) );
		$tabs         = apply_filters( 'myvideoroom_main_template_render', $inbound_tabs, $user_id, $room_name, $host_status, $header );

?>

<div class="mvr-nav-shortcode-outer-wrap">
	<div class="mvr-header-section">

		<div id="mvr-header-table-left" class="mvr-header-table-left">
			<i class="mvr-header-align">
				<?php echo $header['template_icons']; ?>
			</i>
		</div>
		<div id="mvr-header-table-right" class="mvr-header-table-right">
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
							echo $output;
			?>
	</div>

			<?php
						$tab_count = \count( $tabs );
			if ( $tab_count <= 1 ) {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode already properly escaped.
				echo $tabs[0]->get_function_callback();
			} else {
				?>

	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper myvideoroom-side-tab">
		<ul class="mvr-ul-style-menu">
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
		</ul>
					<?php
					$active = null;
				}
				?>

	</nav>


				<?php
				foreach ( $tabs as $article_output ) {
					$function_callback = $article_output->get_function_callback();
					$tab_slug          = $article_output->get_tab_slug();
					?>
	<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>" class="myvideoroom-content-tab">
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

		$template_status = Factory::get_instance( SiteDefaults::class )->horizontal_site_template_mode();

		if ( ! $template_status ) {
			switch ( $input_type ) {
				case self::TAB_VIDEO_ROOM:
					return self::TAB_VIDEO_ROOM;
				case self::TAB_VIDEO_ROOM_SETTINGS:
					return self::TAB_VIDEO_ROOM_SETTINGS;
				case self::TAB_ROOM_PERMISSIONS:
					return self::TAB_ROOM_PERMISSIONS;
				case self::TAB_STOREFRONT:
					return self::TAB_STOREFRONT;
				case self::TAB_SHOPPING_BASKET:
					return self::TAB_SHOPPING_BASKET;
				case self::TAB_INFO_WELCOME:
					return self::TAB_INFO_WELCOME;
			}
		} else {
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
			}
		}
	}
	/**
	 * Welcome Template.
	 *
	 * @param int $user_id - the user ID who is blocking.
	 *
	 * @return string
	 */
	public function welcome_template(): string {

		ob_start();
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
		<?php
		echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
		esc_html_e( 'Welcome to Our Video Room', 'myvideoroom' );
		?>
	</h2>
		<img class="myvideoroom-center" src="
			<?php echo esc_url( plugins_url( '/../img/screen-1.png', __FILE__ ) ); ?>" alt="Video">

		<p class="mvr-template-text">
			<?php
			$new_user           = get_userdata( $user_id );
			$first_display_name = '<strong>' . esc_html__( 'Welcome', 'my-video-room' ) . '</strong>';
			if ( $new_user ) {
				$first_name = $new_user->user_firstname;
				$nicename   = $new_user->user_nicename;
				if ( $first_name ) {
					$first_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
				} elseif ( $nicename ) {
					$first_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
				}
			}
			$second_display_name = esc_html__( 'the site administrators', 'my-video-room' );
			if ( $new_user ) {
				$first_name = $new_user->user_firstname;
				$nicename   = $new_user->user_nicename;
				if ( $first_name ) {
					$second_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
				} elseif ( $nicename ) {
					$second_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
				}
			}
			echo sprintf(
			/* translators: %1s is the text "The Administrator" and %2s is "the site administrators" */
				esc_html__( '%1$s Our rooms work best if you are signed in. Click the help icon for instructions or contact the site owner, your host, or %2$s for more assistance.', 'myvideoroom' ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
				$first_display_name,
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
				$second_display_name
			);
			?>
		</p>
		<div class="mvr-powered-by">
				<img class="myvideoroom-product-image" src="
			<?php echo esc_url( plugins_url( '/../img/mvr-imagelogo.png', __FILE__ ) ); ?>" alt="Powered by MyVideoRoom">
		</div>


		<?php

		return ' ';
	}

}
