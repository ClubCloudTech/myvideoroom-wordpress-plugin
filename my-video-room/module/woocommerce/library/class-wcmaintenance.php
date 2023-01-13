<?php
/**
 * Maintenance and Scheduled Operations. WooCommerce Module
 *
 * @package my-video-room/module/woocommerce/library/class-WCMaintenance.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideoDAO;

/**
 * Class WCMaintenance
 * Provides Support for WooCommerce MVR Module Scheduled Maintenance and options.
 */
class WCMaintenance {

	const OPTION_WC_CLEAN_SYNC = 'myvideoroom-wc-sync-cleanup';
	/**
	 * Activate. Sets Up Maintenance Numbers and Parameters for Module.
	 *
	 * @return void
	 */
	public function activate() {
		\update_option( self::OPTION_WC_CLEAN_SYNC, 14 );

		if ( ! wp_next_scheduled( 'myvideoroom_trim_sync_state_table' ) ) {
			wp_schedule_event( time(), 'daily', 'myvideoroom_trim_sync_state_table' );
		}

	}

	/**
	 * De-Activate. Removes Filters on De-activation.
	 *
	 * @return void
	 */
	public function de_activate() {
		wp_clear_scheduled_hook( 'myvideoroom_trim_sync_state_table' );
	}

	/**
	 * Render Menu Option
	 *
	 * @param string $input -  Inbound Option Elements.
	 * @return ?string
	 */
	public function render_maintenance_menu_option( string $input = null ) {
		return $input .= '
		<tr class="mvr-table-mobile">
		<td>
		<label for="' . esc_attr( self::OPTION_WC_CLEAN_SYNC ) . '
				class="mvr-preferences-paragraph myvideoroom-separation">
				' . esc_html__( 'WooCommerce Basket Sharing Tolerance (Days) ', 'myvideoroom' ) . '
			</label>
		</td>
		<td>
		<input type="number" min="1" max="365" class="myvideoroom-maintenance-setting"
			id="' . esc_attr( self::OPTION_WC_CLEAN_SYNC ) . '" name ="' . esc_attr( self::OPTION_WC_CLEAN_SYNC ) . '"
			value="' . esc_attr( get_option( self::OPTION_WC_CLEAN_SYNC ) ) . '" />
			<i class="myvideoroom-dashicons mvr-icons dashicons-editor-help myvideoroom-dashicons-override" title="' . \esc_html__( 'How Many Days to keep Room Table for, outside of this there will be no record of the user\'s presence in the room. (Default 14)', 'myvideoroom' ) . '"></i>
		</td>
	</tr>';
	}

	/**
	 * Process Result.
	 *
	 * @param array $response -  Inbound response Elements that will go back to the Ajax Script.
	 * @return array
	 */
	public function process_update_filter( array $response ): array {
		$setting_woocommerce_clean = Factory::get_instance( Ajax::class )->get_string_parameter( self::OPTION_WC_CLEAN_SYNC );
		\update_option( self::OPTION_WC_CLEAN_SYNC, intval( $setting_woocommerce_clean ) );

		return $response;
	}

	/**
	 * Global Function for trim Sync State table (for Cron jobs)
	 *
	 * @return array|false|int|mixed|string|void|null
	 */
	public function myvideoroom_trim_sync_state_table() {
		$function_to_call = new WCMaintenance();
		return $function_to_call->mvr_trim_wc_room_table( ...func_get_args() );
	}

	/**
	 * Prune Sync State Table.
	 *
	 * Deletes Room Presence Table records older than stored config limits.
	 *
	 * @return string
	 */
	private function mvr_trim_wc_room_table() {
		$timestamp_days      = \get_option( self::OPTION_WC_CLEAN_SYNC );
		$timestamp_number    = $timestamp_days * 24 * 60 * 60;
		$time_now            = \current_time( 'timestamp' );
		$tolerance_timestamp = $time_now - $timestamp_number;
		$status              = Factory::get_instance( WooCommerceVideoDAO::class )->delete_records_by_timestamp( $tolerance_timestamp );
		return $status;
	}

}
