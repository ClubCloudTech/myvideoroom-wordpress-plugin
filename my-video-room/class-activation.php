<?php
/**
 * Installs and uninstalls the plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Admin\Modules;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Core\SiteDefaults;

/**
 * Class Activation
 */
class Activation {

	const ROLE_GLOBAL_HOST = 'myvideoroom_video_host';

	/**
	 * Activate the plugin
	 */
	public static function activate() {
		$activator = new self();
		Factory::get_instance( SiteDefaults::class )->activate_module();
		$activator->create_roles_and_permissions()
			->enable_default_modules();
	}

	/**
	 * Enable default modules
	 *
	 * @return Activation
	 */
	public function enable_default_modules(): self {
		Module::load_built_in_modules();
		\do_action( 'myvideoroom_init' );

		$room_builder_module = Factory::get_instance( Module::class )->get_module( 'roombuilder' );

		if ( $room_builder_module ) {
			Factory::get_instance( Modules::class )->activate_module( $room_builder_module );
		}

		$elementor_module = Factory::get_instance( Module::class )->get_module( 'elementor' );

		if ( $elementor_module && $elementor_module->is_compatible() ) {
			Factory::get_instance( Modules::class )->activate_module( $elementor_module );
		}

		return $this;
	}

	// ---

	/**
	 * Creates role and caps
	 *
	 * @return Activation
	 */
	public function create_roles_and_permissions(): self {
		\add_role(
			self::ROLE_GLOBAL_HOST,
			\esc_html__( 'Video Default Host', 'myvideoroom' ),
			array( Plugin::CAP_GLOBAL_HOST => true )
		);

		global $wp_roles;
		$default_admins = array( 'administrator' );

		foreach ( $default_admins as $role ) {
			$wp_roles->add_cap( $role, Plugin::CAP_GLOBAL_HOST );
		}

		return $this;
	}

	/**
	 * Remove the plugin
	 */
	public static function deactivate() {
		$activator = new self();
		$activator->delete_roles_and_permissions()
			->delete_options();
	}

	/**
	 * Delete all registered options
	 *
	 * @return Activation
	 */
	public function delete_options(): self {
		\delete_option( Plugin::SETTING_SERVER_DOMAIN );
		\delete_option( Plugin::SETTING_ACTIVATION_KEY );
		\delete_option( Plugin::SETTING_ACTIVATED_MODULES );
		\delete_option( Plugin::SETTING_ACCESS_TOKEN );
		\delete_option( Plugin::SETTING_PRIVATE_KEY );

		return $this;
	}

	/**
	 * Remove roles and caps
	 *
	 * @return Activation
	 */
	public function delete_roles_and_permissions(): self {
		global $wp_roles;

		foreach ( \array_keys( $wp_roles->roles ) as $role ) {
			$wp_roles->remove_cap( $role, Plugin::CAP_GLOBAL_HOST );
		}

		\remove_role( self::ROLE_GLOBAL_HOST );

		return $this;
	}
}
