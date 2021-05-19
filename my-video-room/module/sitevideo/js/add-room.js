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

			$add_room.append( '<span class="close">×</span>' ).on(
				'click',
				function () {
					$add_room.hide();
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
