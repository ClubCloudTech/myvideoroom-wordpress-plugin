<?php
/**
 * SiteVideo Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/SiteVideo
 */

declare( strict_types=1 );

<<<<<<< HEAD
namespace MyVideoRoomPlugin\Module\SiteVideo;

=======
use MyVideoRoomPlugin\Factory;
>>>>>>> Next batch of cleanup post arrival
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;

\add_action(
	'myvideoroom_init',
	function () {
		Module::register(
<<<<<<< HEAD
			'site-video',
			\esc_html__( 'Site Video', 'myvideoroom' ),
			array(
				\esc_html__(
					'The site wide video room is available for team wide meetings, events, or any need for a central room at the website level. This permanent room is created automatically by the module at activation, and can be renamed and removed. It is secured such that any normal site administrator is a host of the room. Room permissions, reception settings, templates, and custom reception videos are all available for the room.',
=======
			'siteconference',
			'Site Conference Center',
			array(
				esc_html__(
					'The site conference module suite is available for team wide meetings, events, or any need for central rooms at the website level.
					These permanent rooms are created automatically by the module, at activation, and can be renamed. They can be individually secured
					such that any site role group can host the room. Room permissions, reception settings, templates, and custom reception videos
					are all available for each conference room. You can connect permanent WebRTC enabled devices like projectors, and microphones to rooms permanently',
>>>>>>> Next batch of cleanup post arrival
					'myvideoroom'
				),
			),
			fn() => Factory::get_instance( MVRSiteVideo::class )->runtime()
		)
		->add_activation_hook(
			fn () => Factory::get_instance( MVRSiteVideo::class )->initialise_module()
		)
		->add_admin_page_hook( fn () => Factory::get_instance( MVRSiteVideo::class )->render_sitevideo_admin_page() )
		->add_deactivation_hook(
			fn () => Factory::get_instance( MVRSiteVideo::class )->de_initialise_module()
		);

		/**
		 * Example of adding hooks
		 * ->add_compatibility_hook( fn () => true )
		 * ->add_admin_page_hook( fn () => 'The room builder was successfully activated' )
		 * ->add_activation_hook( fn () => false )
		 * ->add_deactivation_hook( fn () => false );
		 */
	}
);
