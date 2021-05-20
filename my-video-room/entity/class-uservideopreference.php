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
	 * UserVideoPreference constructor.
	 *
	 * @param int     $user_id                  Userid.
	 * @param string  $room_name                Room Name.
	 * @param ?string $layout_id                Video Template.
	 * @param ?string $reception_id             Reception Template.
	 * @param bool    $reception_enabled        Reception Status.
	 * @param bool    $reception_video_enabled  Reception Video Status.
	 * @param ?string $reception_video_url      Reception Video Path.
	 * @param bool    $show_floorplan           Show Video Template to Guests Status.
	 */
	public function __construct(
		int $user_id,
		string $room_name,
		string $layout_id = null,
		string $reception_id = null,
		bool $reception_enabled = false,
		bool $reception_video_enabled = false,
		string $reception_video_url = null,
		bool $show_floorplan = false

	) {
		$this->user_id                 = $user_id;
		$this->room_name               = $room_name;
		$this->layout_id               = $layout_id;
		$this->reception_id            = $reception_id;
		$this->reception_enabled       = $reception_enabled;
		$this->reception_video_enabled = $reception_video_enabled;
		$this->reception_video_url     = $reception_video_url;
		$this->show_floorplan          = $show_floorplan;
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
	public function get_reception_video_enabled_setting(): bool {
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
	public function get_show_floorplan_setting(): bool {
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
}
