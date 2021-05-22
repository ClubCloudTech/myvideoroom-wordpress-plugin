/**
 * Get the settings section
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

/*global myvideoroom_sitevideo_settings*/

(function ( $ ) {
	$( '.myvideoroom-sitevideo-settings' ).on(
		'click',
		function (e) {
			var room_id = $( this ).data( 'roomId' );

			var $container   = $( '.mvr-security-room-host' );
			var loading_text = $container.data( 'loadingText' );

			$container.html( loading_text );

			var ajax_url = myvideoroom_sitevideo_settings.ajax_url;

			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: ajax_url,
					data: {
						action: 'myvideoroom_sitevideo_settings',
						roomId: room_id
					},
					success: function (response) {
						if ('URLSearchParams' in window) {
							var searchParams = new URLSearchParams( window.location.search )
							searchParams.set( 'room_id', room_id );

							var newRelativePathQuery = window.location.pathname + '?' + searchParams.toString();
							history.pushState( null, '', newRelativePathQuery );
						}

						$container.html( response );

						if ( window.myvideoroom_tabbed_init ) {
							window.myvideoroom_tabbed_init( $container );
						}
					},
					error: function (response) {

					}
				}
			);

			e.preventDefault();
			return false;
		}
	);
})( jQuery );
