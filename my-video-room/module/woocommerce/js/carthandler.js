/**
 * Blocks Certain WooCoommerce cart pages from Clicking through browser (opens new window)
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\JS\carthandler.js
 */

  (function ($) {


	if ($( '.mvr-woocommerce-basket' ).length >0 || $( '.mvr-notification-master' ).length >0 || $( '.mvr-storefront-master' ).length >0){
		$(document).ready(function(){
			$('.woocommerce a').attr('target', '_blank');
		  });	
	}
	

})( jQuery );