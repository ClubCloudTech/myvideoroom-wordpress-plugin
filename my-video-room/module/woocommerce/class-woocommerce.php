<?php
/**
 * Class WooCommerce- Provides the Integration between MyVideoRoom and WooCommerce.
 *
 * @package file class-WooCommerce.php.
 */

namespace MyVideoRoomPlugin\Module\WooCommerce;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Entity\MenuTabDisplay;
use MyVideoRoomPlugin\Module\Security\Library\SecurityRoomHelpers;
use MyVideoRoomPlugin\Module\Security\Shortcode\SecurityVideoPreference;
use MyVideoRoomPlugin\Module\WooCommerce\Library\ShoppingBasket;

/**
 * Class WooCommerce- Provides the WooCommerce Integration Features for MyVideoRoom.
 */
class WooCommerce {

	const MODULE_WOOCOMMERCE_BASKET_ID = 10561;
	const MODULE_WOOCOMMERCE_NAME      = 'woocommerce-module';
	const MODULE_WOOCOMMERCE_BASKET    = 'woocommerce-basket';
	const MODULE_SECURITY_ADMIN_PAGE   = 'view-admin-settings-security';
	const MODULE_SECURITY_DISPLAY      = ' Advanced Room Permissions';

	/**
	 * Initialise On Module Activation.
	 * Once off functions for activating Module.
	 */
	public function activate_module() {

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

		// add_filter( 'myvideoroom_sitevideo_admin_page_menu', array( $this, 'render_security_sitevideo_tabs' ), 20, 2 );

		// Add Permissions Menu to Main Frontend Template.
		add_filter(
			'myvideoroom_main_template_render',
			array(
				$this,
				'render_shortcode_basket_tab',
			),
			40,
			4
		);
		
		// Add Config Page to Main Room Manager.
		add_filter(
			'myvideoroom_permissions_manager_menu',
			array(
				Factory::get_instance( SecurityRoomHelpers::class ),
				'render_security_admin_settings_page',
			),
			10,
			1
		);

		// Listener for Page Regeneration and Refresh.
		\add_action( 'myvideoroom_page_delete_post_number_refresh', array( Factory::get_instance( SecurityRoomHelpers::class ), 'update_security_post_id' ), 10, 2 );
	}

	/**
	 * Setup of Module Menu
	 */
	public function security_menu_setup() {
		add_action( 'mvr_module_submenu_add', array( $this, 'security_menu_button' ) );
	}

	/**
	 * Render Module Menu.
	 */
	public function security_menu_button() {
		$name = self::MODULE_SECURITY_DISPLAY;
		$slug = self::MODULE_SECURITY_NAME;
		//phpcs:ignore --WordPress.WP.I18n.NonSingularStringLiteralText - $name is a constant text literal already.
		$display = esc_html__( $name, 'myvideoroom' );
		echo '<a class="mvr-menu-header-item" href="?page=my-video-room-extras&tab=' . esc_html( $slug ) . '">' . esc_html( $display ) . '</a>';
	}

	/**
	 * Render Security Admin Page.
	 *
	 * @return string
	 */
	public function render_woocommerce_admin_page(): string {
		return ( require __DIR__ . '/views/view-settings-woocommerce.php' )();
	}

	/**
	 * Render Security Admin Tabs.
	 *
	 * @param array $input   - the inbound menu.
	 * @param int   $room_id - the room identifier.
	 *
	 * @return array - outbound menu.
	 */
	public function render_security_sitevideo_tabs( array $input, int $room_id ): array {
		$room_object = Factory::get_instance( RoomMap::class )->get_room_info( $room_id );

		if ( ! $room_object ) {
			return $input;
		}

		$room_name = $room_object->room_name;

		// Host Menu Tab - rendered in Security as its a module feature of Security.
		$host_menu = new MenuTabDisplay(
			esc_html__( 'Room Hosts', 'my-video-room' ),
			'roomhosts',
			fn() => Factory::get_instance( SecurityVideoPreference::class )
						->choose_settings(
							$room_id,
							$room_name . Dependencies::MULTI_ROOM_HOST_SUFFIX,
							null,
							'roomhost'
						)
		);
		array_push( $input, $host_menu );

		// Permissions Default Tab - rendered in Security as its a module feature of Security.
		$base_menu = new MenuTabDisplay(
			esc_html__( 'Room Permissions', 'my-video-room' ),
			'roompermissions',
			fn() => Factory::get_instance( SecurityVideoPreference::class )->choose_settings(
				$room_id,
				esc_textarea( $room_name ),
				'roomhost'
			)
		);
		array_push( $input, $base_menu );

		return $input;
	}

	/**
	 * Render Shopping Basket in Main Shortcode.
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
				->render_basket( $host_status )
		);
		array_push( $input, $basket_menu );

		return $input;
	}
}
