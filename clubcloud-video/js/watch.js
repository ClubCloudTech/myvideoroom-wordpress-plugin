jQuery('document').ready(function () {
    var cc$ = jQuery.noConflict();

    var $elements = cc$('.clubcloud-video-waiting');

    var watch = {};
    var $indexedElements = {};

    var textEmpty = 'Nobody is currently waiting';
    var textSingle = 'One person is waiting in reception';
    var textPlural = '{{count}} people are waiting in reception';

    if (Notification.permission !== "denied") {
        Notification.requestPermission();
    }

    var updateEndpoints = function (tableData) {
        var $element = $indexedElements[tableData.clientId];

        var receptionCount = tableData.users.filter(function (item) {
            return item.inReception === true
        }).length;

        var text;

        if (receptionCount) {
            if (receptionCount > 1) {
                text = ($element.data('textPlural') || textPlural).replace('{{count}}', receptionCount);
            } else {
                text = ($element.data('textSingle') || textSingle).replace('{{count}}', receptionCount);
            }

            if (Notification.permission === "granted") {
                new Notification(text);
            }
        } else {
            text = ($element.data('textEmpty') || textEmpty);
        }

        if ($element) {
            $element.html(text);
        }
    }

    if ($elements.length) {
        $elements.each(function (index) {
            var $this = cc$(this);
            var endpoint = $this.data('serverEndpoint');

            $indexedElements[index] = $this;

            watch[endpoint] = watch[endpoint] || [];

            watch[endpoint].push({
                videoServerEndpoint: $this.data('videoServerEndpoint'),
                domain: window.location.hostname,
                roomName: $this.data('roomName'),
                securityToken: $this.data('securityToken'),
                clientId: index
            });
        })

        for (var endpoint in watch) {
            if (watch.hasOwnProperty(endpoint)) {
                (function (endpoint) {
                    var socket = io(endpoint, {
                        withCredentials: true
                    });

                    socket.on("connect", function () {
                        socket.emit('watch', watch[endpoint], function () {});
                    });

                    socket.on('table-data', updateEndpoints);
                })(endpoint)
            }
        }
    }
});
