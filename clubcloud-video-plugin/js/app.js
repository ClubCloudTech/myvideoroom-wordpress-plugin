/**
 * Main JavaScript file the video plugin
 *
 * @package ClubCloudVideoPlugin
 */

jQuery.noConflict()(
	function () {
		var cc$ = jQuery.noConflict();

		if (cc$( '.clubcloud-video-app' ).length) {
			if (cc$.ajaxSettings && cc$.ajaxSettings.headers) {
				delete cc$.ajaxSettings.headers;
			}

			cc$.ajax(
				{
					url: clubCloudAppEndpoint + "/asset-manifest.json",
					dataType: 'json'
				}
			).then(
				function (data) {
					Object.values( data.files ).map(
						function (file) {
							if (file.endsWith( ".js" )) {
								cc$.ajax(
									{
										beforeSend: function() {},
										url: clubCloudAppEndpoint + "/" + file,
										dataType: "script"
									}
								);
							} else if (file.endsWith( ".css" )) {
								cc$( '<link rel="stylesheet" href="' + clubCloudAppEndpoint + '/' + file + '" type="text/css" />' ).appendTo( 'head' );
							}
						}
					);
				}
			)
		}
	}
)
