/**
 * Show and hide the add room form
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

/*global myvideroom_sitevideo_settings*/

(function ($) {
	function init(){
		console.log( 'init fire' );
		$( '#submit-button' ).hide();
		$( '#room-display-name' ).on( 'keyup', checkShow );
		$( '#room-url-link' ).on( 'keyup', checkShow );
	}

	/**
	 * Check if Name and Email conditions are met in main form
	 */
	function checkShow(){
		var display_name = $( '#room-display-name' ).val().length,
		url_link         = $( '#room-url-link' ).val().length;

		if (display_name >= 5) {
			$( '#room-name-icon' ).show();
		} else {
			$( '#submit' ).hide();
			$( '#room-name-icon' ).hide();
		}
		if (url_link >= 5) {
			$( '#room-link-icon' ).show();
		} else {
			$( '#submit' ).hide();
			$( '#room-link-icon' ).hide();
		}

		if ( display_name >= 5 && url_link >= 5 ) {
			$( '#submit-button' ).show();
			$( '#submit-button' ).addClass( 'mvr-title-header' );
		} else {
			$( '#submit-button' ).removeClass( 'mvr-title-header' );
			$( '#submit-button' ).hide();
			return false;
		}
	}
	$( '.myvideoroom-sitevideo-add-room' ).each(
		function () {
			var $add_room = $( this );
			$add_room.hide();

			var $button = $( '.myvideoroom-sitevideo-add-room-button', $add_room.parent() );

			$( '<span aria-label="button" class=" mvr-ul-style-menu mvr-main-button-cancel">Cancel</span>' )
				.appendTo( $( 'form', $add_room ) )
				.on(
					'click',
					function (e) {
						$button.show();
						$add_room.hide();
						e.stopPropagation();
						init();
						return false;
					}
				);

			$button
				.css( 'display', 'inline-block' )
				.on(
					'click',
					function () {
						$( this ).hide();
						$add_room.show();
						init();
					}
				);
		}
	);
	init();
})( jQuery );
