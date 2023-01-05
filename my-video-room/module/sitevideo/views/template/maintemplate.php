<?php
/**
 * Outputs the main site shortcode frame and template
 * This page appears from all non simple shortcode calls.
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo\Views\Template\maintemplate.php
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Render the Main Template
 *
 * @param string $header - the header of the template.
 * @param array  $tabs -Inbound object with tabs.
 * @param object $html_library - randomizing object class.
 * @param string $room_name - the room name.
 *
 * @return string
 */


return function (
	array $header,
	array $tabs,
	object $html_library,
	string $room_name = null
): string {
	ob_start();
	$id = wp_rand( 1, 5000 );
	if ( true === $header['visitor_status'] ) {
		$status = MVRSiteVideo::SETTING_GUEST;
	} else {
		$status = MVRSiteVideo::SETTING_HOST;
	}
	?>
<div class="mvr-nav-shortcode-outer-wrap " style="max-width: 1250px;">
	<div id="roominfo" data-room-name="<?php echo esc_attr( $room_name ); ?>"
		data-logged-in="<?php echo esc_attr( is_user_logged_in() ); ?>"
		data-room-type="<?php echo esc_attr( $header['room_type'] ); ?>"
		data-checksum="<?php echo esc_attr( Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_host_checksum( $header['room_type'], $status, $header['room_name'] ) ); ?>"
		>
	</div>
	<div class="mvr-header-section">
		<div id="mvr-notification-icons" class="myvideoroom-header-table-left">
			<?php //phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
				echo $header['template_icons'] ;
			?>
		</div>
		<div id="mvr-header-table-right" class="myvideoroom-header-table-right">
			<span class="mvr-header-title mvr-header-align">
				<?php //phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
							echo $header['name_output']. ' ' . $header['module_name'];
				?>
			</span>
				<p class="myvideoroom-header-adjust" data-id="<?php echo esc_url( $header['invite_menu'] ); ?>">
				<?php

			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
							echo esc_url( $header['invite_menu'] ) . '<i class="dashicons dashicons-clipboard myvideoroom-clipboard-copy" title="Copy"></i>'			
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
			$count = 0;
	foreach ( $tabs as $article_output ) {

		$function_callback = $article_output->get_function_callback();
		$tab_slug          = $article_output->get_tab_slug();
		?>
	<article id="<?php echo esc_attr( $html_library->get_id( $tab_slug ) ); ?>"
		class="myvideoroom-content-tab mvr-article-separation">
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
	<div class="mvr-clear"></div>
</div>

	<?php

			return \ob_get_clean();
};
