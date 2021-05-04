<?php
/**
 * Get details about the host WordPress is installed on
 *
 * @package MyVideoRoomPlugin\Library
 */

namespace MyVideoRoomPlugin\Library;

/**
 * Class Host
 */
class Host {

	/**
	 * Get the host name
	 *
	 * @return string
	 */
	public function get_host(): string {
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$host = preg_replace( '#^https?://#', '', esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) ) );
		} else {
			$host = '';
		}

		return $host;
	}
}
