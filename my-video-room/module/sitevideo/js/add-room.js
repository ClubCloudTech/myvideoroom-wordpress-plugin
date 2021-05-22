/**
 * Show and hide the add room form
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

/*global myvideroom_sitevideo_settings*/

(function ( $ ) {
	$( '.myvideoroom-sitevideo-add-room' ).each(
		function () {
			var $add_room = $( this );
			$add_room.hide();

			var $button = $( '.myvideoroom-sitevideo-add-room-button', $add_room.parent() );

			$( '<span aria-label="button" class="button button-primary negative">Cancel</span>' )
				.appendTo( $( 'form', $add_room ) )
				.on(
					'click',
					function ( e ) {
						$button.show();
						$add_room.hide();
						e.stopPropagation();
						return false;
					}
				);

			$button
				.css( 'display', 'inline-block' )
				.on(
					'click',
					function () {
						$( this ) .hide();
						$add_room.show();
					}
				)
		}
	)
})( jQuery );
