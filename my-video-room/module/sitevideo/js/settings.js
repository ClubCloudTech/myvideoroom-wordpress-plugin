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
			var post_id   = $( this ).data( 'postId' );
			var room_name = $( this ).data( 'roomName' );

			var ajax_url = myvideoroom_sitevideo_settings.ajax_url;

			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: ajax_url,
					data: {
						action: 'myvideoroom_sitevideo_settings',
						postId: post_id,
						roomName: room_name
					},
					success: function (response) {
						var $container = $( '.mvr-security-room-host' );

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
