<?php
/**
 * Allow user to change video preferences
 *
 * @package MyVideoRoomPlugin\Module\Security\Shortcode
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Shortcode;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Module\Security\DAO\SecurityVideoPreference as SecurityVideoPreferenceDao;
use MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference as SecurityVideoPreferenceEntity;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoRoomHelpers;
use MyVideoRoomPlugin\Module\SiteVideo\Library\MVRSiteVideoViews;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Shortcode\App;
use MyVideoRoomPlugin\SiteDefaults;

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
		$room_name = $attributes['room'] ?? 'default';

		// $this->check_for_update_request();

		return Factory::get_instance( MVRSiteVideoRoomHelpers::class )->create_site_conference_page( true );
	}


}
