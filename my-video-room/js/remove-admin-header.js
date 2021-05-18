/**
 * Remove Admin Headers and Menu's from Selected Admin Center Pages.
 *
 * @package MyVideoRoomPlugin
 */

jQuery( document ).ready(
	function($) {
		$( '#adminmenuback, #adminmenuwrap, #wpadminbar' ).remove();
	}
);
