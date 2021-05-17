<?php
/**
 * A User Video Preference
 *
 * @package MyVideoRoomExtrasPlugin\Entity
 */

namespace MyVideoRoomPlugin\Module\Security\Entity;

/**
 * Class SecurityVideoPreference
 */
class SecurityVideoPreference {

	/**
	 * User_id
	 *
	 * @var int $user_id
	 */
	private int $user_id;

	/**
	 * Room_name
	 *
	 * @var int $room_name
	 */
	private string $room_name;

	/**
	 * Allowed_roles
	 *
	 * @var int $allowed_roles
	 */
	private ?string $allowed_roles;

	/**
	 * Blocked_roles
	 *
	 * @var int $blocked_roles
	 */
	private ?string $blocked_roles;

	/**
	 * Room_disabled
	 *
	 * @var int $room_disabled
	 */
	private bool $room_disabled;

	/**
	 * Site_override_enabled
	 *
	 * @var int $user_id
	 */
	private bool $site_override_enabled;

	/**
	 * Anonymous_enabled
	 *
	 * @var int $anonymous_enabled
	 */
	private bool $anonymous_enabled;

	/**
	 * Allow_role_control_enabled
	 *
	 * @var int $allow_role_control_enabled
	 */
	private bool $allow_role_control_enabled;

	/**
	 * User_id
	 *
	 * @var int $user_id
	 */
	private bool $block_role_control_enabled;

	/**
	 * Restrict_group_to_members_setting
	 *
	 * @var int $restrict_group_to_members_setting
	 */
	private ?string $restrict_group_to_members_setting;

	/**
	 * Bp_friends_setting
	 *
	 * @var int $bp_friends_setting
	 */
	private ?string $bp_friends_setting;




	/**
	 * SecurityVideoPreference constructor.
	 *
	 * @param int         $user_id - The User ID.
	 * @param string      $room_name - The Room Name.
	 * @param string|null $allowed_roles - Roles Allowed to be Hosted/Shown.
	 * @param string|null $blocked_roles - Invert Roles to Blocked Instead.
	 * @param bool        $room_disabled - Disable Room from Displaying.
	 * @param bool        $anonymous_enabled - Disable Room from Displaying to Signed Out Users.
	 * @param bool        $allow_role_control_enabled - Disable Room to users who arent in specific roles.
	 * @param bool        $block_role_control_enabled - Flips Allowed Roles to Blocked Roles instead.
	 * @param bool        $site_override_enabled - Overrides User settings with central ones.
	 * @param string|null $restrict_group_to_members_setting - Blocks rooms from outside users (used for BuddyPress initially but can use any group plugin).
	 * @param string|null $bp_friends_setting - Setting for BuddyPress Friends (can be other platforms with plugins).
	 */
	public function __construct(
		int $user_id,
		string $room_name,
		string $allowed_roles = null,
		string $blocked_roles = null,
		bool $room_disabled = false,
		bool $anonymous_enabled = false,
		bool $allow_role_control_enabled = false,
		bool $block_role_control_enabled = false,
		bool $site_override_enabled = false,
		string $restrict_group_to_members_setting = null,
		string $bp_friends_setting = null

	) {
		$this->user_id                           = $user_id;
		$this->room_name                         = $room_name;
		$this->allowed_roles                     = $allowed_roles;
		$this->blocked_roles                     = $blocked_roles;
		$this->room_disabled                     = $room_disabled;
		$this->anonymous_enabled                 = $anonymous_enabled;
		$this->allow_role_control_enabled        = $allow_role_control_enabled;
		$this->block_role_control_enabled        = $block_role_control_enabled;
		$this->site_override_enabled             = $site_override_enabled;
		$this->restrict_group_to_members_setting = $restrict_group_to_members_setting;
		$this->bp_friends_setting                = $bp_friends_setting;
	}

	/**
	 * Gets User ID.
	 *
	 * @return int
	 */
	public function get_user_id(): int {
		return $this->user_id;
	}

	/**
	 * Gets Room Name.
	 *
	 * @return string
	 */
	public function get_room_name(): string {
		return $this->room_name;
	}

	/**
	 * Gets Allowed Roles.
	 *
	 * @return string
	 */
	public function get_allowed_roles(): ?string {
		return $this->allowed_roles;
	}

	/**
	 * Sets Roles Allowed.
	 *
	 * @param string|null $allowed_roles - the roles.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_allowed_roles( string $allowed_roles = null ): SecurityVideoPreference {
		$this->allowed_roles = $allowed_roles;
		return $this;
	}

	/**
	 * Gets Blocked Roles.
	 *
	 * @return string
	 */
	public function get_blocked_roles(): ?string {
		return $this->blocked_roles;
	}

	/**
	 * Sets Blocked Roles.
	 *
	 * @param string|null $blocked_roles - the roles.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_blocked_roles( string $blocked_roles = null ): SecurityVideoPreference {
		$this->blocked_roles = $blocked_roles;
		return $this;
	}

	/**
	 * Gets Room Disabled State.
	 *
	 * @return bool
	 */
	public function is_room_disabled(): bool {
		return $this->room_disabled;
	}

	/**
	 * Sets Room Disabled State.
	 *
	 * @param bool $room_disabled - sets the state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_room_disabled( bool $room_disabled ): SecurityVideoPreference {
		$this->room_disabled = $room_disabled;
		return $this;
	}

	/**
	 * Gets Room Anonymous Access State.
	 *
	 * @return bool
	 */
	public function is_anonymous_enabled(): bool {
		return $this->anonymous_enabled;
	}

	/**
	 * Sets Room Anonymous Access State.
	 *
	 * @param bool $anonymous_enabled - The disabled state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_anonymous_enabled( bool $anonymous_enabled ): SecurityVideoPreference {
		$this->anonymous_enabled = $anonymous_enabled;
		return $this;
	}

	/**
	 * Gets Role Control State.
	 *
	 * @return bool
	 */
	public function is_allow_role_control_enabled(): bool {
		return $this->allow_role_control_enabled;
	}

	/**
	 * Sets Role Control State.
	 *
	 * @param bool $allow_role_control_enabled - the feature state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_allow_role_control_enabled( bool $allow_role_control_enabled ): SecurityVideoPreference {
		$this->allow_role_control_enabled = $allow_role_control_enabled;
		return $this;
	}

	/**
	 * Gets Role Control State Block (block all but listed rather than allow all but listed).
	 *
	 * @return bool
	 */
	public function is_block_role_control_enabled(): bool {
		return $this->block_role_control_enabled;
	}

	/**
	 * Sets Role Control State Block (block all but listed rather than allow all but listed).
	 *
	 * @param bool $block_role_control_enabled - the block flag state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_block_role_control_enabled( bool $block_role_control_enabled ): SecurityVideoPreference {
		$this->block_role_control_enabled = $block_role_control_enabled;
		return $this;
	}

	/**
	 * Get Site Override State.
	 *
	 * @return bool
	 */
	public function check_site_override_setting(): bool {
		return $this->site_override_enabled;
	}

	/**
	 * Set Site Override State.
	 *
	 * @param bool $site_override_enabled - the state of override flag.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_site_override_setting( bool $site_override_enabled ): SecurityVideoPreference {
		$this->site_override_enabled = $site_override_enabled;
		return $this;
	}

	/**
	 * Get Restrict Group to Members State.
	 *
	 * @return bool
	 */
	public function check_restrict_group_to_members_setting(): ?string {
		return $this->restrict_group_to_members_enabled;
	}

	/**
	 * Set Restrict Group to Members State.
	 *
	 * @param bool $restrict_group_to_members_enabled - the state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_restrict_group_to_members_setting( $restrict_group_to_members_enabled ): SecurityVideoPreference {
		$this->restrict_group_to_members_enabled = $restrict_group_to_members_enabled;
		return $this;
	}

	/**
	 * Get BP Friends State.
	 *
	 * @return bool
	 */
	public function check_bp_friends_setting(): ?string {
		return $this->bp_friends_setting;
	}

	/**
	 * Set Restrict Group to Members State.
	 *
	 * @param bool $bp_friends_setting  - the BP friends block state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_bp_friends_setting( $bp_friends_setting ): SecurityVideoPreference {
		$this->bp_friends_setting = $bp_friends_setting;
		return $this;
	}






}
