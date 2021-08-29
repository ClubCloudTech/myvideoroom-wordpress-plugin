<?php
/**
 * Class WooCommerce- Provides the Integration between MyVideoRoom and WooCommerce.
 *
 * @package file class-WooCommerce.php.
 */

namespace MyVideoRoomPlugin\Module\WooCommerce;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;

/**
 * Class WooCommerce- Provides the WooCommerce Integration Features for MyVideoRoom.
 */
class WooCommerce {

	const MODULE_WOOCOMMERCE_BASKET_ID        = 10561;
	const MODULE_WOOCOMMERCE_NAME             = 'woocommerce-module';
	const MODULE_WOOCOMMERCE_BASKET           = 'woocommerce-basket';
	const SETTING_REFRESH_BASKET              = 'woocommerce-refresh-basket';
	const SETTING_DELETE_PRODUCT              = 'woocommerce-delete-product';
	const SETTING_DELETE_BASKET               = 'woocommerce-delete-basket';
	const SETTING_DELETE_BASKET_CONFIRMED     = 'woocommerce-delete-basket-confirmed';
	const SETTING_BROADCAST_PRODUCT           = 'woocommerce-broadcast-single-product';
	const SETTING_BROADCAST_PRODUCT_CONFIRMED = 'woocommerce-broadcast-single-product-confirmed';
	const TABLE_NAME_WOOCOMMERCE_CART         = 'myvideoroom_wocommerce_cart_sync';
	const TABLE_NAME_WOOCOMMERCE_ROOM         = 'myvideoroom_wocommerce_room_presence';

	/**
	 * Initialise On Module Activation.
	 * Once off functions for activating Module.
	 */
	public function activate_module() {

		// Create Tables in Database.
		Factory::get_instance( WooCommerceVideoDAO::class )->install_woocommerce_sync_config_table();
		Factory::get_instance( WooCommerceRoomSyncDAO::class )->install_woocommerce_room_presence_table();

		Factory::get_instance( ModuleConfig::class )->register_module_in_db(
			self::MODULE_WOOCOMMERCE_BASKET,
			self::MODULE_WOOCOMMERCE_BASKET_ID,
			true
		);
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_WOOCOMMERCE_BASKET_ID, true );

	}

	/**
	 * De-Initialise On Module De-activation.
	 * Once off functions for activating Module.
	 */
	public function de_activate_module() {

	}

	/**
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function init() {
		// @TODO remove before production.
		\add_shortcode( 'ccproxytest', array( $this, 'proxy_test') );

		// Add Permissions Menu to Main Frontend Template.
		add_filter(
			'myvideoroom_main_template_render',
			array(
				$this,
				'render_shortcode_basket_tab',
			),
			50,
			4
		);

		// Ajax Handler for Basket.
		\add_action( 'wp_ajax_myvideoroom_woocommerce_basket', array( $this, 'get_ajax_page_basketwc' ), 10, 2 );
		\add_action( 'wp_ajax_nopriv_myvideoroom_woocommerce_basket', array( $this, 'get_ajax_page_basketwc' ), 10, 2 );

		\wp_enqueue_script(
			'myvideoroom-woocommerce-basket-js',
			\plugins_url( '/js/ajaxbasket.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			27,
			true
		);

		\wp_localize_script(
			'myvideoroom-woocommerce-basket-js',
			'myvideoroom_woocommerce_basket',
			array( 'ajax_url' => \admin_url( 'admin-ajax.php' ) )
		);

		// Initialise PHPSESSION to track logged out users.
		$this->start_php_session();

	}

	/**
	 * Render WooCommerce Admin Page.
	 *
	 * @return string
	 */
	public function render_woocommerce_admin_page(): string {
		return ( require __DIR__ . '/views/view-settings-woocommerce.php' )();
	}


	/**
	 * Controller Function to Render Shopping Basket in Main Shortcode.
	 *
	 * @param array  $input       - the inbound menu.
	 * @param int    $post_id     - the user or entity identifier.
	 * @param string $room_name   - the room identifier.
	 * @param bool   $host_status - whether function is for a host type.
	 *
	 * @return array - outbound menu.
	 */
	public function render_shortcode_basket_tab( array $input, int $post_id, string $room_name, bool $host_status ): array {

		// Check Activation Status of Basket Module.
		$module_id     = self::MODULE_WOOCOMMERCE_BASKET_ID;
		$module_status = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( $module_id );

		if ( ! $module_status ) {
			return $input;
		}

		$basket_menu = new MenuTabDisplay(
			esc_html__( 'Shopping Basket', 'my-video-room' ),
			'shoppingbasket',
			fn() => Factory::get_instance( ShoppingBasket::class )
				->render_basket( $room_name, $host_status )
		);

		array_push( $input, $basket_menu );
		return $input;

	}

	/**
	 * Get WooCommerce Basket Ajax Data
	 * Handles Ajax Posts for baskets and refreshes the window depending on what was passed into it
	 */
	public function get_ajax_page_basketwc() {

		$product_id   = (int) Factory::get_instance( Ajax::class )->get_text_parameter( 'productId' );
		$input_type   = Factory::get_instance( Ajax::class )->get_text_parameter( 'inputType' );
		$host_status  = Factory::get_instance( Ajax::class )->get_text_parameter( 'hostStatus' );
		$auth_nonce   = Factory::get_instance( Ajax::class )->get_text_parameter( 'authNonce' );
		$room_name    = Factory::get_instance( Ajax::class )->get_text_parameter( 'roomName' );
		$quantity     = Factory::get_instance( Ajax::class )->get_text_parameter( 'quantity' );
		$variation_id = (int) Factory::get_instance( Ajax::class )->get_text_parameter( 'variationId' );

		switch ( $input_type ) {

			// Case Delete a Product from a Basket - has no Confirmation.

			case self::SETTING_DELETE_PRODUCT:
				Factory::get_instance( ShoppingBasket::class )->delete_product_from_cart( $product_id );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			// Case Delete Entire Basket Step1 - Pre Confirmation.

			case self::SETTING_DELETE_BASKET:
				$message                      = \esc_html__( 'clear your basket ?', 'myvideoroom' );
				$delete_basket_nonce          = wp_create_nonce( self::SETTING_DELETE_BASKET_CONFIRMED );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_nav_bar_button( self::SETTING_DELETE_BASKET_CONFIRMED, esc_html__( 'Clear Basket', 'my-video-room' ), $room_name, $delete_basket_nonce );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved );
				break;

			// Case Delete Entire Basket Step2 - Post Confirmation.

			case self::SETTING_DELETE_BASKET_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, self::SETTING_DELETE_BASKET_CONFIRMED ) ){
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					wc()->cart->empty_cart();
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			// Case Broadcast Single Product Step1 - Pre Confirmation.

			case self::SETTING_BROADCAST_PRODUCT:
				$message                      = \esc_html__( 'share this product ?', 'myvideoroom' );
				$broadcast_product_nonce      = wp_create_nonce( self::SETTING_BROADCAST_PRODUCT_CONFIRMED );
				$confirmation_button_approved = Factory::get_instance( ShoppingBasket::class )->basket_product_share_button( self::SETTING_BROADCAST_PRODUCT_CONFIRMED, esc_html__( 'Share Product', 'my-video-room' ), $room_name, $broadcast_product_nonce, $quantity, $product_id, $variation_id );
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->cart_confirmation( $input_type, $room_name, $auth_nonce, $message, $confirmation_button_approved );
				break;

				// Case Delete Entire Basket Step2 - Post Confirmation.
			case self::SETTING_BROADCAST_PRODUCT_CONFIRMED:
				if ( ! wp_verify_nonce( $auth_nonce, self::SETTING_BROADCAST_PRODUCT_CONFIRMED ) ) {
					esc_html_e( 'This Operation is Not Authorised', 'myvideoroom' );

				} else {
					// Broadcast Product Action.
					echo esc_html_e( 'The Product has Been Shared', 'myvideoroom' );
				}
				// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
				break;

			default:
			// phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( ShoppingBasket::class )->render_basket( $room_name, $host_status );
		}
		die();
	}


	/**
	 * Start PHP Session
	 * Starts PHP Session Cookie in case user is signed out.
	 *
	 * @return void
	 */
	public function start_php_session() {

		if ( ! session_id() ) {
			session_start();
		}
	}

	public function proxy_test() {
		Factory::get_instance( WooCommerceRoomSyncDAO::class )->install_woocommerce_room_presence_table();
		Factory::get_instance( WooCommerceVideoDAO::class )->install_woocommerce_sync_config_table();
	}


}
