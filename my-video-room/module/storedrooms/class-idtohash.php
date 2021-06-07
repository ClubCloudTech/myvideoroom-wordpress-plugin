<?php
/**
 * Converts incremented IDs to a 3 digit string.
 *
 * @package MyVideoRoomPlugin\Module\StoredRooms
 */

namespace MyVideoRoomPlugin\Module\StoredRooms;

use MyVideoRoomPlugin\Plugin;

/**
 * Class IdToHash
 */
class IdToHash {

	private const OUTPUT_BASE    = 36;
	private const DEFAULT_OFFSET = 129;

	/**
	 * A unique seed based on the nonce.
	 *
	 * @var int
	 */
	private int $seed;

	/**
	 * IdToHash constructor.
	 */
	public function __construct() {
		$nonce      = \get_option( Plugin::SETTING_NONCE );
		$this->seed = (int) round( \base_convert( $nonce, 16, 10 ) / 5000 ) - 100;
	}

	/**
	 * Get a hash from an numeric id.
	 *
	 * @param int $id The numeric ID to crate the hash from.
	 *
	 * @return string
	 */
	public function get_hash_from_id( int $id ): string {
		$id = $id + self::DEFAULT_OFFSET;

		$seed = $this->seed;

		$randomisation_factor = ( ( ( $seed + $id ) * 104513 ) % self::OUTPUT_BASE );

		$number = $id + $randomisation_factor + $seed;

		return base_convert( $randomisation_factor, 10, self::OUTPUT_BASE ) . base_convert( $number, 10, self::OUTPUT_BASE );
	}

	/**
	 * Convert a hash back to an id.
	 *
	 * @param string $hash The hash to convert back to an id.
	 *
	 * @return int
	 */
	public function get_id_from_hash( string $hash ): int {
		$factor = base_convert( $hash[0], self::OUTPUT_BASE, 10 );
		$number = base_convert( substr( $hash, 1 ), self::OUTPUT_BASE, 10 );

		$value = $number - $factor - $this->seed;

		return $value - self::DEFAULT_OFFSET;
	}
}
