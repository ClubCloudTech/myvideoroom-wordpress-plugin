/**
 * Main JavaScript file the video plugin
 *
 * @package MyVideoRoomPlugin
 */

jQuery.noConflict()(
	function () {
		var $ = jQuery.noConflict();

		if ($( '.myvideoroom-app' ).length) {
			if ($.ajaxSettings && $.ajaxSettings.headers) {
				delete $.ajaxSettings.headers;
			}

			$.ajax(
				{
					url: myVideoRoomAppEndpoint + "/asset-manifest.json",
					dataType: 'json'
				}
			).then(
				function (data) {
					Object.values( data.files ).map(
						function (file) {
							if (file.endsWith( ".js" )) {
								$.ajax(
									{
										beforeSend: function() {},
										url: myVideoRoomAppEndpoint + "/" + file,
										dataType: "script"
									}
								);
							} else if (file.endsWith( ".css" )) {
								$( '<link rel="stylesheet" href="' + myVideoRoomAppEndpoint + '/' + file + '" type="text/css" />' ).appendTo( 'head' );
							}
						}
					);
				}
			)
		}
	}
)
