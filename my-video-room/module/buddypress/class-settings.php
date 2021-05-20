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

	private int $id;

	private bool $restrict_group_to_members_enabled;

	private string $friend_restriction;

	/**
	 * Settings constructor.
	 *
	 * @param int    $id                                The record id.
	 * @param bool   $restrict_group_to_members_enabled
	 * @param string $friend_restriction
	 */
	public function __construct( int $id, bool $restrict_group_to_members_enabled, string $friend_restriction ) {
		$this->id                                = $id;
		$this->restrict_group_to_members_enabled = $restrict_group_to_members_enabled;
		$this->friend_restriction                = $friend_restriction;
	}

	public function get_id(): int {
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function is_restrict_group_to_members_enabled(): bool {
		return $this->restrict_group_to_members_enabled;
	}

	/**
	 * @return string
	 */
	public function get_friend_restriction(): string {
		return $this->friend_restriction;
	}
}
