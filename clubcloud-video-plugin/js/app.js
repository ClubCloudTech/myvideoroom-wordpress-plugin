/**
 * Main JavaScript file the video plugin
 *
 * @package ClubCloudVideoPlugin
 */

jQuery.noConflict()(
	function () {
		var $ = jQuery.noConflict();

		if ($( '.clubcloud-video-app' ).length) {
			if ($.ajaxSettings && $.ajaxSettings.headers) {
				delete $.ajaxSettings.headers;
			}

			$.ajax(
				{
					url: clubCloudAppEndpoint + "/asset-manifest.json",
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
										url: clubCloudAppEndpoint + "/" + file,
										dataType: "script"
									}
								);
							} else if (file.endsWith( ".css" )) {
								$( '<link rel="stylesheet" href="' + clubCloudAppEndpoint + '/' + file + '" type="text/css" />' ).appendTo( 'head' );
							}
						}
					);
				}
			)
		}
	}
)
