/**
 * Get the settings section
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

/*global myvideroom_sitevideo_settings*/

(function ( $ ) {
	$( '.myvideoroom-sitevideo-settings' ).on(
		'click',
		function (e) {
			var post_id = $( this ).data( 'postId' );

			var ajax_url = myvideoroom_sitevideo_settings.ajax_url;

			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: ajax_url,
					data: {
						action: 'myvideoroom_sitevideo_settings',
						postId: post_id
					},
					success: function (response) {
						$( '.mvr-security-room-host' ).html( response );
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
