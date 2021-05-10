/**
 * Load the video app for all instances of the app
 *
 * @package MyVideoRoomPlugin
 */

/*global myVideoRoomAppEndpoint*/

jQuery.noConflict()(
	function () {
		var $ = jQuery.noConflict();

		if ($( '.myvideoroom-app' ).length) {
			if ($.ajaxSettings && $.ajaxSettings.headers) {
				delete $.ajaxSettings.headers;
			}

			$.ajax(
				{
					url: myVideoRoomAppEndpoint + '/asset-manifest.json',
					dataType: 'json'
				}
			).then(
				function (data) {
					Object.values( data.files ).map(
						function (file) {
							var url = myVideoRoomAppEndpoint + '/' + file;

							if (file.endsWith( '.js' )) {
								$.ajax(
									{
										beforeSend: function() {},
										url: url,
										dataType: "script"
									}
								);
							} else if (file.endsWith( '.css' )) {
								$( '<link rel="stylesheet" type="text/css" />' )
									.attr( 'href', url )
									.appendTo( 'head' );
							}
						}
					);
				}
			)
		}
	}
)
