jQuery('document').ready(function () {
    var cc$ = jQuery.noConflict();

    if (cc$('.clubcloud-video-app').length) {
        cc$.get(clubCloudAppEndpoint + "/asset-manifest.json").then(function (data) {
            Object.values(data.files).map(function (file) {
                if (file.endsWith(".js")) {
                    cc$.getScript(clubCloudAppEndpoint + "/" + file);
                } else if (file.endsWith(".css")) {
                    cc$('<link rel="stylesheet" href="' + clubCloudAppEndpoint + '/' + file + '" type="text/css" />').appendTo('head');
                }
            });
        })
    }
})
