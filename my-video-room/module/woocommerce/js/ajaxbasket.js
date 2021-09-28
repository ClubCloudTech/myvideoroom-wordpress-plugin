/**
 * Handle Ajax requests for Baskets
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\JS\ajaxbasket.js
 */

(function ($) {
	var handleEvent = function (e){
				var product_id   = $( this ).data( 'productId' );
				var record_id    = $( this ).data( 'recordId' );
				var input_type   = $( this ).data( 'inputType' );
				var host_status  = $( this ).data( 'hostStatus' );
				var auth_nonce   = $( this ).data( 'authNonce' );
				var room_name    = $( this ).data( 'roomName' );
				var quantity     = $( this ).data( 'quantity' );
				var variation_id = $( this ).data( 'variationId' );
				var target_class = $( this ).data( 'targetClass' );
				var container    = $( '.mvr-woocommerce-basket' );
				var ajax_url     = myvideoroom_woocommerce_basket.ajax_url;

				$.ajax(
					{
						type: 'post',
						dataType: 'html',
						url: ajax_url,
						data: {
							action: 'myvideoroom_woocommerce_basket',
							productId: product_id,
							inputType: input_type,
							hostStatus: host_status,
							authNonce: auth_nonce,
							roomName: room_name,
							quantity: quantity,
							variationId: variation_id,
							recordId: record_id,
							targetClass: target_class
						},
						success: function (response) {
							var $response_length = response.length;
							if ( $response_length > 40 ) {

								// Hard Delete of Existing Container to Avoid Duplication.
								container_parent = container.parent().attr( 'id' );
								container.remove();
								container.parent().empty();
								$( '#' + container_parent ).html( response );
								init();

							}

						},
						error: function (){
							console.log( 'timeout reached' );
							setTimeout( () => {  triggerRefresh( room_name ); }, 1000 );
						}
					}
				);
		console.log( 'endajax main on click' );
		e.preventDefault();
		e.stopPropagation();
		return false;
	}

	function refreshHeartbeat( original_room, message_room ) {
		var ajax_url        = myvideoroom_woocommerce_basket.ajax_url;
		var input_type      = 'refresh';
		var room_name       = $( '#roominfo' ).data( 'roomName' );
		var last_queuenum   = $( '#roomid' ).data( 'lastQueuenum' );
		var last_carthash   = $( '#roomid' ).data( 'lastCarthash' );
		var last_storecount = $( '#storeid' ).data( 'lastStorecount' );
		var container       = $( '#mvr-basket-section' );
		var notification    = $( '.mvr-notification-master' );
		var $storefront     = $( '.mvr-storefront-master' );
		var mainvideo       = $( '.myvideoroom-app' );
		var videosetting    = $( '#video-host-wrap' );
		var securitysetting = $( '#security-video-host-wrap' );
		var icondisplay     = $( '#mvr-notification-icons' );

		if ( typeof room_name === 'undefined' ) {
			room_name = original_room;
		} else {
			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: ajax_url,
					data: {
						action: 'myvideoroom_woocommerce_basket',
						inputType: input_type,
						roomName: room_name,
						lastQueuenum: last_queuenum,
						lastCarthash: last_carthash,
						lastStorecount: last_storecount,
						messageRoom: message_room,
					},
					success: function (response) {

						var state_response = JSON.parse( response );
						if (state_response.status == 'change' ) {
							notification.html( state_response.notificationbar );
							// Hard Delete of Existing Container to Avoid Duplication.
							container_parent = container.parent().attr( 'id' );
							container.empty();
							container.parent().empty();
							$( '#' + container_parent ).prepend( '<div id="mvr-basket-section2"></div>' );
							container = $( '#mvr-basket-section2' );
							container.empty();
							container.html( state_response.mainwindow );
						}
						if (state_response.storestatus == 'change' ) {
							notification.html( state_response.notificationbar );
							$storefront.html( state_response.storefront );
						}
						if (state_response.messagewindow == 'change' ) {
							notification.html( state_response.notificationbar );
						}
						if (state_response.settingchange == 'change' ) {

							if (confirm( 'The Room Host has made a change to the room that will require you to reconnect your call - do you want to do this ?' )) {
	
								let exitbutton = document.querySelector("#Reset_app__30V6t > div.Interface_interface__26TVe.undefined > div.Interface_reception__3IXL8 > button");
								if (exitbutton){
									exitbutton.click();
								}
								videosetting.html( state_response.videosetting );
								securitysetting.html( state_response.securitysetting );
								icondisplay.html( state_response.icons );

								// Hard Delete of Existing Container to Avoid Duplication.
								mainvideo_parent = mainvideo.parent().attr( 'id' );
								mainvideo.remove();
								mainvideo.parent().empty();
								$( '#' + mainvideo_parent ).html( state_response.mainvideo );
								mainvideo = $( '.myvideoroom-app' );
								reload();
								if (window.myvideoroom_tabbed_init) {
									window.myvideoroom_tabbed_init( mainvideo );
								}

							} else {
								// Do nothing!

							}

						}
						if (state_response.securitychange == 'change' ) {
							securitysetting.html( state_response.securitysetting );
							icondisplay.html( state_response.icons );
						}

						if (state_response.status == 'nochange') {

						} else {
							init();
						}

					},
					error: function (){
						setTimeout( () => {  triggerRefresh( room_name ); }, 1000 );
					}
				}
			);
		}

		if ( typeof room_name === 'undefined' && container ) {
			triggerRefresh( room_name );
		}

		return null;
	}

	function triggerRefresh( room_checksum ) {

		var ajax_url     = myvideoroom_woocommerce_basket.ajax_url;
		var container    = $( '.mvr-woocommerce-basket' );
		var input_type   = 'reload';
		var room_name    = $( '#roominfo' ).data( 'roomName' );
		var notification = $( '.mvr-notification-master' );
		var $storefront  = $( '.mvr-storefront-master' );

		if ( typeof room_name === 'undefined' ) {
			room_name = room_checksum;
		}
				$.ajax(
					{
						type: 'post',
						dataType: 'html',
						url: ajax_url,
						data: {
							action: 'myvideoroom_woocommerce_basket',
							inputType: input_type,
							roomName: room_name,
						},
						success: function (response) {
							var state_response = JSON.parse( response );

							// Hard Delete of Existing Container to Avoid Duplication.
							container_parent = container.parent().attr( 'id' );
							container.remove();
							container.parent().empty();
							$( '#' + container_parent ).prepend( '<div id="mvr-basket-section" class="mvr-nav-settingstabs-outer-wrap mvr-woocommerce-basket myvideoroom-welcome-page"></div>' );
							$( '#mvr-basket-section' ).html( state_response.mainwindow );
							console.log( 'triggerRefreshDiv' );

							notification.html( state_response.notificationbar );
							$storefront.html( state_response.storefront );
							init();
						},
						error: function ( response ){
							setTimeout( () => {  triggerRefresh; }, 1000 );
						}
					}
				);
				return null;
	}

	function notifyRefresh( room_checksum ) {

		var ajax_url     = myvideoroom_woocommerce_basket.ajax_url;
		var input_type   = 'reload';
		var room_name    = $( '#roominfo' ).data( 'roomName' );
		var notification = $( '.mvr-notification-master' );

		if ( typeof room_name === 'undefined' ) {
			room_name = room_checksum;
		}
				$.ajax(
					{
						type: 'post',
						dataType: 'html',
						url: ajax_url,
						data: {
							action: 'myvideoroom_woocommerce_basket',
							inputType: input_type,
							roomName: room_name,
						},
						success: function (response) {
							var state_response = JSON.parse( response );

							notification.html( state_response.notificationbar );
							init();
						},
						error: function ( response ){
							setTimeout( () => {  triggerRefresh; }, 1000 );
						}
					}
				);
		return null;
	}

	var init = function(){
		
		$( '.myvideoroom-woocommerce-basket-ajax' ).on(
			'click',
			handleEvent
		);

		$( document.body ).on(
			'updated_cart_totals',
			handleEvent
		);

		$( ".myvideoroom-button-link" ).click(
			function(event){
				event.stopPropagation();
				event.preventDefault();
				event.stopImmediatePropagation();
				if ($( '#basket-video-host-wrap-item' ).length) {
					document.getElementById( "basket-video-host-wrap-item" ).classList.add( 'mvr-shopping-basket-frame' );
				}
				if ($( '#mvr-basket-section' ).length) {
					document.getElementById( "mvr-basket-section" ).classList.remove( 'mvr-clear' );
				}
				if ($( '#shoppingbasket' ).length) {
					document.getElementById( "shoppingbasket" ).classList.remove( 'mvr-hide' );
				}
			}
		);

		$( ".myvideoroom-button-dismiss" ).click(
			function(event){
				event.stopPropagation();
				event.preventDefault();
				event.stopImmediatePropagation();
				let dismiss = $( this ).closest( 'button' ).attr( 'id' );
				$( "#" + dismiss ).fadeOut( 'slow' );

			}
		);
		$( ".mvr-main-button-cancel" ).click(
			function(event){
				event.stopPropagation();
				event.preventDefault();

				$( '#mvr-video' ).click();
			}
		);
		$( ".mvr-main-button-enabled" ).click(
			function(event){
				event.stopPropagation();
				event.preventDefault();

				$( '#mvr-video' ).click();
			}
		);

		$( '.nav-tab' ).click(
			function(event) {

				if ( this.id == 'mvr-shopping-basket' ) {

					if ($( '#basket-video-host-wrap-item' ).length) {
						document.getElementById( "basket-video-host-wrap-item" ).classList.add( 'mvr-shopping-basket-frame' );
					}
					if ($( '#mvr-basket-section' ).length) {
						document.getElementById( "mvr-basket-section" ).classList.remove( 'mvr-clear' );
					}
					if ($( '#shoppingbasket' ).length) {
						document.getElementById( "shoppingbasket" ).classList.remove( 'mvr-hide' );
					}
				} else if ( this.id == 'mvr-video' ) {
					if ($( '#shoppingbasket' ).length) {
						document.getElementById( "shoppingbasket" ).classList.remove( 'mvr-hide' );
					}
				
				} else {
					if ($( '#shoppingbasket' ).length) {
						document.getElementById( "shoppingbasket" ).classList.add( 'mvr-hide' );
					}
					if ($( '#basket-video-host-wrap-item' ).length) {
						document.getElementById( "basket-video-host-wrap-item" ).classList.remove( 'mvr-shopping-basket-frame' );
					}
					if ($( '#mvr-basket-section' ).length) {
						document.getElementById( "mvr-basket-section" ).classList.add( 'mvr-clear' );
					}
					if ($( '#shoppingbasket' ).length) {
						document.getElementById( "shoppingbasket" ).classList.add( 'mvr-hide' );
					}
				}
				event.preventDefault();
			}
		);

		return false;
	}

	function reload() {

				// WordPress may add custom headers to the request, this is likely to trigger CORS issues, so we remove them.
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
											beforeSend: function () {},
											url: url,
											dataType: 'script'
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
				);
				$( '#mvr-video' ).click();
	}

	/* Disabling Execution outside of MVR */
	var mvrIsactive = document.getElementsByClassName( 'mvr-nav-shortcode-outer-wrap' );

	if ( mvrIsactive.length > 0) {
		var original_room = $( '#roominfo' ).data( 'roomName' ),
		host_status       = $( '#roomid' ).data( 'hostStatus' );
		if ( host_status !== 1) {
			document.getElementById( "shoppingbasket" ).classList.add( 'mvr-hide' );
		}
		var message_room = $( '.myvideoroom-app' ).data( 'roomName' );
		setInterval( refreshHeartbeat, 6000, original_room, message_room );
		notifyRefresh( original_room );

		$( '.ajaxsecurity' ).submit(
			function () {

				if (confirm( "Changing this setting will immediately apply it and you must reconnect to the room, do you want to do this ?" )) {
					return true;
				} else {
					return false;
				}
			}
		);

		$( '.ajaxvideosettings' ).submit(
			function () {

				if (confirm( "Changing Video Settings will immediately apply them and you must reconnect to the room, do you want to do this ?" )) {
					 return true;
				} else {
					return false;
				}
			}
		);
	}
	init();

})( jQuery );
