/**
 * Handle Ajax requests for Tabbed Frames
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

/*global myvideoroom_sitevideo_settings*/

(function ($) {
	window.myvideoroom_tabbed_init;
	$( '.myvideoroom-sitevideo-settings' ).on(
		'click',
		function (e) {
			var room_id    = $( this ).data( 'roomId' );
			var input_type = $( this ).data( 'inputType' );
			var $container   = $( '.mvr-security-room-host' );
			var loading_text = $container.data( 'loadingText' );
			$( '.myvideoroom-sitevideo-hide-button' ).show();
			if ( input_type === 'close' ) {
				$container.empty();
				$( '#mvr-close_'+room_id ).hide();
				return false;
			}

			$container.html( loading_text );

			var ajax_url = myvideoroom_sitevideo_settings.ajax_url;

			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: ajax_url,
					data: {
						action: 'myvideoroom_sitevideo_settings',
						roomId: room_id,
						inputType: input_type
					},
					success: function (response) {
						if ('URLSearchParams' in window) {
							var searchParams = new URLSearchParams( window.location.search );
							searchParams.set( 'room_id', room_id );

							var newRelativePathQuery = window.location.pathname + '?' + searchParams.toString();
							history.pushState( null, '', newRelativePathQuery );
						}

						$container.html( response );

						if (window.myvideoroom_tabbed_init) {
							window.myvideoroom_tabbed_init( $container );
						}

						if (window.myvideoroom_app_init) {
							window.myvideoroom_app_init( $container[0] );
						}

						if (window.myvideoroom_app_load) {
							window.myvideoroom_app_load();
						}

						if (window.myvideoroom_shoppingbasket_init) {
							window.myvideoroom_shoppingbasket_init();
						}

					}
				}
			);

			e.preventDefault();
			return false;
		}
	);
})( jQuery );
