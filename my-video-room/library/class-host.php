<?php
/**
 * Get details about the host WordPress is installed on
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

/**
 * Class Host
 */
class Host {

	/**
	 * Get the host name
	 *
	 * @return ?string
	 */
	public function get_host(): ?string {
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			return \preg_replace( '#^https?://#', '', \esc_url_raw( \wp_unslash( $_SERVER['HTTP_HOST'] ) ) );
		}

		return null;
	}
}
