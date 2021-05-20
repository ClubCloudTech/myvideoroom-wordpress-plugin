<?php
/**
 * Display section templates
 *
 * @package MyVideoRoomPlugin\Library\SectionTemplates.php
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Shortcode as Shortcode;

/**
 * Class SectionTemplate
 */
class SectionTemplates extends Shortcode {

	/**
	 * Render a Template to Automatically Wrap the Video Shortcode with additional tabs to add more functionality
	 *  Used to add Admin Page for each Room for Hosts, Returns Header and Shortcode if no additional pages passed in
	 *
	 * @param ?string $header           The Header of the Shortcode.
	 * @param ?string $shortcode        The Shortcode to Render.
	 * @param ?string $admin_page       The admin page if any.
	 * @param null    $permissions_page The Permissions Page.
	 *
	 * @return string. The completed Formatted Template.
	 */
	public function shortcode_template_wrapper( string $header = null, string $shortcode = null, string $admin_page = null, $permissions_page = null ): string {

		// Randomizing Pages by Header to avoid page name conflicts if multiple frames.
		$header_length    = strlen( $header );
		$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( Dependencies::MODULE_SECURITY_ID );
		?>
		<div class="mvr-nav-shortcode-outer-wrap">
			<div class="mvr-header-section">
			<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Header Already Escaped.
				echo $header;
			?>
			</div>
			<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper mvr-shortcode-menu">
			<?php
			// Video Menu Tab - only show if others exist.
			if ( $admin_page || $permissions_page ) {
				echo '<a class="nav-tab-active mvr-menu-header-item mvr-main-shortcode" href="#myvideoroom_page1' . esc_attr( $header_length ) . '" >' . esc_html__( 'Video Room', 'my-video-room' ) . '</a>';
			}
			// Security Tab.
			if ( $security_enabled && $permissions_page ) {
				echo '<a class="mvr-menu-header-item mvr-main-shortcode" href="#myvideoroom_page2' . esc_attr( $header_length ) . '" >' . esc_html__( 'Room Permissions', 'my-video-room' ) . '</a>';
			}
			// Admin Tab.
			if ( $admin_page ) {
				echo '<a class="mvr-menu-header-item mvr-main-shortcode" href="#myvideoroom_page3' . esc_attr( $header_length ) . '" >' . esc_html__( 'Host Settings', 'my-video-room' ) . '</a>';
			}
			?>
			</nav>
			<?php
			/*
				Adding Body Section
			*/
			// Adding Shortcode (the only one guaranteed to exist).
			$output  = '<article id="myvideoroom_page1' . esc_attr( $header_length ) . '" >';
			$output .= $shortcode . '</article>';
			// Adding Permissions Tab if Exists.
			if ( $security_enabled && $permissions_page ) {
				$output .= '<article id="myvideoroom_page2' . esc_attr( $header_length ) . '">';
				$output .= $permissions_page . ' </article>';
			}
			// Adding Room Admin Tab if Exists.
			if ( $admin_page ) {
				$output .= '<article id="myvideoroom_page3' . esc_attr( $header_length ) . '">';
				$output .= $admin_page . ' </article>';
			}

			$output .= '</div>';
			return $output;
	}
}
