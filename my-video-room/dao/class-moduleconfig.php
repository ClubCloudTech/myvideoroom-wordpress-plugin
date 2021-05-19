<?php
/**
 * Data Access Object for controlling Room Mapping Database Entries - Configures Modules.
 *
 * @package MyVideoRoomPlugin\DAO
 */

namespace MyVideoRoomPlugin\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Core\SiteDefaults;
use MyVideoRoomPlugin\DAO\RoomMap;
use MyVideoRoomPlugin\Library\HttpGet;

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
	 * Get a User Video Preference from the database
	 *
	 * @param string $room_name The Room Name.
	 *
	 * @return ?int Post ID or Null.
	 */
	public function read( string $room_name ): ?int {
		global $wpdb;
		$table_name_sql = $wpdb->prefix . self::TABLE_NAME;
		$prepared_query = $wpdb->prepare(
			// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'SELECT post_id FROM ' . $table_name_sql . ' WHERE room_name = %s',
			$room_name
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );
		if ( $row ) {
			$result = (int) $row->post_id;
		}
		return $result;
	}

	/**
	 * Check Table Exists
	 *
	 * @param string $table_name - name of table to check.
	 *
	 * @return bool
	 */
	public function check_table_exists( string $table_name ): bool {
		global $wpdb;
		$table_name_sql = $wpdb->prefix . $table_name;
		$prepared_query = $wpdb->prepare(
			// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'SELECT 1 FROM ' . $table_name_sql . ' LIMIT 1',
		);

		try {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
			$row = $wpdb->get_row( $prepared_query );
			if ( $row ) {
				return true;
			}

			return false;
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * Check Module Exists in Database
	 *
	 * @param  int $module_id - The module ID.
	 *
	 * @return bool
	 */
	public function check_module_exists( int $module_id ): bool {
		global $wpdb;

		// First Check Database for ModuleID - return No if blank.
		if ( ! $module_id ) {
			return false;
		}

		$table_name_sql = $wpdb->prefix . self::TABLE_NAME;
		$prepared_query = $wpdb->prepare(
			// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'SELECT 1 FROM ' . $table_name_sql . ' WHERE module_id = %d',
			$module_id
		);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- already prepared above.
		$row = $wpdb->get_row( $prepared_query );
		if ( $row ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Register a given room in the Database, and ensure it does not already exist
	 *
	 * @param  string  $module_name             Name of module to register.
	 * @param  int     $module_id               ID of module to register.
	 * @param  bool    $module_has_admin_page   Whether module has admin page.
	 * @param  ?string $module_admin_path       Path to location of admin page.
	 *
	 * @return string|int|null
	 */
	public function register_module_in_db( string $module_name, int $module_id, bool $module_has_admin_page = null, string $module_admin_path = null ) {
		global $wpdb;
		// Empty input exit.
		if ( ! $module_name || ! $module_id ) {
			return 'Invalid Entry need Module ID and Name';
		}
		// Exit if Module already Exists.
		if ( $this->check_module_exists( $module_id ) ) {
			return true;
		}

		// Create Post.
		return $wpdb->insert(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'module_name'           => $module_name,
				'module_id'             => $module_id,
				'module_has_admin_page' => $module_has_admin_page,
				'module_admin_path'     => $module_admin_path,
			)
		);
	}

	/**
	 * Update Enabled Module Status in Database.
	 *
	 * @param  int  $module_id      ID of module.
	 * @param  bool $module_enabled Is module enabled.
	 *
	 * @return bool|int
	 */
	public function update_enabled_status( int $module_id, bool $module_enabled ) {
		global $wpdb;
		// First Check Database for Room and Post ID - return No if blank.

		if ( ! $module_id ) {
			return false;
		}
		$table_name_sql = $wpdb->prefix . self::TABLE_NAME;
		$prepared_query = $wpdb->prepare(
			// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'UPDATE ' . $table_name_sql . ' SET module_enabled = %d WHERE module_id = %d',
			$module_enabled,
			$module_id,
		);
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- already prepared above.
		$result = $wpdb->query( $prepared_query );
		return $result;
	}

	/**
	 * Read Enabled Module Status in Database
	 *
	 * @param  int $module_id The module ID.
	 *
	 * @return string
	 */
	public function read_enabled_status( int $module_id ) {
		global $wpdb;

		// First Check Database for ModuleID - return No if blank.
		if ( ! $module_id ) {
			return false;
		}
		$table_name_sql = $wpdb->prefix . self::TABLE_NAME;
		$prepared_query = $wpdb->prepare(
		// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'SELECT module_enabled FROM ' . $table_name_sql . ' WHERE module_id = %d',
			$module_id
		);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = null;

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );
		if ( $row ) {
			$result = $row->module_enabled;
		}

		return $result;
	}

	/**
	 * Get Admin URL of Page
	 *
	 * @param  string $module_name - the listed name of the module thats been added.
	 * @return string
	 */
	public function get_module_admin_path( string $module_name ) {
		global $wpdb;

		$table_name_sql = $wpdb->prefix . self::TABLE_NAME;
		$prepared_query = $wpdb->prepare(
			// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'SELECT module_admin_path FROM ' . $table_name_sql . ' WHERE module_name = %s',
			$module_name
		);

		$result = null;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row( $prepared_query );
		if ( $row ) {
			$result = $row->module_admin_path;
		}
		return $result;
	}

	/**
	 * Delete a Room Record Mapping to a URL in Database
	 *
	 *  This function will delete the room name in the database with the parameter
	 *
	 * @param string $room_name - both needed.
	 *
	 * @return bool
	 */
	public function delete_room_mapping( string $room_name ): bool {
		global $wpdb;

		if ( ! $room_name ) {
			return false;
		}

		$table_name_sql = $wpdb->prefix . self::TABLE_NAME;
		$prepared_query = $wpdb->prepare(
			// phpcs:ignore -- WordPress.DB.PreparedSQL.InterpolatedNotPrepared - false positive due to table constant.
			'DELETE FROM ' . $table_name_sql . ' WHERE room_name = %s',
			$room_name
		);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $prepared_query );

		return true;
	}

	/**
	 * Check a Page Exists
	 *
	 * @param  string $room_name - Room Name.
	 *
	 * @return string  Yes, No, Orphan (database exists but page deleted ).
	 */
	public function check_page_exists( string $room_name ) {
		// empty input exit.
		if ( ! $room_name ) {
			return false;
		}

		// First Check Database for Room and Post ID - return No if blank.
		$post_id_check = Factory::get_instance( RoomMap::class )->read( $room_name );
		if ( ! $post_id_check ) {
			return self::PAGE_STATUS_NOT_EXISTS;
		}

		// Second Check Post Actually Exists in WP still (user hasn't deleted page).
		$post_object = get_post( $post_id_check );

		if ( ! $post_object ) {
			return self::PAGE_STATUS_ORPHANED;
		} else {
			return self::PAGE_STATUS_EXISTS;
		}
	}

	/**
	 * This function renders the activate/deactivate button for a given module
	 * Used only in admin pages of plugin
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
				$this->update_enabled_status( $module_id, true );
				break;
			case self::ACTION_DISABLE:
				$this->update_enabled_status( $module_id, false );
				break;
		}

		// Check enabled status to see which button to render.
		$is_module_enabled = $this->read_enabled_status( $module_id );

		// Check if is sub tab to mark as such to strip out extra data in URL when called back.

		$base_url = \add_query_arg(
			array(
				'module_id' => $module_id,
			),
			home_url( $server_path )
		);

		if ( $is_module_enabled ) {
			$action      = self::ACTION_DISABLE;
			$type        = 'positive';
			$main_text   = __( 'Enabled', 'myvideoroom' );
			$action_text = __( 'Disable module', 'myvideoroom' );
			$result      = true;
		} else {
			$action      = self::ACTION_ENABLE;
			$type        = 'negative';
			$main_text   = __( 'Disabled', 'myvideoroom' );
			$action_text = __( 'Enable module', 'myvideoroom' );
			$result      = false;
		}

		$url = \add_query_arg(
			array(
				'module_action' => $action,
			),
			$base_url
		);

		$sub_tab_url = \add_query_arg(
			array(
				'subtab' => 1,
			),
			$url
		);

		?>
		<div style="display: flex; justify-content: space-between; width: 50%;">
			<a href="<?php echo esc_url( $url ); ?>" class="button button-primary <?php echo esc_attr( $type ); ?>">
				<?php echo esc_html( $main_text ); ?>
			</a>
			<a href="<?php echo esc_url( $sub_tab_url ); ?>" class="button button-primary">
				<?php echo esc_html( $action_text ); ?>
			</a>
		</div>
		<?php

		return $result;

	}


	/**
	 * Sub_module_activation_button.
	 *
	 * @param int $module_id Module.
	 *
	 * @return string
	 */
	public function sub_module_activation_button( int $module_id ): string {
		$module_id_by_url = null;
		$module_status    = null;

		// Listening for Input.
		if ( isset( $_GET['subaction'] ) ) {
			$module_status = $params['subaction'] ?? htmlspecialchars( sanitize_textarea_field( wp_unslash( $_GET['subaction'] ) ) ?? '' );
		}
		if ( isset( $_GET['submoduleid'] ) ) {
			$module_id_by_url = $params['submoduleid'] ?? htmlspecialchars( sanitize_textarea_field( wp_unslash( $_GET['submoduleid'] ) ) ?? '' );
		}
		// Replace Module ID from URL post if one exists.
		if ( $module_id_by_url ) {
			$module_id = $module_id_by_url;
		}
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$server_path = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		// Check Modules State.
		if ( $module_status ) {
			// Case Disable State Change.
			if ( 'disable' === $module_status ) {
				$this->update_enabled_status( $module_id, false );

			} elseif ( 'enable' === $module_status ) {
				$this->update_enabled_status( $module_id, true );
			}
		}

		// Processing Link for Button.
		if ( $module_id_by_url ) {
			$original_url = home_url( $server_path );
			// Strip out anything after &action which is done by plugin.
			$original_url = substr( $original_url, 0, strpos( $original_url, '&subaction' ) );
			wp_safe_redirect( $original_url );
			exit();
		}

		// Check enabled status to see which button to render.
					$is_module_enabled = $this->read_enabled_status( $module_id );
					// Check if is sub tab to mark as such to strip out extra data in URL when called back.

					$sub_tab_tag = '&subtab=1';

					// Build URL.
					$current_url = home_url( $server_path );
		if ( ! $is_module_enabled ) {

			$current_url .= '&subaction=enable&submoduleid=' . $module_id;
			$output_link  = '<div id="ccbutton-array" style="display: flex;	justify-content: space-between; width: 50%;">
			<a href="' . $current_url . $sub_tab_tag . '" class="button button-primary" style="background-color:red" >' . esc_html_e( 'Disabled', 'myvideoroom' ) . '</a>
			<a href="' . $current_url . $sub_tab_tag . '" class="button button-primary" >' . esc_html_e( 'Enable Module', 'myvideoroom' ) . '</a>
			</div>';
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  output already formatted without user input.
			echo $output_link;
			return false;

		} else {
			$current_url .= '&subaction=disable&submoduleid=' . $module_id;
			$output_link  = '<div id="ccbutton-array" style= "display: flex;	justify-content: space-between; width: 50%;">
			<a href="' . $current_url . $sub_tab_tag . '" class="button button-primary" style="background-color:green" >' . esc_html_e( 'Enabled', 'myvideoroom' ) . '</a>
			<a href="' . $current_url . $sub_tab_tag . '" class="button button-primary"  >' . esc_html_e( 'Disable Module', 'myvideoroom' ) . '</a>
			</div>';
		}
		//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped  output already formatted without user input.
		echo $output_link;
		// Exit if both action and module were in URL (as it can only happen when sub features were called ) - Need to strip out action parameters and refresh page to allow child modules to not display incorrectly.
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
	public function module_activation_status( int $module_id ): bool {
		return (bool) $this->read_enabled_status( $module_id );
	}
}
