<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\MeetingIdGenerator;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library\MVRPersonalMeetingControllers;

/**
 * Class MVRPersonalMeeting - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRPersonalMeetingViews {

	// ---
	// Meet Center Template Section.

	/**
	 * Render Guest /Meet Page Template for no invite and no username -
	 * Template used for Trapping no input into meet centre and asking user for invite, or username
	 *
	 * @return string
	 */
	public function meet_guest_reception_template() {

		wp_enqueue_style( 'mvr-template' );
		wp_enqueue_style( 'mvr-menutab-header' );
		ob_start();

		?>
<div id="video-host-wrap" class="mvr-nav-shortcode-outer-wrap">

	<div class="mvr-header-table-left">
		<h2 class="mvr-header-title"><?php esc_html_e( 'Welcome to ', 'my-video-room' ) . esc_html( get_bloginfo( 'name' ) ); ?></h2>
		<img class="mvr-user-image" src="
				<?php
				$custom_logo_id = get_theme_mod( 'custom_logo' );
				$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
				if ( ! $image ) {
					$image = plugins_url( '/images/logoCC-clear.png', __FILE__ );
					echo esc_url( $image );
				} else {
					echo esc_url( $image[0] );
				}
				?>
				" alt="Site Logo">
	</div>
	<div class="mvr-header-table-right-split" class="mvr-header-title">
		<h2 class="mvr-header-title"><?php esc_html_e( 'Please Select Your Meeting Host', 'my-video-room' ); ?></h2>
		<form action="">
			<label for="host"
				class="mvr-header-label"><?php esc_html_e( 'Host\'s Username:', 'my-video-room' ); ?></label>
			<input type="text" id="host" name="host" class="mvr-select-box">
			<p class="mvr-title-label">
				<?php esc_html_e( 'This is the Site Username for the user you would like to join', 'my-video-room' ); ?>
			</p>
			<h2 class="mvr-header-title"><?php esc_html_e( 'OR', 'my-video-room' ); ?></h2>
			<label for="host"
				class="mvr-header-label"><?php esc_html_e( 'Host\'s Invite Code:', 'my-video-room' ); ?> </label>
			<input type="text" id="invite" name="invite" class="mvr-select-box">
			<p class="mvr-title-label">
				<?php esc_html_e( 'This is the Invite Code XXX-YYY-ZZZ for the meeting', 'my-video-room' ); ?></p>
			<input type="submit" value="Submit" class="mvr-form-button">
		</form>
	</div>
</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Render Meeting Center Signed Out Page template for meetings - to switch template to signed out users for meeting Page
	 */
	public function meet_signed_out_page_template() {
		wp_enqueue_style( 'mvr-menutab-header' );
		wp_enqueue_script( 'myvideoroom-outer-tabs' );
		?>
<div class="mvr-admin-page-wrap">
	<ul class="menu mvr-nav-settingstabs-outer-wrap myvideoroom-nav-tab-wrapper ul mvr-nav-border-bottom-none">
		<a class="mvr-outer-tab-active mvr-menu-header-item"
			href="#page100"><?php esc_html_e( 'Join a Meeting', 'my-video-room' ); ?></a>
		<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo do_action( 'mvr_output_outer_menu' );
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo do_action( 'mvr_output_signedout_menu' ); ?>
	</ul>
	<div id="page100" class="mvr-admin-page-wrap">
		<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_guest_shortcode(); 
		?>
	</div>
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
			echo do_action( 'mvr_output_outer_section' );
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
			echo do_action( 'mvr_output_signedout_section' );
			?>
</div>
		<?php
	}

	/**
	 * Render Meeting Center Signed In normal user Page template for meetings
	 */
	public function meet_signed_in_page_template() {
		wp_enqueue_style( 'mvr-menutab-header' );
		wp_enqueue_script( 'myvideoroom-outer-tabs' );
		?>
<div class="mvr-header-outer-wrap">
	<ul class="menu mvr-nav-settingstabs-outer-wrap myvideoroom-nav-tab-wrapper mvr-nav-border-bottom-none">
		<a class="outer-nav-tab-active mvr-menu-header-item"
			href="#page20"><?php esc_html_e( 'Join a Meeting', 'my-video-room' ); ?></a>
		<a class="mvr-menu-header-item"
			href="#page10"><?php esc_html_e( 'Host a Meeting', 'my-video-room' ); ?></a>
		<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo do_action( 'mvr_output_outer_menu' );
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo do_action( 'mvr_output_signedout_menu' ); ?>
	</ul>
	<div id="page10" class="mvr-shortcode-tab-wrap">
		<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_host_shortcode(); 
		?>
	</div>
	<div id="page20" class="mvr-shortcode-tab-wrap">
		<?php
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
				echo Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_guest_shortcode(); 
		?>
	</div>
		<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
			echo do_action( 'mvr_output_outer_section' );
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Function already Sanitised.
			echo do_action( 'mvr_output_signedout_section' );
		?>
</div>
		<?php
	}

	/**
	 * Render guest header template for meetings - used above guest room video shortcodes - provides meeting invite links, name, owner etc
	 *
	 * @param int $host_id - the Host ID of the room.
	 * @return string
	 */
	public function meet_guest_header( $host_id ): string {
		$module_id    = MVRPersonalMeeting::MODULE_PERSONAL_MEETING_DISPLAY;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$user         = get_user_by( 'id', $host_id );
		$name_output  = esc_html__( 'Visiting ', 'my-video-room' ) . $user->user_nicename;
		$is_guest     = true;
		$meeting_link = Factory::get_instance( MeetingIdGenerator::class )->invite_menu_shortcode( array( 'user_id' => $user_id ) );

		return $render( $module_id, $name_output, $host_id, MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, $is_guest, $meeting_link );
	}

	/**
	 * Render Host header template for meetings - used above Host room video shortcodes - provides meeting invite links, name, owner etc
	 *
	 * @return string
	 */
	public function meet_host_header(): string {

		$module_id    = MVRPersonalMeeting::MODULE_PERSONAL_MEETING_DISPLAY;
		$render       = require WP_PLUGIN_DIR . '/my-video-room/views/header/view-roomheader.php';
		$user         = wp_get_current_user();
		$user_id      = \get_current_user_id();
		$name_output  = esc_html__( 'Host ', 'my-video-room' ) . $user->user_nicename;
		$is_guest     = false;
		$meeting_link = Factory::get_instance( MeetingIdGenerator::class )->invite_menu_shortcode( array( 'user_id' => $user_id ) );

		return $render( $module_id, $name_output, $user_id, MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, $is_guest, $meeting_link );
	}
}
