<?php
/**
 * Extends the room builder with advanced permissions
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\AdvancedPermissions;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\Post;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\RoomPermissionsOption;

/**
 * Class Module
 */
class RoomBuilder {

	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * RoomBuilder constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', fn() => $this->enqueue_scripts_and_styles(), );
		add_action( 'admin_enqueue_scripts', fn() => $this->enqueue_scripts_and_styles(), );

		add_filter( 'myvideoroom_roombuilder_create_shortcode', array( $this, 'generate_shortcode_constructor' ), 0, 1 );
		add_filter( 'myvideoroom_roombuilder_permission_options', array( $this, 'add_permissions_option' ) );
		add_filter( 'myvideoroom_roombuilder_permission_options_selected', array( $this, 'ensure_correct_permission_is_selected' ) );

		add_action( 'myvideoroom_roombuilder_permission_section', array( $this, 'add_permission_section' ) );
	}

	/**
	 * Enqueue required scripts and styles
	 */
	private function enqueue_scripts_and_styles() {
		$plugin_version = Factory::get_instance( Version::class )->get_plugin_version();

		wp_enqueue_style(
			'myvideoroom-advancedpermissions-roombuilder-css',
			plugins_url( '/css/roombuilder.css', realpath( __FILE__ ) ),
			false,
			$plugin_version,
		);

		wp_enqueue_script(
			'myvideoroom-advancedpermissions-roombuilder-js',
			plugins_url( '/js/roombuilder.js', realpath( __FILE__ ) ),
			array( 'jquery' ),
			$plugin_version,
			true
		);
	}

	/**
	 * Add an option for advanced settings to the room builder permissions section
	 *
	 * @param RoomPermissionsOption[] $options The current permissions options.
	 *
	 * @return RoomPermissionsOption[]
	 */
	public function add_permissions_option( array $options ): array {
		$permissions_preference   = Factory::get_instance( Post::class )->get_radio_post_parameter( 'room_permissions_preference' );
		$use_advanced_permissions = ( 'use_advanced_permissions' === $permissions_preference );

		$options[] = new RoomPermissionsOption(
			'use_advanced_permissions',
			$use_advanced_permissions,
			__( 'Use advanced permissions', 'myvideoroom' ),
			esc_html__(
				'This will allow you to select which users or roles will be an admin for this shortcode.',
				'myvideoroom'
			),
		);

		return $options;
	}

	/**
	 * Ensure correct permission is selected
	 *
	 * @param RoomPermissionsOption[] $options The current permissions options.
	 *
	 * @return RoomPermissionsOption[]
	 */
	public function ensure_correct_permission_is_selected( array $options ): array {
		$permissions_preference   = Factory::get_instance( Post::class )->get_radio_post_parameter( 'room_permissions_preference' );
		$use_advanced_permissions = ( 'use_advanced_permissions' === $permissions_preference );

		foreach ( $options as $permission ) {
			if ( 'use_advanced_permissions' !== $permission->get_key() ) {
				$permission->set_as_selected( $permission->is_selected() && ! $use_advanced_permissions );
			}
		}

		return $options;
	}

	/**
	 * Get the correct shortcode constructor
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function generate_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor ): AppShortcodeConstructor {
		$post_library = Factory::get_instance( Post::class );

		$permissions_preference   = $post_library->get_radio_post_parameter( 'room_permissions_preference' );
		$use_advanced_permissions = ( 'use_advanced_permissions' === $permissions_preference );

		if ( $use_advanced_permissions ) {
			$admin_strings = array();

			$user_permissions = $post_library->get_multi_post_parameter( 'advanced_permissions_users' );
			$role_permissions = $post_library->get_multi_post_parameter( 'advanced_permissions_roles' );

			if ( $user_permissions ) {
				$admin_strings[] = 'user:' . implode( ',', $user_permissions );
			}

			if ( $role_permissions ) {
				$admin_strings[] = 'role:' . implode( ',', $role_permissions );
			}

			if ( $admin_strings ) {
				$shortcode_constructor->add_custom_string_param( 'host', implode( ';', $admin_strings ) );
			}
		}

		return $shortcode_constructor;
	}

	/**
	 * Add the permissions section to the room builder
	 */
	public function add_permission_section() {
		$post_library = Factory::get_instance( Post::class );

		$user_permissions = $post_library->get_multi_post_parameter( 'advanced_permissions_users' );
		$role_permissions = $post_library->get_multi_post_parameter( 'advanced_permissions_roles' );

		$section = ( require __DIR__ . '/views/settings.php' )(
			$user_permissions,
			$role_permissions,
			self::$id_index++
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All upstream variables have already been sanitised in their function.
		echo $section;
	}
}
