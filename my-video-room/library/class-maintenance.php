<?php
/**
 * Maintenance and Scheduled Operations.
 *
 * @package my-video-room/library/class-maintenance.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\DAO\RoomSyncDAO;
use MyVideoRoomPlugin\Factory;

/**
 * Class Maintenance Provides Support for Main Plugin Scheduled Maintenance and options.
 */
class Maintenance {

	const OPTION_DB_CLEAN_SYNC       = 'myvideoroom-db-sync-cleanup';
	const OPTION_LAST_TEMPLATE_SYNCC = 'myvideoroom-last-time-sync';

	/**
	 * Init All Maintenance Filters.
	 *
	 * @return void
	 */
	public function init() {
		\add_filter( 'myvideoroom_maintenance_result_listener', array( $this, 'process_update_filter' ), 10, 2 );
		\add_filter( 'myvideoroom_maintenance_page_option', array( $this, 'render_maintenance_menu_option' ), 10, 1 );

		add_filter( 'cron_schedules', array( $this, 'mvr_add_cron_interval' ) );

	}
	/**
	 * Add Cron Interval.
	 *
	 * @param array $schedules - THe schedule from the filter.
	 * @return ?string
	 */
	public function mvr_add_cron_interval( array $schedules ) {
		$schedules['twicedaily'] = array(
			'interval' => 60 * 60 * 24 * 2,
			'display'  => 'Every Minute',
		);
		return $schedules;
	}

	/**
	 * Activate. Sets Up Maintenance Numbers and Parameters for Module.
	 *
	 * @return void
	 */
	public function activate() {
		\update_option( self::OPTION_DB_CLEAN_SYNC, 14 );
		Factory::get_instance( AvailableScenes::class )->update_templates();
		if ( ! wp_next_scheduled( 'mvr_trim_room_presence_table' ) ) {
			wp_schedule_event( time(), 'daily', 'mvr_trim_room_presence_table' );
		}
	}

	/**
	 * De-Activate. Removes Filters on De-activation.
	 *
	 * @return void
	 */
	public function de_activate() {
		wp_clear_scheduled_hook( 'mvr_trim_room_presence_table' );
	}

	/**
	 * Process Result.
	 *
	 * @param array $response -  Inbound response Elements that will go back to the Ajax Script.
	 * @return array
	 */
	public function process_update_filter( array $response ): array {
		$setting_db_clean = Factory::get_instance( Ajax::class )->get_string_parameter( self::OPTION_DB_CLEAN_SYNC );
		\update_option( self::OPTION_DB_CLEAN_SYNC, intval( $setting_db_clean ) );
		$response['feedback'] = \esc_html__( 'Settings Saved', 'myvideoroom' );
		return $response;
	}

	/**
	 * Render Menu Option for Maintenance Table.
	 *
	 * @param string $input -  Inbound Option Elements.
	 * @return ?string
	 */
	public function render_maintenance_menu_option( string $input = null ) {
		return $input .= '
		<tr class="active mvr-table-mobile">
		<td>
		<label for="' . esc_attr( self::OPTION_DB_CLEAN_SYNC ) . '
				class="mvr-preferences-paragraph myvideoroom-separation">
				' . esc_html__( 'Room Sync Database Tolerance (Days)', 'myvideoroom' ) . '
			</label>
		</td>
		<td>
		<input type="text" class="myvideoroom-maintenance-setting"
			id="' . esc_attr( self::OPTION_DB_CLEAN_SYNC ) . '" name ="' . esc_attr( self::OPTION_DB_CLEAN_SYNC ) . '"
			value="' . esc_attr( get_option( self::OPTION_DB_CLEAN_SYNC ) ) . '" />
			<i class="myvideoroom-dashicons mvr-icons dashicons-editor-help" title="' . \esc_html__( 'How Many Days to keep Room Table for, outside of this there will be no record of the user\'s presence in the room. (Default 14)', 'myvideoroom' ) . '"></i>
		</td>
	</tr>';
	}

	/**
	 * Prune Room Presence Table.
	 *
	 * Deletes Room Presence Table records older than stored config limits.
	 *
	 * @return string
	 */
	public function mvr_trim_room_presence_table() {
		$timestamp_days      = \get_option( self::OPTION_DB_CLEAN_SYNC );
		$timestamp_number    = $timestamp_days * 24 * 60 * 60;
		$time_now            = \current_time( 'timestamp' );
		$tolerance_timestamp = $time_now - $timestamp_number;
		$status              = Factory::get_instance( RoomSyncDAO::class )->delete_records_by_timestamp( $tolerance_timestamp );
		return $status;
	}
}

