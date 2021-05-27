<?php
/**
 * SiteVideo Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/SiteVideo
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\SiteVideo;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Plugin;

\add_action(
	Plugin::ACTION_INIT,
	function () {
		Module::register(
			MVRSiteVideo::MODULE_SITE_VIDEO_NAME,
			\esc_html__( 'Room Management Center and Conferencing', 'myvideoroom' ),
			array(
				\esc_html__(
					'The site conference and room management module suite is available for team wide meetings, events, or any need for central rooms at the website level. These permanent rooms are created automatically by the module, at activation, and can be renamed. They can be individually secured such that any site role group can host the room. Room permissions, reception settings, templates, and custom reception videos are all available for each conference room. You can connect permanent WebRTC enabled devices like projectors, and microphones to rooms permanently',
					'myvideoroom'
				),
			),
			fn() => Factory::get_instance( MVRSiteVideo::class )->init()
		)
		->add_activation_hook(
			fn () => Factory::get_instance( MVRSiteVideo::class )->activate_module()
		)
		->add_admin_page_hook( fn () => Factory::get_instance( MVRSiteVideoRoomHelpers::class )->render_sitevideo_admin_page() )
		->add_deactivation_hook(
			fn () => Factory::get_instance( MVRSiteVideo::class )->de_activate_module()
		);
	}
);
