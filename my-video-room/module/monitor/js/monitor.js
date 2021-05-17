/**
 * Main JavaScript file the watcher of the video plugin
 *
 * @package MyVideoRoomPlugin
 */

/*global myvideoroom_monitor_texts*/

(function ( $ ) {

	var $elements = $( '.myvideoroom-monitor' );

	var watch            = {};
	var $indexedElements = {};

	if (Notification.permission !== 'denied') {
		Notification.requestPermission();
	}

	/**
	 * Get the text for an element
	 *
	 * @param {jQuery}   $element
	 * @param {string[]} defaults
	 * @param {string}   name
	 * @return {string}
	 */
	var getText = function ($element, defaults, name) {
		if ($element.data( name )) {
			return $element.data( name );
		} else {
			return defaults[name] || '';
		}
	};

	/**
	 * Update all the monitors with text indicating number of people
	 *
	 * @param {{seatedCount: number, receptionCount:number, clientId: number}} tableData
	 */
	var updateEndpoints = function (tableData) {
		var $element = $indexedElements[tableData.clientId];
		var roomName = $element.data( 'roomName' );

		var text;
		var count;
		var outputText;
		var outputTextPlain;

		switch ($element.data( 'type' )) {
			case 'seated':
				count = tableData.seatedCount;
				text  = myvideoroom_monitor_texts.seated;
				break;
			case 'all':
				count = tableData.userCount;
				text  = myvideoroom_monitor_texts.all;
				break;
			case 'reception':
			default:
				count = tableData.receptionCount;
				text  = myvideoroom_monitor_texts.reception;
				break;
		}

		if (count) {
			if (count > 1) {
				outputText = getText( $element, text, 'textPlural' )
					.replace( /{{count}}/g, count )
					.replace( /{{name}}/g, roomName );

				outputTextPlain = (getText( $element, text, 'textPluralPlain' ) || outputText)
					.replace( /{{count}}/g, count )
					.replace( /{{name}}/g, roomName );
			} else {
				outputText      = getText( $element, text, 'textSingle' )
					.replace( /{{count}}/g, count )
					.replace( /{{name}}/g, roomName );
				outputTextPlain = (getText( $element, text, 'textSinglePlain' ) || outputText)
					.replace( /{{count}}/g, count )
					.replace( /{{name}}/g, roomName );
			}

			if ($element.data( 'type' ) === 'reception' && Notification.permission === 'granted') {
				new Notification( outputTextPlain );
			}
		} else {
			outputText = getText( $element, text, 'textEmpty' );
		}

		if ($element) {
			$element.html( outputText );
		}
	};

	if ($elements.length) {
		$elements.each(
			function (index) {
				var $this    = $( this );
				var endpoint = $this.data( 'serverEndpoint' );

				$indexedElements[index] = $this;

				watch[endpoint] = watch[endpoint] || [];

				watch[endpoint].push(
					{
						videoServerEndpoint: $this.data( 'videoServerEndpoint' ),
						domain: window.location.hostname,
						roomName: $this.data( 'roomName' ),
						roomHash: $this.data( 'roomHash' ),
						securityToken: $this.data( 'securityToken' ),
						clientId: index
					}
				);
			}
		);

		for (var endpoint in watch) {
			if (watch.hasOwnProperty( endpoint )) {
				(function (endpoint) {
					var socket = io(
						endpoint,
						{
							withCredentials: true
						}
					);

					socket.on(
						'connect',
						function () {
							socket.emit( 'watch', watch[endpoint], function () {} );
						}
					);

					socket.on(
						'error',
						function (e) {
							console && console.log && console.log( e );
						}
					);

					socket.on( 'table-data', updateEndpoints );
				})( endpoint );
			}
		}
	}
})( jQuery );
