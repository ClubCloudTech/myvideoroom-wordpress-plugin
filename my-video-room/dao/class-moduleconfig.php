<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries - Configures Modules.
 *
 * @package my-video-room/dao/class-moduleconfig.php
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\SiteDefaults;

/**
 * Class ModuleConfig
 * Configures Database Layer for Module Manager.
 */
class ModuleConfig {

	const TABLE_NAME = SiteDefaults::TABLE_NAME_MODULE_CONFIG;

	const ACTION_ENABLE  = 'enable';
	const ACTION_DISABLE = 'disable';

	/**
	 * Register a given room in the Database, and ensure it does not already exist
	 *
	 * @param string $module_name           Name of module to register.
	 * @param int    $module_id             ID of module to register.
	 * @param bool   $module_has_admin_page Whether module has admin page.
	 *
	 * @return bool
	 */
	public function register_module_in_db( string $module_name, int $module_id, bool $module_has_admin_page = null ): bool {
		global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					INSERT IGNORE INTO ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_table_name() . '
					(module_name, module_id, module_has_admin_page) 
					VALUES( %s, %d, %d )
				',
				$module_name,
				$module_id,
				$module_has_admin_page
			)
		);

		\wp_cache_delete( $module_name, implode( '::', array( __CLASS__, 'is_module_activation_enabled' ) ) );

		return true;
	}

	/**
	 * Get the table name for this DAO.
	 *
	 * @return string
	 */
	private function get_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * This function renders the activate/deactivate button for a given module
	 * Used only in admin pages of plugin
	 *
	 * @TODO - Move this somewhere more appropriate, it should also return a string, not output directly
	 *
	 * @param int    $module_id Module ID.
	 * @param string $module_status current state of module (used from Ajax calls).
	 * @param bool   $dependency_check - if the Module has something that prevents it from activating (requires caller to provide a bool screen function).
	 *
	 * @return string  Button with link
	 */
	public function module_activation_button( int $module_id, string $module_status = null, bool $dependency_check = null ): ?string {

		switch ( $module_status ) {
			case self::ACTION_ENABLE:
				\do_action( 'myvideoroom_enable_feature_module', $module_id );
				$this->update_enabled_status( $module_id, true );
				break;
			case self::ACTION_DISABLE:
				\do_action( 'myvideoroom_disable_feature_module', $module_id );
				$this->update_enabled_status( $module_id, false );
				break;
		}

		// Check enabled status to see which button to render.
		$is_module_enabled = $this->is_module_activation_enabled( $module_id );

		// Check if is sub tab to mark as such to strip out extra data in URL when called back.

		if ( $is_module_enabled && false === $dependency_check ) {
			$action      = null;
			$type        = 'dashicons-dismiss';
			$description = esc_html__( 'This module depends on a module is not active. This feature will not work', 'myvideoroom' );
			$status      = 'mvr-icons-disabled mvr-admin-ajax';
			$main_text   = __( 'Inactive', 'myvideoroom' );
		} elseif ( $is_module_enabled ) {
			$action      = self::ACTION_DISABLE;
			$type        = 'dashicons-plugins-checked';
			$description = esc_html__( 'This feature is enabled and working properly.', 'myvideoroom' );
			$status      = 'mvr-icons-enabled mvr-admin-ajax';
			$main_text   = __( 'Active', 'myvideoroom' );
		} elseif ( false === $dependency_check ) {
			$action      = null;
			$type        = 'dashicons-dismiss';
			$description = esc_html__( 'You cannot enable this as a dependent module is not active', 'myvideoroom' );
			$status      = 'mvr-icons-disabled mvr-admin-ajax';
			$main_text   = __( 'Supporting Module Not Found', 'myvideoroom' );
		} else {
			$action      = self::ACTION_ENABLE;
			$type        = 'dashicons-admin-plugins';
			$description = esc_html__( 'This scenario is currently disabled and its rooms are offline', 'myvideoroom' );
			$status      = 'mvr-icons-disabled mvr-admin-ajax';
			$main_text   = __( 'Inactive', 'myvideoroom' );
		}
		$nonce = \wp_create_nonce( $module_id );

		return '
		<div id="display' . \esc_attr( $module_id ) . '">
		<div id="module' . \esc_attr( $module_id ) . '">
			<a href="#"
				class="' . esc_html( $status ) . '"
				title="' . esc_html( $description ) . '"
				data-action="' . esc_html( $action ) . '"
				data-module="' . esc_html( $module_id ) . '"
			> <i class="dashicons ' . esc_html( $type ) . ' ' . esc_html( $status ) . '"></i>
				' . esc_html( $main_text ) . '</a></div></div>';
	}

	/**
	 * Update Enabled Module Status in Database.
	 *
	 * @param int  $module_id      ID of module.
	 * @param bool $module_enabled Is module enabled.
	 *
	 * @return bool
	 */
	public function update_enabled_status( int $module_id, bool $module_enabled ): bool {
		global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$this->get_table_name(),
			array( 'module_enabled' => (int) $module_enabled ),
			array( 'module_id' => $module_id )
		);

		\wp_cache_set(
			$module_id,
			$module_enabled,
			implode(
				'::',
				array(
					__CLASS__,
					'is_module_activation_enabled',
				)
			)
		);

		return true;
	}

	// ---

	/**
	 * This function renders the activate/deactivate button for a give module
	 * Used only in admin pages of plugin
	 *
	 * @param int $module_id - The module ID.
	 *
	 * @return bool
	 */
	public function is_module_activation_enabled( int $module_id ): bool {
		global $wpdb;

		$found  = false;
		$result = \wp_cache_get( $module_id, __METHOD__, $found );

		if ( ! $found ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$row = $wpdb->get_row(
				$wpdb->prepare(
					'
						SELECT module_enabled 
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_table_name() . ' 
						WHERE module_id = %d
					',
					$module_id
				)
			);

			if ( $row ) {
				$result = (bool) $row->module_enabled;
			}

			\wp_cache_set( $module_id, $result, __METHOD__ );
		}

		return (bool) $result;
	}
}
