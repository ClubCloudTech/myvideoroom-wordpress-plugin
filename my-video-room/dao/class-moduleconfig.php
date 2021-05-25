<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries - Configures Modules.
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\VideoHelpers;

/**
 * Class ModuleConfig
 * Configures Database Layer for Module Manager.
 */
class ModuleConfig {

	const TABLE_NAME = SiteDefaults::TABLE_NAME_MODULE_CONFIG;

	const ACTION_ENABLE          = 'enable';
	const ACTION_DISABLE         = 'disable';
	const PAGE_STATUS_EXISTS     = 'page-exists';
	const PAGE_STATUS_NOT_EXISTS = 'page-not-exists';
	const PAGE_STATUS_ORPHANED   = 'page-not-exists-but-has-reference';

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
	 * Register a given room in the Database, and ensure it does not already exist
	 *
	 * @param  string  $module_name             Name of module to register.
	 * @param  int     $module_id               ID of module to register.
	 * @param  bool    $module_has_admin_page   Whether module has admin page.
	 * @param  ?string $module_admin_path       Path to location of admin page.
	 *
	 * @return bool
	 */
	public function register_module_in_db( string $module_name, int $module_id, bool $module_has_admin_page = null, string $module_admin_path = null ): bool {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				'
					INSERT IGNORE INTO ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */$this->get_table_name() . '
					(module_name, module_id, module_has_admin_page, module_admin_path) 
					VALUES( %s, %d, %d, %s )
				',
				$module_name,
				$module_id,
				$module_has_admin_page,
				$module_admin_path
			)
		);

		\wp_cache_delete( $module_name, implode( '::', array( __CLASS__, 'is_module_activation_enabled' ) ) );
		\wp_cache_delete( $module_id, implode( '::', array( __CLASS__, 'get_module_admin_path' ) ) );

		return true;
	}


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

	/**
	 * Update Enabled Module Status in Database.
	 *
	 * @param  int  $module_id      ID of module.
	 * @param  bool $module_enabled Is module enabled.
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

		\wp_cache_set( $module_id, $module_enabled, implode( '::', array( __CLASS__, 'is_module_activation_enabled' ) ) );

		return true;
	}

	/**
	 * Get Admin URL of Page
	 *
	 * @param  string $module_name - the listed name of the module that's been added.
	 *
	 * @return string
	 */
	public function get_module_admin_path( string $module_name ): ?string {
		global $wpdb;

		$found  = false;
		$result = \wp_cache_get( $module_name, __METHOD__, $found );

		if ( false === $found ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$row = $wpdb->get_row(
				$wpdb->prepare(
					'
						SELECT module_admin_path 
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_table_name() . ' 
						WHERE module_name = %s
					',
					$module_name
				)
			);

			if ( $row ) {
				$result = $row->module_admin_path;
			}

			\wp_cache_set( $module_name, $result, __METHOD__ );
		}

		return $result;
	}

	// ---

	/**
	 * This function renders the activate/deactivate button for a given module
	 * Used only in admin pages of plugin
	 *
	 * @TODO - Move this somewhere more appropriate, it should also return a string, not output directly
	 *
	 * @param int $module_id Module ID.
	 *
	 * @return string  Button with link
	 */
	public function module_activation_button( int $module_id ): string {
		$http_get_library = Factory::get_instance( HttpGet::class );

		$module_status = $http_get_library->get_string_parameter( 'module_action' );
		$module_id     = $http_get_library->get_string_parameter( 'module_id', $module_id );

		$server_path = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$server_path = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

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

		$base_url = \add_query_arg(
			array(
				'module_id' => $module_id,
			),
			home_url( $server_path )
		);

		if ( $is_module_enabled ) {
			$action      = self::ACTION_DISABLE;
			$type        = 'dashicons-plugins-checked';
			$description = esc_html__( 'This scenario is currently enabled and its rooms are accepting meetings.', 'myvideoroom' );
			$status      = 'mvr-icons-enabled';
			$main_text   = __( 'Active', 'myvideoroom' );
			$result      = true;
		} else {
			$action      = self::ACTION_ENABLE;
			$type        = 'dashicons-admin-plugins';
			$description = esc_html__( 'This scenario is currently disabled and its rooms are offline', 'myvideoroom' );
			$status      = 'mvr-icons-disabled';
			$main_text   = __( 'Inactive', 'myvideoroom' );
			$result      = false;
		}

		$url = \add_query_arg(
			array(
				'module_action' => $action,
			),
			$base_url
		);

		?>
		<div>
			<a href="<?php echo esc_url( $url ); ?>"
			class="<?php echo esc_html( $status ); ?>"
			title="<?php echo esc_html( $description ); ?>"
			>
			<i class="dashicons <?php echo esc_html( $type ) . ' ' . esc_html( $status ); ?>"></i>
			<?php echo esc_html( $main_text ); ?></a>
		</div>
		<?php

		return $result;
	}
}
