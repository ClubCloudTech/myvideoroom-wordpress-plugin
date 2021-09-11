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

	const TAB_VIDEO_ROOM       = 'Video Room';
	const TAB_HOST_SETTINGS    = 'Host Settings';
	const TAB_ROOM_PERMISSIONS = 'Room Permissions';
	const TAB_STOREFRONT       = 'Storefront';
	const TAB_SHOPPING_BASKET  = 'Shopping Basket';


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
		$tabs         = apply_filters( 'myvideoroom_main_template_render', $inbound_tabs, $user_id, $room_name, $host_status );

		// Proxy for Site Template Mode.
		if ( SiteDefaults::VIDEO_TEMPLATE_MODE === 1 ) {

			?>

			<div class="mvr-nav-shortcode-outer-wrap">
				<div class="mvr-header-section">

						<div id="mvr-header-table-left" class="mvr-header-table-left">
						<i class = "mvr-header-align">
						<?php echo $header['template_icons']; ?>
					</i>	
					</div>
						<div id="mvr-header-table-right" class="mvr-header-table-right">
						<p class = "mvr-header-title mvr-header-align">
						<?php //phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
							echo $header['name_output']. ' ' . $header['module_name'];
						?>
						</p>
						</div>

						<?php
							//echo \var_dump( $header);

							//echo $header;
						?>
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
				<ul>
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
								<?php echo $tab_display_name ; ?>
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

						// Original Template
		} else {

			?>

<div class="mvr-nav-shortcode-outer-wrap">
	<div class="mvr-header-section">
			<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
				echo $header;
			?>
	</div>

			<?php
			$tab_count = \count( $tabs );
			if ( $tab_count <= 1 ) {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode already properly escaped.
				echo $tabs[0]->get_function_callback();
			} else {
				?>
	<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
		<ul>
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
					<?php echo esc_html( $tab_display_name ); ?>
				</a>
			</li>
					<?php
					$active = null;
				}
				?>
		</ul>
	</nav>
	<div id="mvr-notification-master" class="mvr-nav-shortcode-outer-wrap-clean mvr-notification-master">
				<?php
				$output = \apply_filters( 'myvideoroom_notification_master', '', $room_name );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - callback escaped within itself.
				echo $output;
				?>
	</div>
				<?php
				foreach ( $tabs as $article_output ) {
					$function_callback = $article_output->get_function_callback();
					$tab_slug          = $article_output->get_tab_slug();
					?>
	<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>">
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
	}

	public function template_icon_switch( string $input_type ) {

		$template_status = Factory::get_instance( SiteDefaults::class )->horizontal_site_template_mode();

		if ( ! $template_status ) {
			switch ( $input_type ) {
				case self::TAB_VIDEO_ROOM:
					return self::TAB_VIDEO_ROOM;
				case self::TAB_HOST_SETTINGS:
					return self::TAB_HOST_SETTINGS;
				case self::TAB_ROOM_PERMISSIONS:
					return self::TAB_ROOM_PERMISSIONS;
				case self::TAB_STOREFRONT:
					return self::TAB_STOREFRONT;
				case self::TAB_SHOPPING_BASKET:
					return self::TAB_SHOPPING_BASKET;
			}
		} else {
			switch ( $input_type ) {
				case self::TAB_VIDEO_ROOM:
					return '<span title ="' . esc_html__( 'Video Room', 'myvideoroom' ) . '" class="dashicons dashicons-video-alt3"></span>';
				case self::TAB_HOST_SETTINGS:
					return '<span title ="' . esc_html__( 'Room Hosting, Reception, and Layout Settings', 'myvideoroom' ) . '" class="dashicons dashicons-welcome-widgets-menus"></span>';
				case self::TAB_ROOM_PERMISSIONS:
					return '<span title ="' . esc_html__( 'Room Security and Permissions', 'myvideoroom' ) . '" class="dashicons dashicons-lock"></span>';
				case self::TAB_STOREFRONT:
					return '<span title ="' . esc_html__( 'Room Builtin Storefront', 'myvideoroom' ) . '" class="dashicons dashicons-store"></span>';
				case self::TAB_SHOPPING_BASKET:
					return '<span title ="' . esc_html__( 'Your Shopping Basket with AutoSync to Room', 'myvideoroom' ) . '" class="dashicons dashicons-cart"></span>';
			}
		}
	}

}
