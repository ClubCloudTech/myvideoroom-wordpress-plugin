<?php
/**
 * Shopping Basket WooCommerce Functions
 *
 * @package MyVideoRoomPlugin/Module/WooCommerce/ShoppingBasket
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

/**
 * Class Shopping Basket
 * Handles all elements of rendering WooCommerce Shopping Baskets and Broadcasts.
 */
class ShoppingBasket {

	/**
	 * Get the shortcode reference
	 *
	 * @param bool $host_status  Whether user is a host.
	 * @return Void
	 */
	public function render_basket( bool $host_status ) {
		return 'basket';
	}
}
