<?php
/**
 * User Video Preference Entity Object
 *
 * @package MyVideoRoomPlugin\Entity
 */

namespace MyVideoRoomPlugin\Entity;

/**
 * Class UserVideoPreference
 */
class UserVideoPreference {

	/**
	 * User_id
	 *
	 * @var int $user_id
	 */
	private int $user_id;

	/**
	 * Room_name
	 *
	 * @var string $room_name
	 */
	private string $room_name;

	/**
	 * Layout_id
	 *
	 * @var ?string $layout_id
	 */
	private ?string $layout_id;

	/**
	 * Reception_id
	 *
	 * @var ?string $reception_id
	 */
	private ?string $reception_id;

	/**
	 * Reception_enabled
	 *
	 * @var bool $reception_enabled
	 */
	private bool $reception_enabled;

	/**
	 * Reception_video_enabled
	 *
	 * @var bool $reception_video_enabled
	 */
	private bool $reception_video_enabled;

	/**
	 * Reception_video_url
	 *
	 * @var ?string $reception_video_url
	 */
	private ?string $reception_video_url;

	/**
	 * Show_floorplan
	 *
	 * @var bool $show_floorplan
	 */
	private bool $show_floorplan;

	/**
	 * Timestamp
	 *
	 * @var int $timestamp - the Timestamp.
	 */
	private ?int $timestamp;

	/**
	 * Original Room Name
	 *
	 * @var ?string $original_room_name
	 */
	private ?string $original_room_name;

	/**
	 * UserVideoPreference constructor.
	 *
	 * @param int     $user_id                 Userid.
	 * @param string  $room_name               Room Name.
	 * @param ?string $layout_id               Video Template.
	 * @param ?string $reception_id            Reception Template.
	 * @param bool    $reception_enabled       Reception Status.
	 * @param bool    $reception_video_enabled Reception Video Status.
	 * @param ?string $reception_video_url     Reception Video Path.
	 * @param bool    $show_floorplan          Show Video Template to Guests Status.
	 * @param ?int    $timestamp               Last Updated Timestamp.
	 * @param ?string $original_room_name      The unmodified room name as it appears in storage db.
	 */
	public function __construct(
		int $user_id,
		string $room_name,
		string $layout_id = null,
		string $reception_id = null,
		bool $reception_enabled = false,
		bool $reception_video_enabled = false,
		string $reception_video_url = null,
		bool $show_floorplan = false,
		?int $timestamp = null,
		string $original_room_name = null

	) {
		$this->user_id                 = $user_id;
		$this->room_name               = $room_name;
		$this->layout_id               = $layout_id;
		$this->reception_id            = $reception_id;
		$this->reception_enabled       = $reception_enabled;
		$this->reception_video_enabled = $reception_video_enabled;
		$this->reception_video_url     = $reception_video_url;
		$this->show_floorplan          = $show_floorplan;
		$this->timestamp               = $timestamp;
		$this->original_room_name      = $original_room_name;
	}

	/**
	 * Create from a JSON object
	 * Note - Original Room Name does not get stored in JSON as it does not get stored in database.
	 *
	 * @param string $json The JSON representation of the object.
	 *
	 * @return ?\MyVideoRoomPlugin\Entity\UserVideoPreference
	 */
	public static function from_json( string $json ): ?self {
		$data = json_decode( $json );

		if ( $data ) {
			return new self(
				$data->user_id,
				$data->room_name,
				$data->layout_id,
				$data->reception_id,
				$data->reception_enabled,
				$data->reception_video_enabled,
				$data->reception_video_url,
				$data->show_floorplan,
				$data->timestamp,
			);
		}

		return null;
	}

	/**
	 * Convert to JSON
	 * Used for caching.
	 * Note - Original Room Name does not get stored in JSON as it does not get stored in database.
	 *
	 * @return string
	 */
	public function to_json(): string {
		return wp_json_encode(
			array(
				'user_id'                 => $this->user_id,
				'room_name'               => $this->room_name,
				'layout_id'               => $this->layout_id,
				'reception_id'            => $this->reception_id,
				'reception_enabled'       => $this->reception_enabled,
				'reception_video_enabled' => $this->reception_video_enabled,
				'reception_video_url'     => $this->reception_video_url,
				'show_floorplan'          => $this->show_floorplan,
				'timestamp'               => $this->timestamp,
			)
		);
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
	 * Gets Template (layout) ID.
	 *
	 * @return string
	 */
	public function get_layout_id(): ?string {
		return $this->layout_id;
	}

	/**
	 * Sets Template ID.
	 *
	 * @param string|null $layout_id - the Template.
	 *
	 * @return UserVideoPreference
	 */
	public function set_layout_id( string $layout_id = null ): UserVideoPreference {
		$this->layout_id = $layout_id;

		return $this;
	}

	/**
	 * Gets Reception Template
	 *
	 * @return string
	 */
	public function get_reception_id(): ?string {
		return $this->reception_id;
	}

	/**
	 * Sets Reception Template.
	 *
	 * @param string|null $reception_id - The Template.
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_id( string $reception_id = null ): UserVideoPreference {
		$this->reception_id = $reception_id;

		return $this;
	}

	/**
	 * Gets Reception Enabled State.
	 *
	 * @return bool
	 */
	public function is_reception_enabled(): bool {
		return $this->reception_enabled;
	}

	/**
	 * Sets Reception Enabled State
	 *
	 * @param bool $reception_enabled - Reception Status.
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_enabled( bool $reception_enabled ): UserVideoPreference {
		$this->reception_enabled = $reception_enabled;

		return $this;
	}

	/**
	 * Gets Reception Video Enabled State.
	 *
	 * @return bool
	 */
	public function is_reception_video_enabled(): bool {
		return $this->reception_video_enabled;
	}

	/**
	 * Sets Reception Video Enabled State.
	 *
	 * @param bool $reception_video_enabled - Reception State.
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_video_enabled_setting( bool $reception_video_enabled ): UserVideoPreference {
		$this->reception_video_enabled = $reception_video_enabled;

		return $this;
	}

	/**
	 * Sets Reception Video URL.
	 *
	 * @param string|null $reception_video_url - The URL of Custom Video.
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_video_url_setting( string $reception_video_url = null ): UserVideoPreference {
		$this->reception_video_url = $reception_video_url;

		return $this;
	}

	/**
	 * Gets Custom Reception Video Status.
	 *
	 * @return ?string
	 */
	public function get_reception_video_url_setting(): ?string {
		return $this->reception_video_url;
	}

	/**
	 * Gets Floorplan (Disable Video Template to Guests) setting.
	 *
	 * @return bool
	 */
	public function is_floorplan_enabled(): bool {
		return $this->show_floorplan;
	}

	/**
	 * Sets Floorplan (Disable Video Template to Guests) setting.
	 *
	 * @param bool $show_floorplan - The Disable Video template setting.
	 *
	 * @return UserVideoPreference
	 */
	public function set_show_floorplan_setting( bool $show_floorplan ): UserVideoPreference {
		$this->show_floorplan = $show_floorplan;

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
	public function set_timestamp( int $timestamp ): UserVideoPreference {
		$this->timestamp = $timestamp;

		return $this;
	}

	/**
	 * Sets Original Room Name.
	 *
	 * @param string|null $original_room_name - The unmodified room name as it appears in storage db.
	 *
	 * @return UserVideoPreference
	 */
	public function set_original_room_name( string $original_room_name = null ): UserVideoPreference {
		$this->original_room_name = $original_room_name;

		return $this;
	}

	/**
	 * Gets Original Room Name.
	 *
	 * @return ?string
	 */
	public function get_original_room_name(): ?string {
		return $this->original_room_name;
	}
}
