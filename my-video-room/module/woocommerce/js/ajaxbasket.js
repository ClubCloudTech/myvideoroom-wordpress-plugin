/**
 * Handle Ajax requests for Baskets
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\JS\ajaxbasket.js
 */

/*global myvideoroom_woocommerce_basket*/

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
	
				$container.html( loading_text );
	
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
							$container.html( response );
				init();	
						}
					}
				);
				e.preventDefault();
				return false;
	}

	function refreshHeartbeat() {

	var ajax_url = myvideoroom_woocommerce_basket.ajax_url;
	var input_type   = 'refresh';
	var room_name    = $( '#roomid' ).data( 'roomName' );
	var last_queuenum = $( '#roomid' ).data( 'lastQueuenum' );
	var last_carthash = $( '#roomid' ).data( 'lastCarthash' );
	
	if ( room_name ) {
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
				},
				success: function (response) {

					var state_response = JSON.parse(response);
					if (state_response.status == 'change') {
						triggerRefresh();
					} 
					
					if (state_response.status == 'nochange') {

					} 
				}
			}
		);
	}
	
	}

	function triggerRefresh() {
		
		var ajax_url = myvideoroom_woocommerce_basket.ajax_url;
		var $container   = $( '.mvr-woocommerce-basket' );
		var input_type   = 'reload';
		var room_name    = $( '#roomid' ).data( 'roomName' );
			
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
							$container.html( response );
							init();
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
init();
setInterval( refreshHeartbeat, 6000 );

window.myvideoroom_shoppingbasket_init=init;
})( jQuery );
