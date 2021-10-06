<?php
/**
 * Ajax for Site Video Room.
 *
 * @package MyVideoRoomPlugin\Modules\SiteVideo\MVRSiteVideoRedirect.php
 */

namespace MyVideoRoomPlugin\Module\SiteVideo\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Module\SiteVideo\MVRSiteVideo;
use MyVideoRoomPlugin\Shortcode\App;

/**
 * Class MVRSiteVideo - Renders the Video Plugin for SiteWide Video Room.
 */
class MVRSiteVideoRedirect {


	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_redirect';

	/**
	 * Initialisation
	 */
	public function init() {
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'render_redirect_shortcode' ) );
	}



	/** Current Picture Path
	 * Returns current file name of upload directory.
	 *
	 * @param array $attributes - Shortcode to render in Iframe.
	 * @return ?string
	 */
	public function render_redirect_shortcode( array $attributes = null ) {

		$http_get_library = Factory::get_instance( HttpGet::class );
		$nonce_received   = $http_get_library->get_string_parameter( 'nonce' );
		$shortcode        = $http_get_library->get_string_parameter( 'shortcode' );
		$nonce_verify     = \wp_verify_nonce( $nonce_received, $shortcode . MVRSiteVideo::ROOM_SLUG_REDIRECT );

		if ( ! $http_get_library->is_get_request( MVRSiteVideo::ROOM_SLUG_REDIRECT ) || ! $nonce_verify ) {
			esc_html_e( 'Nonce Invalid or Invalid Post Received', 'myvideoroom' );
			return null;
		}
		$id = $http_get_library->get_integer_parameter( 'itemid' );
		if ( $shortcode ) {
			?>
			<div class="mvr-admin-page-wrap">
			<?php
			echo do_shortcode( '[' . $shortcode . ']' );
			?>
			</div>
			<?php
		}

	}
}
