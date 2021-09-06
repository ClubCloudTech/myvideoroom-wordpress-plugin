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
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Library\AjaxHandler;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;

/**
 * Class WooCommerce- Provides the WooCommerce Integration Features for MyVideoRoom.
 */
class WooCommerce {

	const MODULE_WOOCOMMERCE_BASKET_ID           = 10561;
	const SETTING_HEARTBEAT_THRESHOLD            = 12;
	const SETTING_TOLERANCE_FOR_LAST_ACTIVE      = 30 * 60; /* minutes x seconds */
	const MODULE_WOOCOMMERCE_NAME                = 'woocommerce-module';
	const MODULE_WOOCOMMERCE_BASKET              = 'woocommerce-basket';
	const SETTING_REFRESH_BASKET                 = 'woocommerce-refresh-basket';
	const SETTING_DELETE_PRODUCT                 = 'woocommerce-delete-product';
	const SETTING_ADD_PRODUCT                    = 'woocommerce-add-product';
	const SETTING_DELETE_PRODUCT_QUEUE           = 'woocommerce-delete-product-queue';
	const SETTING_DELETE_PRODUCT_QUEUE_CONFIRMED = 'woocommerce-delete-product-queue-confirmed';
	const SETTING_DELETE_BASKET                  = 'woocommerce-delete-basket';
	const SETTING_DELETE_BASKET_CONFIRMED        = 'woocommerce-delete-basket-confirmed';
	const SETTING_BROADCAST_PRODUCT              = 'woocommerce-broadcast-single-product';
	const SETTING_BROADCAST_PRODUCT_CONFIRMED    = 'woocommerce-broadcast-single-product-confirmed';
	const TABLE_NAME_WOOCOMMERCE_CART            = 'myvideoroom_wocommerce_cart_sync';
	const TABLE_NAME_WOOCOMMERCE_ROOM            = 'myvideoroom_wocommerce_room_presence';

	const SETTING_ACCEPT_ALL_QUEUE           = 'accept-all';
	const SETTING_ACCEPT_ALL_QUEUE_CONFIRMED = 'accept-all-confirmed';
	const SETTING_REJECT_ALL_QUEUE           = 'reject-all';
	const SETTING_REJECT_ALL_QUEUE_CONFIRMED = 'reject-all-confirmed';

	const SETTING_ENABLE_SYNC            = 'enable-sync';
	const SETTING_ENABLE_SYNC_CONFIRMED  = 'enable-sync-confirmed';
	const SETTING_DISABLE_SYNC           = 'disable-sync';
	const SETTING_DISABLE_SYNC_CONFIRMED = 'disable-sync-confirmed';

	const SETTING_REQUEST_MASTER           = 'request-master';
	const SETTING_REQUEST_MASTER_PENDING   = 'request-master-pending';
	const SETTING_REQUEST_MASTER_CONFIRMED = 'request-master-confirmed';

	const SETTING_REQUEST_MASTER_DECLINED_PENDING = 'request-master-declined-pending';
	const SETTING_REQUEST_MASTER_DECLINED         = 'request-master-declined';

	const SETTING_REQUEST_MASTER_APPROVED_PENDING = 'request-master-approved-pending';
	const SETTING_REQUEST_MASTER_APPROVED         = 'request-master-approved';

	const SETTING_REQUEST_MASTER_WITHDRAW_PENDING = 'request-master-withdraw-pending';
	const SETTING_REQUEST_MASTER_WITHDRAW         = 'request-master-withdraw';

	const SETTING_ENABLE_BASKET_DOWNLOAD            = 'enable-sync-dowload';
	const SETTING_ENABLE_BASKET_DOWNLOAD_CONFIRMED  = 'enable-sync-download-confirmed';
	const SETTING_DISABLE_BASKET_DOWNLOAD           = 'disable-sync-download';
	const SETTING_DISABLE_BASKET_DOWNLOAD_CONFIRMED = 'disable-sync-download-confirmed';


	const SETTING_BASKET_REQUEST_OFF     = 'br-off';
	const SETTING_BASKET_REQUEST_ON      = 'br-enabled-and-on';
	const SETTING_BASKET_REQUEST_PENDING = 'br-pending';
	const SETTING_BASKET_REQUEST_USER    = 'br-user-placeholder';


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

		// Add Basket Menu to Main Frontend Templates.
		add_filter(
			'myvideoroom_main_template_render',
			array(
				$this,
				'render_shortcode_basket_tab',
			),
			50,
			4
		);
		\wp_enqueue_script(
			'myvideoroom-woocommerce-basket-js',
			\plugins_url( '/js/ajaxbasket.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			163,
			true
		);

		wp_enqueue_script(
			'myvideoroom-notification-buttons',
			plugins_url( '/../../js/notification.js', __FILE__ ),
			array( 'jquery' ),
			117,
			true
		);

		// Add Notification Bar to Video Call.
		\add_action( 'myvideoroom_notification_master', array( Factory::get_instance( ShoppingBasket::class ), 'render_notification_tab' ), 10, 2 );

		// Ajax Handler for Basket.
		\add_action( 'wp_ajax_myvideoroom_woocommerce_basket', array( Factory::get_instance( AjaxHandler::class ), 'get_ajax_page_basketwc' ), 10, 2 );
		\add_action( 'wp_ajax_nopriv_myvideoroom_woocommerce_basket', array( Factory::get_instance( AjaxHandler::class ), 'get_ajax_page_basketwc' ), 10, 2 );

		\wp_enqueue_script( 'myvideoroom-admin-tabs' );

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
