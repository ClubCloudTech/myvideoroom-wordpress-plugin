<?php

/**
 * Addon functionality for Site Video Room.
 *
 * @package MyVideoRoomPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\Module\Security\Templates\SecurityTemplates;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\VideoHelpers;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Module\Security\Library\SecurityEngine;
use MyVideoRoomPlugin\Views\Errors\ErrorTemplates;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoControllers {


	/**
	 * Provides Shortcode support for SiteVideo Conference Center Rooms.
	 *
	 * @param mixed $params - ID - the PostID that comes from Shortcode.
	 *
	 * @return string
	 */
	public function sitevideo_shortcode( $params = array() ): string {
		$id = $params['id'] ?? null;
		$id = \intval( $id );

		Factory::get_instance( UserVideoPreference::class )->check_for_update_request();
		Factory::get_instance( SecurityVideoPreference::class )->check_for_update_request();

		return $this->sitevideo_switch( $id );
	}

	/**
	 * Auto Switching Function for Site Video Room to Host and Guests
	 *
	 * @param int $id - the Room ID to access.
	 *
	 * @return string the correct template.
	 */
	public function sitevideo_switch( int $id ): string {

		if ( ! Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( MVRSiteVideo::MODULE_SITE_VIDEO_ID ) ) {
			return Factory::get_instance( SecurityTemplates::class )->room_blocked_by_site();
		}

		wp_enqueue_style( 'dashicons' );
		$current_user_is_host = apply_filters(
			'myvideoroom_site_video_user_host_status',
			current_user_can( Plugin::CAP_GLOBAL_HOST ),
			$id
		);

		if ( $current_user_is_host ) {
			return $this->site_videoroom_host_function( $id );
		} else {
			return $this->site_videoroom_guest_shortcode( $id );
		}
	}

	/**
	 * A Shortcode for the Site Video Room - Host
	 * This is used for the Member admin entry pages to access their preferred Video Layout - it is paired with the sitevideoroomguest function and accessed by the relevant video switch
	 *
	 * @param int  $post_id - Post ID of DB.
	 * @param bool $in_ajax_window - Flag to Disable specific tabs (like conference center) that should not run inside Ajax, and only on top parent.
	 *
	 * @return string
	 */
	public function site_videoroom_host_function( int $post_id, bool $in_ajax_window = null ): string {
		// Shortcode Initialise Hooks.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();

		// Get Room Entity Information.
		$room_name    = null;
		$display_name = '';

		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $post_id );
		if ( $room_object ) {
			$room_name    = $room_object->room_name;
			$display_name = $room_object->display_name;
		} else {
			return Factory::get_instance( ErrorTemplates::class )->invalid_room_name();
		}

		// Security Engine - blocks room rendering if another setting has blocked it (eg upgrades, site lockdown, or other feature).
		if ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( Dependencies::MODULE_SECURITY_ID ) ) {
			$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $post_id, 'sitevideohost', MVRSiteVideo::MODULE_SITE_VIDEO_ID, $room_name );
			if ( $render_block ) {
				return $render_block;
			}
		}

		// Get Room Parameters.
		$video_template = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $post_id, $room_name, true );

		$myvideoroom_app = AppShortcodeConstructor::create_instance()
			->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'sitevideo', $display_name ) )
			->set_original_room_name( $room_name )
			->set_layout( $video_template )
			->set_as_host();

		// Construct Shortcode Template - and execute.
		$header        = Factory::get_instance( MVRSiteVideoViews::class )->site_videoroom_host_template( $post_id );
		$host_status   = true;
		$output_object = array();
		$display_name  = Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM );
		$host_menu     = new MenuTabDisplay(
			$display_name,
			'videoroom',
			fn () => \do_shortcode( $myvideoroom_app->output_shortcode_text() ),
			'mvr-video'
		);
		array_push( $output_object, $host_menu );
		$admin_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM_SETTINGS ),
			'adminpage',
			fn () => \do_shortcode(
				//phpcs:ignore PEAR.Functions.FunctionCallSignature.Indent - indent is correct.
				Factory::get_instance( UserVideoPreference::class )->choose_settings(
					$post_id,
					$room_name
				)
			)
		);
		array_push( $output_object, $admin_menu );

		return Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $post_id, $room_name, $host_status, $in_ajax_window );
	}

	/**
	 * Site Viderooms Guest Entrance.
	 *
	 * Shortcode Handler for the Site Video Rooms - for guests
	 * This is used for the Guest entry pages to access the Management Meeting Room - it is paired with the sitevideoroomhost shortcode
	 *
	 * @param int $room_id - the ID of the Room to process.
	 *
	 * @since Version 1
	 */
	public function site_videoroom_guest_shortcode( int $room_id ):string {
		// Shortcode Initialise Hooks.
		factory::get_instance( SiteDefaults::class )->shortcode_initialise_filters();

		// Get Room Entity Information.
		$room_object  = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );
		$room_name    = $room_object->room_name;
		$display_name = $room_object->display_name;

		if ( ! $room_name ) {
			return Factory::get_instance( ErrorTemplates::class )->invalid_room_name();
		}

		// Security Engine - blocks room rendering if another setting has blocked it (eg upgrades, site lockdown, or other feature).
		if ( Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( Dependencies::MODULE_SECURITY_ID ) ) {
			$render_block = Factory::get_instance( SecurityEngine::class )->render_block( $room_id, 'sitevideoguest', MVRSiteVideo::MODULE_SITE_VIDEO_ID, $room_name );
			if ( $render_block ) {
				return $render_block;
			}
		}
		// Get Parameters for Room Info.
		$reception_setting     = Factory::get_instance( VideoHelpers::class )->get_enable_reception_state( $room_id, $room_name, true );
		$reception_template    = Factory::get_instance( VideoHelpers::class )->get_reception_template( $room_id, $room_name, true );
		$video_template        = Factory::get_instance( VideoHelpers::class )->get_videoroom_template( $room_id, $room_name, true );
		$video_reception_state = Factory::get_instance( VideoHelpers::class )->get_video_reception_state( $room_id, $room_name, true );
		$video_reception_url   = Factory::get_instance( VideoHelpers::class )->get_video_reception_url( $room_id, $room_name, true );
		$disable_floorplan     = Factory::get_instance( VideoHelpers::class )->get_show_floorplan( $room_id, $room_name, true );

		// Build Base Room.
		$myvideoroom_app = AppShortcodeConstructor::create_instance();
		$myvideoroom_app->set_name( Factory::get_instance( SiteDefaults::class )->room_map( 'sitevideo', $display_name ) );
		$myvideoroom_app->set_layout( $video_template );
		$myvideoroom_app->set_original_room_name( $room_name );

		// Reception setting.
		if ( $reception_setting ) {
			$myvideoroom_app->enable_reception()->set_reception_id( $reception_template );

			if ( $video_reception_state ) {
				$myvideoroom_app->set_reception_video_url( $video_reception_url );
			}
		}

		// Floorplan Disable setting.
		if ( $disable_floorplan ) {
			$myvideoroom_app->disable_floorplan();
		}
		// Construct Shortcode Template - and execute.
		$header        = Factory::get_instance( MVRSiteVideoViews::class )->site_videoroom_guest_template( $room_id );
		$host_status   = false;
		$output_object = array();
		$host_menu     = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_VIDEO_ROOM ),
			'videoroom',
			fn () => \do_shortcode( $myvideoroom_app->output_shortcode_text() ),
			'mvr-video'
		);
		array_push( $output_object, $host_menu );

		return Factory::get_instance( SectionTemplates::class )->shortcode_template_wrapper( $header, $output_object, $room_id, $room_name, $host_status );
	}
}
