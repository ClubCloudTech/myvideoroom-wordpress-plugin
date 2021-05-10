<?php
/**
 * Represents an admin notice
 *
 * @package MyVideoRoomPlugin\ValueObject
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\ValueObject;

/**
 * Class Notice
 */
class Notice {
	const TYPE_SUCCESS = 'success';
	const TYPE_WARNING = 'warning';
	const TYPE_ERROR   = 'error';

	const TYPES = array(
		self::TYPE_SUCCESS,
		self::TYPE_WARNING,
		self::TYPE_ERROR,
	);

	/**
	 * The type of notice - @see self::TYPES
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * The notice message
	 *
	 * @var string
	 */
	private string $message;

	/**
	 * Notice constructor.
	 *
	 * @param string $type    The type of notice - @see self::TYPES.
	 * @param string $message The notice message.
	 *
	 * @throws \Exception When an invalid type is passed.
	 */
	public function __construct( string $type, string $message ) {
		if ( ! in_array( $type, self::TYPES, true ) ) {
			throw new \Exception( 'Invalid type' );
		}

		$this->type    = $type;
		$this->message = $message;
	}

	/**
	 * Get the type of notice - @see self::TYPES
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get the notice message
	 *
	 * @return string
	 */
	public function get_message(): string {
		return $this->message;
	}
}
