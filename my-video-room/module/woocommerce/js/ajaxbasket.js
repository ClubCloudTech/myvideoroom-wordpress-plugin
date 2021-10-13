/**
 * Handle Ajax requests for Baskets
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\JS\ajaxbasket.js
 */

(function ($) {
	
	/**
	 * Handle Ajax Click Events for Basket Actions
	 */
	var handleEvent = function (e){
				var product_id     = $( this ).data( 'productId' ),
				record_id          = $( this ).data( 'recordId' ),
				input_type         = $( this ).data( 'inputType' ),
				host_status        = $( this ).data( 'hostStatus' ),
				auth_nonce         = $( this ).data( 'authNonce' ),
				room_name          = $( this ).data( 'roomName' ),
				quantity           = $( this ).data( 'quantity' ),
				variation_id       = $( this ).data( 'variationId' ),
				target_class       = $( this ).data( 'targetClass' ),
				target_window      = $( this ).data( 'target' ),
				container          = $( '.mvr-woocommerce-basket' ),
				notify_confirm     = $( '#mvr-main-basket-confirmation' ),
				basket_window      = $( '#mvr-basket-section' ),
				status_message     = $( '#mvr-postbutton-notification' ),
				ajax_url           = myvideoroom_woocommerce_basket.ajax_url;
				store_manage       = $( '#roommanage-video-host-wrap-table' );
				store_notification = $( '#roommanage-video-notification' );

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
							targetClass: target_class,
							target: target_window
						},
						success: function (response) {
							var response_length = response.length;
							console.log( 'Ajax Clickbased Response Success' );
							var state_response = JSON.parse( response );
							if ( response_length > 40 ) {

								if ( state_response.basketwindow ) {
									refreshTarget( container, state_response.basketwindow, true );
									document.getElementById( "mvr-basket-section" ).classList.remove( 'mvr-clear' );
								}
								if ( state_response.confirmation ) {
									notify_confirm.html( state_response.confirmation );
									basket_window.hide();
									document.getElementById( "mvr-basket-section" ).classList.remove( 'mvr-clear' );
									$( '#mvr-video' ).click();
								}
								if ( state_response.feedback ) {
									status_message.html( state_response.feedback );
									setTimeout( function() { status_message.fadeOut(); }, 7000 );

								}
								if ( state_response.shopconfirmation ) {
									store_notification.html( state_response.shopconfirmation );
									store_manage.hide();
								}

								if ( state_response.shopwindow ) {
									store_manage.hide();
									store_notification.html( state_response.shopwindow );

									console.log( 'Store Manager Refreshed' );
									document.getElementById( "mvr-basket-section" ).classList.remove( 'mvr-clear' );
								}
								if ( state_response.unhide ) {
									showBasket();
								}

								init();
							}
						},
						error: function (){
							console.log( 'Ajax Click Timeout or Error' );
							setTimeout( () => {  triggerRefresh( room_name ); }, 1000 );
						}
					}
				);
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		console.log( 'Ajax Main on click finished' );
		return false;
	}
	
	/**
	 * Heartbeat function that checks Ajax and redraws key Divs
	 */
	function refreshHeartbeat( original_room, message_room ) {
		var ajax_url    = myvideoroom_woocommerce_basket.ajax_url,
		input_type      = 'refresh',
		room_name       = $( '#roominfo' ).data( 'roomName' ),
		last_queuenum   = $( '#roomid' ).data( 'lastQueuenum' ),
		last_carthash   = $( '#roomid' ).data( 'lastCarthash' ),
		last_storecount = $( '#storeid' ).data( 'lastStorecount' ),
		container       = $( '#mvr-basket-section' ),
		notification    = $( '#mvr-notification-master' ),
		storefront      = $( '#basket-video-host-wrap-shop' ),
		mainvideo       = $( '.myvideoroom-app' ),
		videosetting    = $( '#video-host-wrap' ),
		securitysetting = $( '#security-video-host-wrap' ),
		icondisplay     = $( '#mvr-notification-icons' ),
		notify_confirm  = $( '#mvr-main-basket-window' ),
		status_message  = $( '#mvr-postbutton-notification' );

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

							if ( state_response.notificationbar ) {
								notification.html( state_response.notificationbar );
							}
							if ( state_response.basketwindow ) {
								refreshTarget( container, state_response.basketwindow, true );
							}
							if ( state_response.confirmation ) {
								notify_confirm.html( state_response.confirmation );
							}
							if ( state_response.feedback ) {
								status_message.html( state_response.feedback );
								setTimeout( function() { status_message.fadeOut(); }, 7000 );
							}
							if ( state_response.mainwindow ) {
								refreshTarget( container, state_response.mainwindow );
							}
						}

						if (state_response.storestatus == 'change' ) {
							if ( state_response.notificationbar ) {
								notification.html( state_response.notificationbar );
							}
							if ( state_response.storefront) {
								refreshTarget( storefront, state_response.storefront );
								window.myvideoroom_tabbed_init();
							}
							console.log( 'Storefront Updated' );
						}
						if (state_response.messagewindow == 'change' ) {
							if ( state_response.notificationbar ) {
								notification.html( state_response.notificationbar );
							}
						}
						if (state_response.settingchange == 'change' ) {

							if (confirm( 'The Room Host has made a change to the room that will require you to reconnect your call - do you want to do this ?' )) {

								let exitbutton = document.querySelector( "#Reset_app__30V6t > div.Interface_interface__26TVe.undefined > div.Interface_reception__3IXL8 > button" );
								if (exitbutton) {
									exitbutton.click();
								}
								if ( state_response.videosetting ) {
									refreshTarget( videosetting, state_response.videosetting, true );
								}
								if (  state_response.securitysetting ) {
									refreshTarget( securitysetting, state_response.videosetting, true );
								}
								if (  state_response.mainvideo ) {
									refreshTarget( mainvideo, state_response.mainvideo );
								}
								if ( state_response.icons ) {
									refreshTarget( icondisplay, state_response.icons );
								}
								if (window.myvideoroom_tabbed_init) {
									window.myvideoroom_tabbed_init( mainvideo );
								}
							} 
						}
						if (state_response.securitychange == 'change' ) {
							if (  state_response.securitysetting ) {
								refreshTarget( securitysetting, state_response.videosetting, true );
							}
							if ( state_response.icons ) {
								refreshTarget( icondisplay, state_response.icons );
							}
						}

						if (state_response.status == 'nochange') {

						} else {
							init();
						}

					},
					error: function (){
						console.log( 'Ajax Error on Refresh Heartbeat' );
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

	/**
	 * Handles major errors that other pages draw, causes a page reload of major components
	 */
	function triggerRefresh( room_checksum ) {
		console.log( 'Refresh Triggered' );
		var ajax_url = myvideoroom_woocommerce_basket.ajax_url,
		container    = $( '#mvr-basket-section' ),
		input_type   = 'reload',
		room_name    = $( '#roominfo' ).data( 'roomName' ),
		notification = $( '.mvr-notification-master' ),
		storefront   = $( '.mvr-storefront-master' );

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
							if ( state_response.mainwindow ) {
								refreshTarget( container, state_response.mainwindow, true );
							}
							if ( state_response.notificationbar ) {
								notification.html( state_response.notificationbar );
							}
							if ( state_response.storefront) {
								refreshTarget( storefront, state_response.storefront, true );
								window.myvideoroom_tabbed_init( storefront );
							}

							init();
						},
						error: function ( response ){
							setTimeout( () => {  triggerRefresh; }, 1000 );
						}
					}
				);
				return null;
	}

	/**
	 * Refreshes the Notification bar on first page load
	 */
	function notifyRefresh( room_checksum ) {

		var ajax_url   = myvideoroom_woocommerce_basket.ajax_url,
		input_type     = 'notify',
		room_name      = $( '#roominfo' ).data( 'roomName' ),
		notification   = $( '.mvr-notification-master' ),
		status_message = $( '#mvr-postbutton-notification' );

		notification.empty();
		notification.html( '<strong>Welcome to MyVideoRoom</strong>' );

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
						setTimeout( function() { status_message.fadeOut(); }, 7000 );

						if ( state_response.notificationbar ) {
							setTimeout(
								function() { notification.html( state_response.notificationbar );
									init();
								},
								3000
							);
						}
						init();
					},
					error: function ( response ){
						console.log( response );
						setTimeout( () => {  triggerRefresh; }, 1000 );
					}
				}
			);
		return null;
	}
	
	/**
	 * Manages visibility of Basket Tab
	 */
	var showBasket = function(target){
		if ( target ) {
			$( '#' + target ) . show();
			$( this ).closest( 'div' ).removeClass();
			$( this ).closest( 'div' ).empty();
		} else {
			$( '#mvr-basket-section' ).show();
			$( '#mvr-basket-section-confirmation' ).empty();
			$( '#mvr-basket-section-confirmation' ).removeClass();
			document.getElementById( "mvr-basket-section" ).classList.add( 'mvr-clear' );
		}
	}
	
	/**
	 * Reset activities post click/load/interaction.
	 */
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
				let target = $( this ).attr( 'data-target' );
				if (target) {
					$( "#" + target ).click();
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
				let target = $( this ).attr( 'data-target' );
				if ( target === 'roommanage-video-host-wrap-table') {
					$( '#roommanage-video-host-wrap-table' ).show();
					$( this ).closest( 'div' ).remove();

				} else if ( target === 'mvr-shopping-basket' ) {

					$( '#mvr-basket-section' ).show();
					$( this ).closest( 'div' ).remove();
				}
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
						document.getElementById( "shoppingbasket" ).classList.remove( 'mvr-clear' );
					}
				} else if ( this.id == 'mvr-video' ) {
					if ($( '#shoppingbasket' ).length) {
						document.getElementById( "shoppingbasket" ).classList.remove( 'mvr-hide' );
						document.getElementById( "shoppingbasket" ).classList.add( 'mvr-clear' );
					}

				} else {
					if ($( '#shoppingbasket' ).length) {
						document.getElementById( "shoppingbasket" ).classList.add( 'mvr-hide' );
						document.getElementById( "shoppingbasket" ).classList.remove( 'mvr-clear' );
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

	/**
	 * Script Load Decision 
	 */
	
	var mvrIsactive = document.getElementsByClassName( 'mvr-nav-shortcode-outer-wrap' ),
	woocommActive = document.getElementsByClassName( 'basket-video-host-wrap-shop' );

	if ( mvrIsactive.length > 0 && woocommActive.length >0 ) {
		var original_room = $( '#roominfo' ).data( 'roomName' ),
		host_status       = $( '#roomid' ).data( 'hostStatus' );
		if ( host_status !== 1) {
			document.getElementById( "shoppingbasket" ).classList.add( 'mvr-hide' );
		}
		var message_room = $( '.myvideoroom-app' ).data( 'roomName' );
		setInterval( refreshHeartbeat, 6500, original_room, message_room );
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
		init();
	}

})( jQuery );
