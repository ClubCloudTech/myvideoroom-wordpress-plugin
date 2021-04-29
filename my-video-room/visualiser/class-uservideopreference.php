<?php
/**
 * A User Video Preference
 *
 * @package MyVideoRoomPlugin\Visualiser
 */

namespace MyVideoRoomPlugin\Visualiser;

/**
 * Class UserVideoPreference
 */
class UserVideoPreference {

	private string $room_name;
	private ?string $layout_id;
	private ?string $reception_id;
	private bool $reception_enabled;
	private bool $reception_video_enabled;
	private ?string $reception_video_url;
	private bool $show_floorplan;

	/**
	 * UserVideoPreference constructor.
	 *
	 * @param string      $room_name - for use.
	 * @param string|null $layout_id - room template.
	 * @param string|null $reception_id - reception template.
	 * @param bool        $reception_enabled .
	 */
	public function __construct(
		
		string $room_name,
		string $layout_id = null,
		string $reception_id = null,
		bool $reception_enabled = false,
		bool $reception_video_enabled = false,
		string $reception_video_url = null,
		bool $show_floorplan = false

	) {
		$this->room_name               = $room_name;
		$this->layout_id               = $layout_id;
		$this->reception_id            = $reception_id;
		$this->reception_enabled       = $reception_enabled;
		$this->reception_video_enabled = $reception_video_enabled;
		$this->reception_video_url     = $reception_video_url;
		$this->show_floorplan          = $show_floorplan;
	}


	/**
	 * @return string
	 */
	public function get_room_name(): string {
		return $this->room_name;
	}

	/**
	 * @return string
	 */
	public function get_layout_id(): ?string {
		return $this->layout_id;
	}

	/**
	 * @param string|null $layout_id
	 *
	 * @return UserVideoPreference
	 */
	public function set_layout_id( string $layout_id = null ): UserVideoPreference {
		$this->layout_id = $layout_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_reception_id(): ?string {
		return $this->reception_id;
	}

	/**
	 * @param string|null $reception_id
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_id( string $reception_id = null ): UserVideoPreference {
		$this->reception_id = $reception_id;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_reception_enabled(): bool {
		return $this->reception_enabled;
	}

	/**
	 * @param bool $reception_enabled
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_enabled( bool $reception_enabled ): UserVideoPreference {
		$this->reception_enabled = $reception_enabled;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function get_reception_video_enabled_setting(): bool {
		return $this->reception_video_enabled;
	}

	/**
	 * @param bool $reception_enabled
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_video_enabled_setting( bool $reception_video_enabled ): UserVideoPreference {
		$this->reception_video_enabled = $reception_video_enabled;
		return $this;
	}


	/**
	 * @param string|null $layout_id
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_video_url_setting( string $reception_video_url = null ): UserVideoPreference {
		$this->reception_video_url = $reception_video_url;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_reception_video_url_setting() {
		return $this->reception_video_url;
	}


		/**
		 * @return bool
		 */
	public function get_show_floorplan_setting(): bool {
		return $this->show_floorplan;
	}

	/**
	 * @param bool $reception_enabled
	 *
	 * @return UserVideoPreference
	 */
	public function set_show_floorplan_setting( bool $show_floorplan ): UserVideoPreference {
		$this->show_floorplan = $show_floorplan;
		return $this;
	}












}
