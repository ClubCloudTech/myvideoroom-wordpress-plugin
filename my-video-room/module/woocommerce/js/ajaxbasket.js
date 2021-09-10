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

				var $container   = $( '.mvr-woocommerce-basket' );
				var loading_text = $container.data( 'loadingText' );

				/*$container.html( loading_text );*/

				var ajax_url = myvideoroom_woocommerce_basket.ajax_url;

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
							recordId: record_id
						},
						success: function (response) {
							var $response_length = response.length;
							if ( $response_length > 40 ) {
								$container.html( response );
							}
							init();
						},
						error: function (){
							setTimeout( () => {  triggerRefresh( room_name ); }, 1000 );
						}
					}
				);
		e.preventDefault();
		return false;
	}

	function refreshHeartbeat( original_room ) {
		var ajax_url        = myvideoroom_woocommerce_basket.ajax_url;
		var input_type      = 'refresh';
		var room_name       = $( '#roomid' ).data( 'roomName' );
		var last_queuenum   = $( '#roomid' ).data( 'lastQueuenum' );
		var last_carthash   = $( '#roomid' ).data( 'lastCarthash' );
		var last_storecount = $( '#storeid' ).data( 'lastStorecount' );
		var $container      = $( '.mvr-woocommerce-basket' );
		var $notification   = $( '.mvr-notification-master' );
		var $storefront     = $( '.mvr-storefront-master' );

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
					},
					success: function (response) {

						var state_response = JSON.parse( response );
						if (state_response.status == 'change' ) {
							$notification.html( state_response.notificationbar + '' );
							$container.html( state_response.mainwindow + '' );


						}
						if (state_response.storestatus == 'change' ) {
							$notification.html( state_response.notificationbar + '' );
							$storefront.html( state_response.storefront + '' );
							
						}

						if (state_response.status == 'nochange') {
						}
						init();
					},
					error: function (){
						setTimeout( () => {  triggerRefresh( room_name ); }, 1000 );
					}
					}
			);
		}


		if ( typeof room_name === 'undefined' && $container ) {
			triggerRefresh( room_name );
		}
		$( '.myvideoroom-button-link' ).on(
			'click',
			mvrchangefocus
		);
		$( '.mvr-main-button-cancel' ).on(
			'click',
			mvrchangefocus
		);
	}

	function triggerRefresh( room_checksum ) {

		var ajax_url   = myvideoroom_woocommerce_basket.ajax_url;
		var $container = $( '.mvr-woocommerce-basket' );
		var input_type = 'reload';
		var room_name  = $( '#roomid' ).data( 'roomName' );
		var $notification   = $( '.mvr-notification-master' );
		var $storefront     = $( '.mvr-storefront-master' );

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
						
							$container.html( state_response.mainwindow + '' );
							$notification.html( state_response.notificationbar + '' );
							$storefront.html( state_response.storefront + '' );
									
						init();
						},
						error: function ( response ){
							setTimeout( () => {  triggerRefresh; }, 1000 );
						}
					}
				);
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
	}

	function notifyRefresh( room_checksum ) {

		var ajax_url   = myvideoroom_woocommerce_basket.ajax_url;
		var input_type = 'reload';
		var room_name  = $( '#roomid' ).data( 'roomName' );
		var $notification   = $( '.mvr-notification-master' );
		

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
											
							$notification.html( state_response.notificationbar + '' );
									
						init();
						},
						error: function ( response ){
							setTimeout( () => {  triggerRefresh; }, 1000 );
						}
					}
				);
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
	}

	/* Disabling Execution outside of MVR */
	var mvrIsactive = document.getElementsByClassName( 'mvr-nav-shortcode-outer-wrap' );

	if ( mvrIsactive.length > 0) {
		var original_room = $( '#roomid' ).data( 'roomName' );
		setInterval( refreshHeartbeat, 6000, original_room );
		notifyRefresh( original_room );
	}

	/* Initialise Runtime */
	window.myvideoroom_shoppingbasket_init = init;
	init();

})( jQuery );
