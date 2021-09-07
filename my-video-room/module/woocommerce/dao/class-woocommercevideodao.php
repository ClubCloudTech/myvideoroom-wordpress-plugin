<?php
/**
 * Data Access Object for Cart Storage and Transmission
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\DAO\WooCommerceVideo.php
 */

namespace MyVideoRoomPlugin\Module\WooCommerce\DAO;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Entity\WooCommerceVideo as WooCommerceVideoCart;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class WooCommerceVideo
 */
class WooCommerceVideoDAO {
	/**
	 * Install WooCommerce Sync Config Table.
	 *
	 * @return bool
	 */
	public function install_woocommerce_sync_config_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql_create = 'CREATE TABLE IF NOT EXISTS `' . $this->get_main_table_name() . '` (
			`record_id` int NOT NULL AUTO_INCREMENT,
			`cart_id` VARCHAR(255) NOT NULL,
			`source_cart_id` VARCHAR(255) NOT NULL,
			`room_name` VARCHAR(255) NOT NULL,
			`cart_data` VARCHAR(8192) NULL,
			`product_id` VARCHAR(255) NULL,
			`quantity` VARCHAR(255) NULL,
			`variation_id` VARCHAR(255) NULL,
			`single_product` BOOLEAN,
			`timestamp` BIGINT UNSIGNED NULL,
			PRIMARY KEY (`record_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

		return \maybe_create_table( $this->get_main_table_name(), $sql_create );
	}

	/**
	 * Get the table name for MainTable DAO.
	 *
	 * @return string
	 */
	private function get_main_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . WooCommerce::TABLE_NAME_WOOCOMMERCE_CART;
	}

	/**
	 * Save a Cart Sync Event into the database
	 *
	 * @param WooCommerceVideoCart $woocommercevideocartobject The video preference to save.
	 *
	 * @return WooCommerceVideoCart|null
	 * @throws \Exception When failing to insert, most likely a duplicate key.
	 */
	public function create( WooCommerceVideoCart $woocommercevideocartobject ): ?WooCommerceVideoCart {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$woocommercevideocartobject->get_cart_id(),
			$woocommercevideocartobject->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$this->get_main_table_name(),
			array(
				'cart_id'        => $woocommercevideocartobject->get_cart_id(),
				'source_cart_id' => $woocommercevideocartobject->get_source_cart_id(),
				'room_name'      => $woocommercevideocartobject->get_room_name(),
				'cart_data'      => $woocommercevideocartobject->get_cart_data(),
				'product_id'     => $woocommercevideocartobject->get_product_id(),
				'quantity'       => $woocommercevideocartobject->get_quantity(),
				'variation_id'   => $woocommercevideocartobject->get_variation_id(),
				'single_product' => $woocommercevideocartobject->is_single_product(),
				'timestamp'      => $woocommercevideocartobject->get_timestamp(),

			)
		);

		$woocommercevideocartobject->set_record_id( $wpdb->insert_id );

		\wp_cache_set(
			$cache_key,
			$woocommercevideocartobject->to_json(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_record_by_cart_id',
				)
			)
		);
		\wp_cache_delete(
			$woocommercevideocartobject->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return $woocommercevideocartobject;
	}

	/**
	 * Create a cache key
	 *
	 * @param string $cart_id   The user id.
	 * @param string $room_name The room name.
	 *
	 * @return string
	 */
	private function create_cache_key( string $cart_id, string $room_name ): string {
		return "cart_id:${cart_id}:room_name:${room_name}";
	}

	/**
	 * Get a Cart Object from the database
	 *
	 * @param string $cart_id The Cart id.
	 *
	 * @return WooCommerceVideoCart[]
	 */
	public function get_by_cart_id( string $cart_id ): array {
		global $wpdb;

		$results = array();

		$room_names = \wp_cache_get( $cart_id, __METHOD__ );

		if ( false === $room_names ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$room_names = $wpdb->get_col(
				$wpdb->prepare(
					'
						SELECT room_name
						FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_main_table_name() . '
						WHERE cart_id = %s;
					',
					$cart_id,
				)
			);

			\wp_cache_set( $cart_id, __METHOD__, $room_names );
		}

		foreach ( $room_names as $room_name ) {
			$results[] = $this->get_record_by_cart_id( $cart_id, $room_name );
		}

		return $results;
	}

	/**
	 * Get a Cart Object from the database
	 *
	 * @param string $cart_id   The user id.
	 * @param string $room_name The room name.
	 *
	 * @return WooCommerceVideoCart|null
	 */
	public function get_record_by_cart_id( string $cart_id, string $room_name ): ?WooCommerceVideoCart {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$cart_id,
			$room_name
		);

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return WooCommerceVideoCart::from_json( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT 
			       cart_id,
				   source_cart_id,
			       room_name,
			       cart_data, 
			       product_id, 
			       quantity,
			       variation_id,
			       single_product, 
			       timestamp, 
				   record_id
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_main_table_name() . '
				WHERE cart_id = %s AND room_name = %s;
			',
				array(
					$cart_id,
					$room_name,
				)
			)
		);

		$result = null;

		if ( $row ) {
			$result = new WooCommerceVideoCart(
				$row->cart_id,
				$row->source_cart_id,
				$row->room_name,
				$row->cart_data,
				$row->product_id,
				$row->quantity,
				$row->variation_id,
				(bool) $row->single_product,
				$row->timestamp,
				$row->id,
			);
			wp_cache_set( $cache_key, __METHOD__, $result->to_json() );
		} else {
			wp_cache_set( $cache_key, __METHOD__, null );
		}

		return $result;
	}

	/**
	 * Get a Cart Object from the database by record ID.
	 *
	 * @param int $record_id   The record id.
	 *
	 * @return WooCommerceVideoCart|null
	 */
	public function get_record_by_record_id( int $record_id ): ?WooCommerceVideoCart {
		global $wpdb;

		$cache_key = $record_id;

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return WooCommerceVideoCart::from_json( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT 
			       cart_id,
				   source_cart_id,
			       room_name,
			       cart_data, 
			       product_id, 
			       quantity,
			       variation_id,
			       single_product, 
			       timestamp, 
				   record_id
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_main_table_name() . '
				WHERE record_id = %d;
			',
				$record_id
			)
		);

		$result = null;

		if ( $row ) {
			$result = new WooCommerceVideoCart(
				$row->cart_id,
				$row->source_cart_id,
				$row->room_name,
				$row->cart_data,
				$row->product_id,
				$row->quantity,
				$row->variation_id,
				(bool) $row->single_product,
				$row->timestamp,
				$row->id,
			);
			wp_cache_set( $cache_key, __METHOD__, $result->to_json() );
		} else {
			wp_cache_set( $cache_key, __METHOD__, null );
		}

		return $result;
	}


	/**
	 * Update a Cart Object into the database
	 *
	 * @param WooCommerceVideoCart $woocommercevideocartobject The updated Cart Object.
	 *
	 * @return WooCommerceVideoCart|null
	 * @throws \Exception When failing to update.
	 */
	public function update( WooCommerceVideoCart $woocommercevideocartobject ): ?WooCommerceVideoCart {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$woocommercevideocartobject->get_cart_id(),
			$woocommercevideocartobject->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$this->get_main_table_name(),
			array(
				'cart_id'        => $woocommercevideocartobject->get_cart_id(),
				'source_cart_id' => $woocommercevideocartobject->get_source_cart_id(),
				'room_name'      => $woocommercevideocartobject->get_room_name(),
				'cart_data'      => $woocommercevideocartobject->get_cart_data(),
				'product_id'     => $woocommercevideocartobject->get_product_id(),
				'quantity'       => $woocommercevideocartobject->get_quantity(),
				'variation_id'   => $woocommercevideocartobject->get_variation_id(),
				'single_product' => $woocommercevideocartobject->is_single_product(),
				'timestamp'      => $woocommercevideocartobject->get_timestamp(),
			),
			array(
				'cart_id'   => $woocommercevideocartobject->get_cart_id(),
				'room_name' => $woocommercevideocartobject->get_room_name(),
			)
		);

		\wp_cache_set(
			$cache_key,
			$woocommercevideocartobject->to_json(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_record_by_cart_id',
				)
			)
		);
		\wp_cache_delete(
			$woocommercevideocartobject->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return $woocommercevideocartobject;
	}

	/**
	 * Delete a Sync Record from the database
	 *
	 * @param int $record_id - The Record ID to Delete.
	 *
	 * @return null
	 * @throws \Exception When failing to delete.
	 */
	public function delete_record( $record_id ) {
		global $wpdb;

		$cache_key = $record_id;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$this->get_main_table_name(),
			array(
				'record_id' => $record_id,
			)
		);

		\wp_cache_delete( $cache_key, implode( '::', array( __CLASS__, 'get_record_by_record_id' ) ) );
		\wp_cache_delete(
			$record_id,
			implode(
				'::',
				array(
					__CLASS__,
					'get_record_by_record_id',
				)
			)
		);

		return null;
	}

	/**
	 * Delete a Sync Record from the database
	 *
	 * @param string $room_name - The Room Name to Clear Sync on.
	 *
	 * @return null
	 * @throws \Exception When failing to delete.
	 */
	public function delete_sync_basket( string $room_name ) {
		global $wpdb;

		$cart_id   = WooCommerce::SETTING_BASKET_REQUEST_ON;
		$cache_key = $cart_id;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$success = $wpdb->delete(
			$this->get_main_table_name(),
			array(
				'cart_id'   => $cart_id,
				'room_name' => $room_name,
			)
		);

		\wp_cache_delete(
			$cart_id
		);

		if ( $success ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete a Cart Object from the database
	 *
	 * @param WooCommerceVideoCart $woocommercevideocartobject The Cart Object to delete.
	 *
	 * @return null
	 * @throws \Exception When failing to delete.
	 */
	public function delete( WooCommerceVideoCart $woocommercevideocartobject ) {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$woocommercevideocartobject->get_cart_id(),
			$woocommercevideocartobject->get_room_name()
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$this->get_main_table_name(),
			array(
				'cart_id'   => $woocommercevideocartobject->get_cart_id(),
				'room_name' => $woocommercevideocartobject->get_room_name(),
			)
		);

		\wp_cache_delete( $cache_key, implode( '::', array( __CLASS__, 'get_record_by_cart_id' ) ) );
		\wp_cache_delete(
			$woocommercevideocartobject->get_cart_id(),
			implode(
				'::',
				array(
					__CLASS__,
					'get_by_cart_id',
				)
			)
		);

		return null;
	}

	/**
	 * Get Queue Records.
	 *
	 * @param string $cart_id The ID to match on.
	 * @param string $room_name The room name to query.
	 *
	 * @return array
	 */
	public function get_queue_records( string $cart_id, string $room_name ): array {
		global $wpdb;

		$cache_key = $this->create_cache_key(
			$cart_id,
			$room_name
		);

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( false === $result ) {

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						'
							SELECT record_id
							FROM ' . /*phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared*/ $this->get_main_table_name() . '
							WHERE room_name = %s AND cart_id =%s 
							ORDER BY record_id ASC
						',
						array(
							$room_name,
							$cart_id,
						)
					)
				);

			$result = array_map(
				function ( $row ) {
					return (int) $row->record_id;
				},
				$rows
			);

			\wp_cache_set( $cache_key, $result, __METHOD__ );
		}
		return $result;
	}

	/**
	 * Get Current Basket Sync Records
	 *
	 * @param ?string $room_name - The room type to query.
	 *
	 * @return array
	 */
	public function get_current_basket_sync_queue_records( string $room_name ): ?array {
		global $wpdb;

		// Exit if Sync is turned off.
		if ( ! Factory::get_instance( HostManagement::class )->is_sync_available( $room_name ) ) {
			return null;
		}

		$cart_id   = WooCommerce::SETTING_BASKET_REQUEST_USER;
		$cache_key = $cart_id;

		if ( ! $cart_id ) {
			$cache_key = '__ALL__';
		}

		$result = \wp_cache_get( $cache_key, __METHOD__ );

		if ( $result ) {
			return WooCommerceVideoCart::from_json( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				'
				SELECT 
			       cart_id,
				   source_cart_id,
			       room_name,
			       cart_data, 
			       product_id, 
			       quantity,
			       variation_id,
			       single_product, 
			       timestamp, 
				   record_id
				FROM ' . /* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared */ $this->get_main_table_name() . '
				WHERE cart_id = %s AND room_name = %s 
			',
				$cart_id,
				$room_name
			)
		);

			$result = array_map(
				function ( $row ) {
					$item = new WooCommerceVideoCart(
						$row->cart_id,
						$row->source_cart_id,
						$row->room_name,
						$row->cart_data,
						$row->product_id,
						$row->quantity,
						$row->variation_id,
						(bool) $row->single_product,
						$row->timestamp,
						$row->id,
					);
					return $item;
				},
				$rows
			);

			\wp_cache_set( $cache_key, $result, __METHOD__ );

			return $result;
	}

}
