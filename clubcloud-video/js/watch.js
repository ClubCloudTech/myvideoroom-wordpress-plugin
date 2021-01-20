jQuery('document').ready(function () {
    var cc$ = jQuery.noConflict();

    var $elements = cc$('.clubcloud-video-waiting');

    var watch = {};
    var $indexedElements = {};

    var texts = {
        reception: {
            textEmpty: 'Nobody is currently waiting',
            textSingle: 'One person is waiting in reception',
            textPlural: '{{count}} people are waiting in reception'
        },
        seated: {
            textEmpty: 'Nobody is currently seated',
            textSingle: 'One person is seated',
            textPlural: '{{count}} people are seated'
        },
        all: {
            textEmpty: 'Nobody is currently in this room',
            textSingle: 'One person is currently in this room',
            textPlural: '{{count}} people are currently in this room'
        }
    }

    if (Notification.permission !== "denied") {
        Notification.requestPermission();
    }

    var getText = function ($element, texts, name) {
        if ($element.data(name) ) {
            return atob($element.data(name));
        } else {
            return texts[name];
        }
    }

    var updateEndpoints = function (tableData) {
        var $element = $indexedElements[tableData.clientId];
        var roomName = $element.data('roomName');

        var text;
        var count;
        var outputText;

        switch ($element.data('type')) {
            case 'seated':
                count = tableData.seatedCount;
                text = texts.seated;
                break;
            case 'all':
                count = tableData.userCount;
                text = texts.all;
                break;
            case 'reception':
            default:
                count = tableData.receptionCount;
                text = texts.reception;
                break;
        }


        if (count) {
            if (count > 1) {
                outputText = getText($element, text, 'textPlural').replace('{{count}}', count).replace('{{name}}', roomName);
            } else {
                outputText = getText($element, text, 'textSingle').replace('{{count}}', count).replace('{{name}}', roomName);
            }

            if ($element.data('type') === "reception" && Notification.permission === "granted") {
                new Notification(outputText);
            }
        } else {
            outputText = getText($element, text, 'textEmpty');
        }

        if ($element) {
            $element.html(outputText);
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
