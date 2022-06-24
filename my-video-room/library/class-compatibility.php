<?php
/**
 * Provides Browser Compatibility Headers and Modification
 *
 * @package MyVideoRoomPlugin/library/class-compatibility.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class Compatibility
 *
 * Browser Compatibility Headers and Modification
 */
class Compatibility {

	/**
	 * Init for class
	 */
	public function init() {
		add_filter( 'wp_headers', array( $this, 'modify_wp_headers_for_chrome' ), 10, 2 );
	}

	/**
	 * Modify Headers for Chrome Compatiblity in Media Autoplay
	 *
	 * @param string $headers - the headers from the filter.
	 * @return string
	 */
	public function modify_wp_headers_for_chrome( $headers ) {
		$headers['Permissions-Policy'] = 'autoplay=*';

		return $headers;
	}

}
