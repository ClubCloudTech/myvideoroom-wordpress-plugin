<?php
/**
 * Manages Ajax in the admin pages
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Admin;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Version;

/**
 * Class AdminAjax
 */
class AdminAjax {

	/**
	 * Runtime Shortcodes and Setup
	 * Required for Normal Runtime.
	 */
	public function init() {

		// Enqueue Script Ajax Handling.
		\wp_enqueue_script(
			'mvr-admin-ajax-js',
			\plugins_url( '/js/mvradminajax.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			Factory::get_instance( Version::class )->get_plugin_version() . \wp_rand( 40, 30000 ),
			true
		);
		// Localize script Ajax Upload.
		$script_data_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'mvr_admin_ajax' ),

		);

		wp_localize_script(
			'mvr-admin-ajax-js',
			'myvideoroom_admin_ajax',
			$script_data_array
		);

	}

	/** MyVideoRoom Admin Ajax Support.
	 * Handles ajax calls from backend wp-admin pages
	 *
	 * @return mixed
	 */
	public function myvideoroom_admin_ajax_handler() {
		$response            = array();
		$response['message'] = 'No Change';

		// Security Checks.
		check_ajax_referer( 'mvr_admin_ajax', 'security', false );

		if ( isset( $_POST['action_taken'] ) ) {
			$action_taken = sanitize_text_field( wp_unslash( $_POST['action_taken'] ) );
		}
		if ( isset( $_POST['state'] ) ) {
			$action_state = sanitize_text_field( wp_unslash( $_POST['state'] ) );
		}
		if ( isset( $_POST['module'] ) ) {
			$module = sanitize_text_field( wp_unslash( $_POST['module'] ) );
		}

		/*
		* Create User.
		*
		*/
		if ( 'update_module' === $action_taken ) {

			$button = Factory::get_instance( ModuleConfig::class )->module_activation_button( intval( $module ), $action_state );

				$response['button'] = $button;

			return \wp_send_json( $response );
		}

		die();
	}
}
