/**
 * Blocks Certain WooCoommerce cart pages from Clicking through browser (opens new window)
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\JS\carthandler.js
 */

  (function ($) {
	$(document).ready(function(){
		$('.woocommerce a').attr('target', '_blank');
	  });	

})( jQuery );