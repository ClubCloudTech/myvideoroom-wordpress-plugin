/**
 * Restricts an input to only alphanumeric characters
 *
 * @package MyVideoRoomPlugin
 */

(function ($) {
	var $inputs = $( 'input.myvideoroom-input-restrict-alphanumeric' );
	$inputs.on(
		'keyup keydown',
		function (e) {
			return ! ! ( /[a-z0-9]$/i.test( e.key ) );
		}
	)
})( jQuery );
