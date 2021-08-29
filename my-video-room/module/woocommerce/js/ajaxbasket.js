/**
 * Handle Ajax requests for Baskets
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\JS\ajaxbasket.js
 */

/*global myvideoroom_woocommerce_basket*/

(function ($) {
	var handleEvent = function (e){
				var product_id   = $( this ).data( 'productId' );
				var input_type   = $( this ).data( 'inputType' );
				var host_status  = $( this ).data( 'hostStatus' );
				var auth_nonce   = $( this ).data( 'authNonce' );
				var room_name    = $( this ).data( 'roomName' );
				var quantity      = $( this ).data( 'quantity' );
				var variation_id = $( this ).data( 'variation_id' );	
				
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
							variationId: variation_id 
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
window.myvideoroom_shoppingbasket_init=init;
})( jQuery );
