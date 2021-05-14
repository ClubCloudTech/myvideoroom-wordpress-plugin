<?php
/**
 * Manages modules in the admin page
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Admin;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Admin\Navigation;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\Module as ModuleInstance;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Class Admin
 */
class Modules {

	const MODULE_ACTION_ACTIVATE   = 'activate';
	const MODULE_ACTION_DEACTIVATE = 'deactivate';

	/**
	 * Update the activate modules
	 *
	 * @return ?Notice
	 */
	public function update_active_modules(): ?Notice {
		$page        = sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) );
		$module_slug = sanitize_text_field( wp_unslash( $_GET['module'] ?? '' ) );
		$action      = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );

		if ( Navigation::PAGE_SLUG_MODULES !== $page || ! $module_slug || ! $action ) {
			return null;
		}

		$module = Factory::get_instance( Module::class )->get_module( $module_slug );

		if ( ! $module ) {
			return null;
		}

		check_admin_referer( 'module_' . $action );

		switch ( $action ) {
			case self::MODULE_ACTION_ACTIVATE:
				$activation_status = $this->activate_module( $module );

				if ( $activation_status ) {
					return new Notice(
						Notice::TYPE_SUCCESS,
						esc_html__( 'Module activated', 'myvideoroom' ),
					);
				} else {
					return new Notice(
						Notice::TYPE_ERROR,
						esc_html__( 'Module activation failed', 'myvideoroom' ),
					);
				}
			case self::MODULE_ACTION_DEACTIVATE:
				$activation_status = $this->deactivate_module( $module );

				if ( $activation_status ) {
					return new Notice(
						Notice::TYPE_SUCCESS,
						esc_html__( 'Module deactivated', 'myvideoroom' ),
					);
				} else {
					return new Notice(
						Notice::TYPE_ERROR,
						esc_html__( 'Module deactivation failed', 'myvideoroom' ),
					);
				}
		}

		return null;
	}


	/**
	 * Activate a module
	 *
	 * @param ModuleInstance $module The module to activate.
	 *
	 * @return boolean
	 */
	public function activate_module( ModuleInstance $module ): bool {
		$all_modules       = Factory::get_instance( Module::class )->get_all_modules();
		$activation_status = $module->activate();

		if ( ! $activation_status ) {
			return false;
		}

		$module->set_as_active();

		$activated_modules = array_keys( array_filter( $all_modules, fn( $module ) => $module->is_active() ) );

		update_option( Plugin::SETTING_ACTIVATED_MODULES, wp_json_encode( $activated_modules ) );

		$module->instantiate();

		return true;
	}

	/**
	 * Deactivate a module
	 *
	 * @param ModuleInstance $module The module to deactivate.
	 *
	 * @return boolean
	 */
	private function deactivate_module( ModuleInstance $module ): bool {
		$all_modules         = Factory::get_instance( Module::class )->get_all_modules();
		$deactivation_status = $module->deactivate();

		if ( ! $deactivation_status ) {
			return false;
		}

		$module->set_as_inactive();

		$activated_modules = array_keys( array_filter( $all_modules, fn( $module ) => $module->is_active() ) );

		update_option( Plugin::SETTING_ACTIVATED_MODULES, wp_json_encode( $activated_modules ) );

		return true;
	}
}
