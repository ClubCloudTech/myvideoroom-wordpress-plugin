<?php
/**
 * SiteVideo Module for MyVideoRoom
 *
 * @package MyVideoRoomPlugin/Module/SiteVideo
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Module;

add_action(
	'myvideoroom_init',
	function () {
		Factory::get_instance( Module::class )
			->register(
				'site-video',
				__( 'Site Video', 'myvideoroom' ),
				array(
					__(
						'The site wide video room is available for team wide meetings, events, or any need for a central room at the website level. This permanent room is created automatically by the module at activation, and can be renamed and removed. It is secured such that any normal site administrator is a host of the room. Room permissions, reception settings, templates, and custom reception videos are all available for the room.',
						'myvideoroom'
					),
				),
			);
	}
);
