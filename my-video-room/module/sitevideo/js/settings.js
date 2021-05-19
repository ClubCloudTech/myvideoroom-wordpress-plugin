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
			var post_id = $( this ).data( 'postId' );
			var input_type = $( this ).data( 'inputType' );

			var ajax_url = myvideoroom_sitevideo_settings.ajax_url;

			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: ajax_url,
					data: {
						action: 'myvideoroom_sitevideo_settings',
						postId: post_id,
						inputType: input_type
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
