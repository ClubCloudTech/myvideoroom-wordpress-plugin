<?php
/**
 * Handles activation and deactivation
 *
 * @package MyVideoRoomPlugin\Module\BuddyPress
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\BuddyPress;

/**
 * Class Settings
 */
class Settings {

	/**
	 * The record id
	 *
	 * @var integer
	 */
	private int $id;

	/**
	 * The member restriction string
	 *
	 * @var ?string
	 */
	private ?string $member_restriction;

	/**
	 * The friend restriction string
	 *
	 * @var ?string
	 */
	private ?string $friend_restriction;

	/**
	 * Settings constructor.
	 *
	 * @param int     $id                 The record id.
	 * @param ?string $member_restriction The current member restriction string.
	 * @param ?string $friend_restriction The current friend restriction string.
	 */
	public function __construct( int $id, ?string $member_restriction, ?string $friend_restriction ) {
		$this->id                 = $id;
		$this->member_restriction = $member_restriction;
		$this->friend_restriction = $friend_restriction;
	}

	/**
	 * Get the record id
	 *
	 * @return integer
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Get the current member restriction
	 *
	 * @return ?string
	 */
	public function get_member_restriction(): ?string {
		return $this->member_restriction;
	}

	/**
	 * Get the current friend restriction
	 *
	 * @return ?string
	 */
	public function get_friend_restriction(): ?string {
		return $this->friend_restriction;
	}
}
