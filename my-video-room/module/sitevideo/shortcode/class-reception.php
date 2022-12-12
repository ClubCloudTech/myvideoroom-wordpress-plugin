<?php
/**
 * Allow user to change video preferences
 *
 * @package my-video-room/module/sitevideo/shortcode/class-reception.php
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Shortcode;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Shortcode\App;


/**
 * Class SecurityVideoPreference
 */
class Reception {
	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_conference_reception';
	/**
	 * An increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Provide Runtime
	 */
	public function init() {
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'sitevideo_reception_shortcode' ) );
	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array|string $attributes List of shortcode params.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function sitevideo_reception_shortcode( $attributes = array() ): string {
		return Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_site_conference_page( true );
	}


}
