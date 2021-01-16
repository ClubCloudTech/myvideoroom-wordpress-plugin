jQuery('document').ready(function () {
    var cc$ = jQuery.noConflict();

    var $elements = cc$('.clubcloud-video-waiting');

    var watch = {};
    var $indexedElements = {};

    var textEmpty = 'Nobody is currently waiting';
    var textSingle = 'One person is waiting in reception';
    var textPlural = '{{count}} people are waiting in reception';

    var updateEndpoints = function (tableData) {
        var $element = $indexedElements[tableData.clientId];

        if ($element) {
            var receptionCount = tableData.users.filter(function (item) {
                return item.inReception === true
            }).length;

            if (receptionCount > 1) {
                $element.html(($element.data('textPlural') || textPlural).replace('{{count}}', receptionCount));
            } else if (receptionCount === 1) {
                $element.html(($element.data('textSingle') || textSingle).replace('{{count}}', receptionCount));
            } else {
                $element.html($element.data('textEmpty') || textEmpty);
            }
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
