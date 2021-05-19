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

			$('<span class="close">×</span>' ).appendTo( $add_room ).on(
				'click',
				function ( e ) {
					$add_room.hide();
					e.stopPropagation();
					return false;
				}
			);

			$( '.myvideoroom-sitevideo-add-room-button', $add_room.parent() )
				.css( 'display', 'inline-block' )
				.on(
					'click',
					function () {
						$add_room.show();
					}
				)
		}
	)
})( jQuery );
