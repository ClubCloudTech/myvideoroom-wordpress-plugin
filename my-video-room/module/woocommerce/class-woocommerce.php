<?php
/**
 * Class WooCommerce- Provides the Integration between MyVideoRoom and WooCommerce.
 *
 * @package file class-WooCommerce.php.
 */

namespace MyVideoRoomPlugin\Module\WooCommerce;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\DAO\Setup;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Library\SectionTemplates;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceRoomSyncDAO;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;
use MyVideoRoomPlugin\Module\WooCommerce\Library\AjaxHandler;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShopView;
use MyVideoRoomPlugin\Module\WooCommerce\Library\WooCategory;

/**
 * Class WooCommerce- Provides the WooCommerce Integration Features for MyVideoRoom.
 */
class WooCommerce {

	const MODULE_WOOCOMMERCE_BASKET_ID           = 10561;
	const SETTING_HEARTBEAT_THRESHOLD            = 12;
	const SETTING_TOLERANCE_FOR_LAST_ACTIVE      = 10 * 60; /* minutes x seconds */
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
	const MAIN_CATEGORY_DISPLAY                  = 'MyVideoRoom Parent Store Category';

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

	const SETTING_SAVE_PRODUCT_CATEGORY           = 'save-product-category';
	const SETTING_SAVE_PRODUCT_CATEGORY_CONFIRMED = 'save-product-category-confirmed';


	/**
	 * Initialise On Module Activation.
	 * Once off functions for activating Module.
	 */
	public function activate_module() {

		// Create Tables in Database.
		Factory::get_instance( WooCommerceVideoDAO::class )->install_woocommerce_sync_config_table();

		Factory::get_instance( ModuleConfig::class )->register_module_in_db(
			self::MODULE_WOOCOMMERCE_BASKET,
			self::MODULE_WOOCOMMERCE_BASKET_ID,
			true
		);
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_WOOCOMMERCE_BASKET_ID, true );

		// Create Product Categories for each room.
		Factory::get_instance( WooCategory::class )->activate_product_category();

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
		if ( ! $this->is_woocommerce_active() ) {
			return null;
		}

		// @TODO remove before production.
		\add_shortcode( 'ccproxytest', array( $this, 'proxy_test' ) );

		// Add Basket Menu to Main Frontend Templates.
		add_filter(
			'myvideoroom_main_template_render',
			array(
				$this,
				'render_shortcode_store_tab',
			),
			40,
			5
		);

				// Add Basket Menu to Main Frontend Templates.
		add_filter(
			'myvideoroom_main_template_render',
			array(
				$this,
				'render_shortcode_basket_tab',
			),
			50,
			5
		);

		\wp_enqueue_script(
			'myvideoroom-woocommerce-basket-js',
			\plugins_url( '/js/ajaxbasket.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			wp_rand( 10, 10000 ),
			true
		);

		\wp_enqueue_script(
			'myvideoroom-woocommerce-carthandler',
			\plugins_url( '/js/carthandler.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			13,
			true
		);
		/*
		wp_enqueue_script(
			'myvideoroom-notification-buttons',
			plugins_url( '/../../js/notification.js', __FILE__ ),
			array( 'jquery' ),
			148,
			true
		);*/

		add_filter( 'myvideoroom_basket_buttons', array( Factory::get_instance( WooCategory::class ), 'render_save_category_button' ), 30, 3 );

		// Add Notification Bar to Video Call.
		\add_filter( 'myvideoroom_notification_master', array( Factory::get_instance( ShoppingBasket::class ), 'render_notification_tab' ), 100, 2 );

		// Ajax Handler for Basket Management.
		\add_action( 'wp_ajax_myvideoroom_woocommerce_basket', array( Factory::get_instance( AjaxHandler::class ), 'get_ajax_page_basketwc' ), 10, 2 );
		\add_action( 'wp_ajax_nopriv_myvideoroom_woocommerce_basket', array( Factory::get_instance( AjaxHandler::class ), 'get_ajax_page_basketwc' ), 10, 2 );

		// Ajax Handler for Add to Basket Button.
		// add_action( 'wp_ajax_woocommerce_ajax_add_to_cart', array( Factory::get_instance( CartAjax::class ), 'woocommerce_ajax_add_to_cart' ) );
		// add_action( 'wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array( Factory::get_instance( CartAjax::class ), 'woocommerce_ajax_add_to_cart' ) );

		\wp_enqueue_script( 'myvideoroom-admin-tabs' );

		\wp_localize_script(
			'myvideoroom-woocommerce-basket-js',
			'myvideoroom_woocommerce_basket',
			array( 'ajax_url' => \admin_url( 'admin-ajax.php' ) )
		);

		add_action( 'myvideoroom_post_room_create', array( Factory::get_instance( WooCategory::class ), 'create_product_category' ), 10, 2 );

		// Initialise PHPSESSION to track logged out users.
		$this->start_php_session();

	}

	/**
	 * Is WooCommerce Active - checks if WooCommerce is enabled.
	 *
	 * @return bool
	 */
	public function is_woocommerce_active(): bool {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		return is_plugin_active( 'woocommerce/woocommerce.php' );
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
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_SHOPPING_BASKET ),
			'shoppingbasket',
			fn() => Factory::get_instance( ShoppingBasket::class )
				->render_basket( $room_name, $host_status ),
			'mvr-shopping-basket'
		);

		array_push( $input, $basket_menu );
		return $input;

	}

	/**
	 * Controller Function to Render Shopping Store by room in Main Shortcode.
	 *
	 * @param array  $input       - the inbound menu.
	 * @param int    $post_id     - the user or entity identifier.
	 * @param string $room_name   - the room identifier.
	 * @param bool   $host_status - whether function is for a host type.
	 *
	 * @return array - outbound menu.
	 */
	public function render_shortcode_store_tab( array $input, int $post_id = null, string $room_name, bool $host_status ): array {

		// Check Activation Status of Basket Module.
		$module_id     = self::MODULE_WOOCOMMERCE_BASKET_ID;
		$module_status = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( $module_id );

		if ( ! $module_status ) {
			return $input;
		}

		$basket_menu = new MenuTabDisplay(
			Factory::get_instance( SectionTemplates::class )->template_icon_switch( SectionTemplates::TAB_STOREFRONT ),
			'storefront',
			fn() => Factory::get_instance( ShopView::class )
				->show_shop( $room_name, $host_status ),
			'mvr-shop'
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

	/**
	 * For Testing Purposes.
	 * Starts PHP Session Cookie in case user is signed out.
	 *
	 * @return void
	 */
	public function proxy_test() {
		Factory::get_instance( Setup::class )->install_room_presence_table();

	}


}
