<?php
/**
 * A User Video Preference
 *
 * @package MyVideoRoomPlugin\Entity
 */

namespace MyVideoRoomPlugin\Module\Security\Entity;

/**
 * Class SecurityVideoPreference
 */
class SecurityVideoPreference {

	/**
	 * The record id
	 *
	 * @var ?int
	 */
	private ?int $id;

	/**
	 * User_id
	 *
	 * @var int
	 */
	private int $user_id;

	/**
	 * Room_name
	 *
	 * @var string
	 */
	private string $room_name;

	/**
	 * Allowed_roles
	 *
	 * @var ?string
	 */
	private ?string $allowed_roles;

	/**
	 * Blocked_roles
	 *
	 * @var ?string
	 */
	private ?string $blocked_roles;

	/**
	 * Room_disabled
	 *
	 * @var bool
	 */
	private bool $room_disabled;

	/**
	 * Site_override_enabled
	 *
	 * @var bool
	 */
	private bool $site_override_enabled;

	/**
	 * Anonymous_enabled
	 *
	 * @var bool
	 */
	private bool $anonymous_enabled;

	/**
	 * Allow_role_control_enabled
	 *
	 * @var bool
	 */
	private bool $allow_role_control_enabled;

	/**
	 * User_id
	 *
	 * @var bool
	 */
	private bool $block_role_control_enabled;

	/**
	 * Restrict_group_to_members_setting
	 *
	 * @var ?bool
	 */
	private ?bool $restrict_group_to_members_enabled;

	/**
	 * Bp_friends_setting
	 *
	 * @var ?string
	 */
	private ?string $bp_friends_setting;

	/**
	 * Timestamp
	 *
	 * @var int $timestamp - the Timestamp.
	 */
	private ?int $timestamp;

	/**
	 * SecurityVideoPreference constructor.
	 *
	 * @param ?int    $id                                The record id.
	 * @param int     $user_id                           The User ID.
	 * @param string  $room_name                         The Room Name.
	 * @param ?string $allowed_roles                     Roles Allowed to be Hosted/Shown.
	 * @param ?string $blocked_roles                     Invert Roles to Blocked Instead.
	 * @param bool    $room_disabled                     Disable Room from Displaying.
	 * @param bool    $anonymous_enabled                 Disable Room from Displaying to Signed Out Users.
	 * @param bool    $allow_role_control_enabled        Disable Room to users who arent in specific roles.
	 * @param bool    $block_role_control_enabled        Flips Allowed Roles to Blocked Roles instead.
	 * @param bool    $site_override_enabled             Overrides User settings with central ones.
	 * @param ?string $restrict_group_to_members_enabled Blocks rooms from outside users (used for BuddyPress initially but can use any group plugin).
	 * @param ?string $bp_friends_setting                Setting for BuddyPress Friends (can be other platforms with plugins).
	 * @param ?int    $timestamp                         Last Updated Timestamp.
	 */
	public function __construct(
		?int $id,
		int $user_id,
		string $room_name,
		string $allowed_roles = null,
		string $blocked_roles = null,
		bool $room_disabled = false,
		bool $anonymous_enabled = false,
		bool $allow_role_control_enabled = false,
		bool $block_role_control_enabled = false,
		bool $site_override_enabled = false,
		string $restrict_group_to_members_enabled = null,
		string $bp_friends_setting = null,
		?int $timestamp = null

	) {
		$this->id                                = $id;
		$this->user_id                           = $user_id;
		$this->room_name                         = $room_name;
		$this->allowed_roles                     = $allowed_roles;
		$this->blocked_roles                     = $blocked_roles;
		$this->room_disabled                     = $room_disabled;
		$this->anonymous_enabled                 = $anonymous_enabled;
		$this->allow_role_control_enabled        = $allow_role_control_enabled;
		$this->block_role_control_enabled        = $block_role_control_enabled;
		$this->site_override_enabled             = $site_override_enabled;
		$this->restrict_group_to_members_enabled = $restrict_group_to_members_enabled;
		$this->bp_friends_setting                = $bp_friends_setting;
		$this->timestamp                         = $timestamp;
	}

	/**
	 * Create from a JSON object
	 *
	 * @param string $json The JSON representation of the object.
	 *
	 * @return ?\MyVideoRoomPlugin\Module\Security\Entity\SecurityVideoPreference
	 */
	public static function from_json( string $json ): ?self {
		$data = json_decode( $json );

		if ( $data ) {
			return new self(
				$data->id,
				$data->user_id,
				$data->room_name,
				$data->allowed_roles,
				$data->blocked_roles,
				$data->room_disabled,
				$data->anonymous_enabled,
				$data->allow_role_control_enabled,
				$data->block_role_control_enabled,
				$data->site_override_enabled,
				$data->restrict_group_to_members_enabled,
				$data->bp_friends_setting,
				$data->timestamp,
			);
		}

		return null;
	}

	/**
	 * Convert to JSON
	 * Used for caching.
	 *
	 * @return string
	 */
	public function to_json(): string {
		return wp_json_encode(
			array(
				'id'                                => $this->id,
				'user_id'                           => $this->user_id,
				'room_name'                         => $this->room_name,
				'allowed_roles'                     => $this->allowed_roles,
				'blocked_roles'                     => $this->blocked_roles,
				'room_disabled'                     => $this->room_disabled,
				'anonymous_enabled'                 => $this->anonymous_enabled,
				'allow_role_control_enabled'        => $this->allow_role_control_enabled,
				'block_role_control_enabled'        => $this->block_role_control_enabled,
				'site_override_enabled'             => $this->site_override_enabled,
				'restrict_group_to_members_enabled' => $this->restrict_group_to_members_enabled,
				'bp_friends_setting'                => $this->bp_friends_setting,
				'timestamp'                         => $this->timestamp,
			)
		);
	}

	/**
	 * Get the record id
	 *
	 * @return ?int
	 */
	public function get_id(): ?int {
		return $this->id;
	}

	/**
	 * Set the record id
	 *
	 * @param int $id - userid.
	 *
	 * @return $this
	 */
	public function set_id( int $id ): self {
		$this->id = $id;

		return $this;
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
	 * Set the user ID
	 *
	 * @param int $user_id The new user id.
	 *
	 * @return $this
	 */
	public function set_user_id( int $user_id ): self {
		$this->user_id = $user_id;

		return $this;
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
	 * @return array
	 */
	public function get_roles(): array {
		return explode( '|', $this->allowed_roles );
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
	public function is_site_override_enabled(): bool {
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
	 * @return ?bool
	 */
	public function is_restricted_to_group_to_members(): ?bool {
		return $this->restrict_group_to_members_enabled;
	}

	/**
	 * Set Restrict Group to Members State.
	 *
	 * @param bool $restrict_group_to_members_enabled - the state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_restrict_group_to_members_setting( bool $restrict_group_to_members_enabled ): SecurityVideoPreference {
		$this->restrict_group_to_members_enabled = $restrict_group_to_members_enabled;

		return $this;
	}

	/**
	 * Get BP Friends State.
	 *
	 * @return bool
	 */
	public function is_bp_friends_setting_enabled(): ?bool {
		return $this->bp_friends_setting;
	}

	/**
	 * Set Restrict Group to Members State.
	 *
	 * @param bool $bp_friends_setting - the BP friends block state.
	 *
	 * @return SecurityVideoPreference
	 */
	public function set_bp_friends_setting( bool $bp_friends_setting ): SecurityVideoPreference {
		$this->bp_friends_setting = $bp_friends_setting;

		return $this;
	}

	/**
	 * Gets Timestamp.
	 *
	 * @return int
	 */
	public function get_timestamp(): ?int {
		return $this->timestamp;
	}

	/**
	 * Sets Timestamp.
	 *
	 * @param int $timestamp - sets the Timestamp.
	 *
	 * @return int
	 */
	public function set_timestamp( int $timestamp ): SecurityVideoPreference {
		$this->timestamp = $timestamp;

		return $this;
	}

}
