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

	/**
	 *  Room Name.
	 *
	 * @var string $room_name - The room name.
	 */
	private string $room_name;

	/**
	 *  Layout ID
	 *
	 * @var string $layout_id - The Room Template.
	 */
	private ?string $layout_id;

	/**
	 *  Reception ID
	 *
	 * @var string $reception_id - The Reception Template.
	 */
	private ?string $reception_id;

	/**
	 *  Reception Enabled
	 *
	 * @var bool $reception_enabled - The State of the Reception.
	 */
	private bool $reception_enabled;

	/**
	 *  Reception Video Enabled.
	 *
	 * @var bool $reception_video_enabled - The Custom Reception Video Override State.
	 */
	private bool $reception_video_enabled;

	/**
	 *  Reception Video URL.
	 *
	 * @var string $reception_video_url - The Custom Reception Video URL to Display.
	 */
	private ?string $reception_video_url;

	/**
	 *  Show Floorplan.
	 *
	 * @var bool show_floorplan - The Floorplan (show Room Layout for visitors) setting
	 */
	private bool $show_floorplan;

	/**
	 * UserVideoPreference constructor.
	 *
	 * @param string      $room_name - for use.
	 * @param string|null $layout_id - room template.
	 * @param string|null $reception_id - reception template.
	 * @param bool        $reception_enabled - status of Reception.
	 * @param bool        $reception_video_enabled - status of Reception Video State.
	 * @param string      $reception_video_url - URL of the Custom Reception Video.
	 * @param bool        $show_floorplan - status of Show Floorplan (disable guest room layout) State.
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
	 * Returns Room Name from Object
	 *
	 * @return string - the room name.
	 */
	public function get_room_name(): string {
		return $this->room_name;
	}

	/**
	 * Returns Layout Template Name from Object
	 *
	 * @return string - the Template (layout-ID).
	 */
	public function get_layout_id(): ?string {
		return $this->layout_id;
	}

	/**
	 * Sets the Layout Template Name to Object
	 *
	 * @param string $layout_id - the id of the object to return.
	 */
	public function set_layout_id( string $layout_id = null ): UserVideoPreference {
		$this->layout_id = $layout_id;
		return $this;
	}

	/**
	 * Returns Reception Template Name from Object
	 *
	 * @return string - the Reception Template.
	 */
	public function get_reception_id(): ?string {
		return $this->reception_id;
	}

	/**
	 * Sets the Layout Template Name to Object
	 *
	 * @param string $reception_id - the template to return.
	 */
	public function set_reception_id( string $reception_id = null ): UserVideoPreference {
		$this->reception_id = $reception_id;
		return $this;
	}

	/**
	 * Checks on/off status of Reception from Object.
	 *
	 * @return UserVideoPreference
	 */
	public function is_reception_enabled(): bool {
		return $this->reception_enabled;
	}

	/**
	 * Checks on/off status of Reception from Object.
	 *
	 * @param bool $reception_enabled - checks on/off status of Reception from Object.
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_enabled( bool $reception_enabled ): UserVideoPreference {
		$this->reception_enabled = $reception_enabled;
		return $this;
	}

	/**
	 * Returns whether or not Custom Video is enabled.
	 *
	 * @return bool
	 */
	public function get_reception_video_enabled_setting(): bool {
		return $this->reception_video_enabled;
	}

	/**
	 * Checks on/off status of Reception from Object.
	 *
	 * @param bool $reception_video_enabled - checks on/off status of Reception Video from Object.
	 *
	 * @return UserVideoPreference
	 */
	public function set_reception_video_enabled_setting( bool $reception_video_enabled ): UserVideoPreference {
		$this->reception_video_enabled = $reception_video_enabled;
		return $this;
	}

	/**
	 * Sets the Custom Video status in object.
	 *
	 * @param string $reception_video_url - checks on/off status of Reception Video is on from Object.
	 * @return UserVideoPreference
	 * */
	public function set_reception_video_url_setting( string $reception_video_url = null ): UserVideoPreference {
		$this->reception_video_url = $reception_video_url;
		return $this;
	}

	/**
	 * Checks on/off status of Reception from Object.
	 *
	 * @return bool $reception_video_enabled - checks on/off status of Reception Video from Object.
	 **/
	public function get_reception_video_url_setting() {
		return $this->reception_video_url;
	}

	/**
	 * Checks on/off status of FloorPlan from Object.
	 *
	 * @return bool $reception_video_enabled - checks on/off status of Reception Video from Object.
	 **/
	public function get_show_floorplan_setting(): bool {
		return $this->show_floorplan;
	}

	/**
	 * Checks the Status of the FloorPlan Setting.
	 *
	 * @param bool $show_floorplan - the disable guest layout setting - from the object.
	 * @return UserVideoPreference
	 */
	public function set_show_floorplan_setting( bool $show_floorplan ): UserVideoPreference {
		$this->show_floorplan = $show_floorplan;
		return $this;
	}

}
