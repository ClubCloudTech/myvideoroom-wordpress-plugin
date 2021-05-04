<?php
/**
 * Represents a myvideoroom app shortcode
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

namespace MyVideoRoomPlugin\Visualiser;

/**
 * Class MyVideoRoomApp
 */
class MyVideoRoomApp {
	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_layout(): string {
		return $this->layout;
	}

	/**
	 * @return bool
	 */
	public function is_admin(): bool {
		return $this->admin;
	}

	/**
	 * @return bool
	 */
	public function is_reception(): bool {
		return $this->reception;
	}

	/**
	 * @return bool
	 */
	public function is_floorplan_enabled(): bool {
		return $this->floorplan_enabled;
	}

	/**
	 * @return string
	 */
	public function get_reception_id(): string {
		return $this->reception_id;
	}

	/**
	 * @return string
	 */
	public function get_reception_video(): string {
		return $this->reception_video;
	}

	/**
	 * @return bool
	 */
	public function is_lobby(): bool {
		return $this->lobby;
	}

	public const MYVIDEOROOM_APP_SHORTCODE = 'myvideoroom';

	/**
	 * The name of the room
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * The layout of the room
	 *
	 * @var string
	 */
	private string $layout;

	/**
	 * Is this user an admin?
	 *
	 * @var bool
	 */
	private bool $admin = false;

	/**
	 * Is the reception enabled
	 *
	 * @var bool
	 */
	private bool $reception = false;

	/**
	 * Is the Disable Floorplan Setting enabled
	 *
	 * @var bool
	 */
	private bool $floorplan_enabled = true;

	/**
	 * The layout of the reception
	 *
	 * @var string
	 */
	private string $reception_id = '';

	/**
	 * The URL of the reception Video
	 *
	 * @var string
	 */
	private string $reception_video = '';

	/**
	 * Is the lobby enabled
	 *
	 * @var bool
	 */
	private bool $lobby = false;

	/**
	 * Create an instance - allows for easier chaining
	 *
	 * @param string $name The name of the room.
	 * @param string $layout The layout.
	 *
	 * @return MyVideoRoomApp
	 */
	public static function create_instance( string $name, string $layout ): self {
		return new self( $name, $layout );
	}

	/**
	 * MyVideoRoomApp constructor.
	 *
	 * @param string $name The name of the room.
	 * @param string $layout The layout.
	 */
	public function __construct(
		string $name,
		string $layout
	) {
		$this->name   = $name;
		$this->layout = $layout;
	}

	/**
	 * Enable the reception
	 *
	 * @return $this
	 */
	public function enable_reception(): self {
		$this->reception = true;
		return $this;
	}

	/**
	 * Disable the reception
	 *
	 * @return $this
	 */
	public function disable_reception(): self {
		$this->reception = false;
		return $this;
	}

	/**
	 * Set the reception id
	 *
	 * @param string $reception_id The reception id.
	 *
	 * @return $this
	 */
	public function set_reception_id( string $reception_id ): self {
		$this->reception_id = $reception_id;
		return $this;
	}

	/**
	 * Set_reception_video_url.
	 *
	 * @param string $reception_video_url - Sets it in object.
	 * @return self - the URL object
	 */
	public function set_reception_video_url( string $reception_video_url ): self {
		$this->reception_video = $reception_video_url;
		return $this;
	}

	/**
	 * Set this user as an admin
	 *
	 * @return $this
	 */
	public function enable_admin(): self {
		$this->admin = true;
		return $this;
	}

	/**
	 * Delegate_admin_to_wordpress admins and not do full security.
	 *
	 * @return self
	 */
	public function delegate_admin_to_wordpress(): self {
		$this->admin = false;
		return $this;
	}

	/**
	 * Set Disable Floorplan Flag
	 *
	 * @return $this
	 */
	public function enable_floorplan(): self {
		$this->floorplan_enabled = true;
		return $this;
	}

	/**
	 * Disable_floorplan -Removes Floorplan setting from object.
	 *
	 * @return self
	 */
	public function disable_floorplan(): self {
		$this->floorplan_enabled = false;
		return $this;
	}

	/**
	 * Enable the lobby
	 *
	 * @return $this
	 */
	public function enable_lobby(): self {
		$this->lobby = true;
		return $this;
	}

	/**
	 * Assembles and constructs shortcode parameters from object array
	 * Returns the array of processed deduplicated options
	 *
	 * @param  ?string $text_safe - passes to main constructor a variable that instructs it to not render the shortcode and instead returns the string only (used for visualiser).
	 * @return string
	 */
	public function output_shortcode( string $text_safe = null ): string {
		$shortcode_array = array(
			'name'   => $this->name,
			'layout' => $this->layout,
		);

		if ( true === $this->admin ) {
			$shortcode_array['admin'] = true;
		} elseif ( false === $this->admin ) {
			$shortcode_array['admin'] = false;
		}

		// Reception Setting. Note it can be modified by other parameters like Floorplan which require Reception to be on.
		if ( $this->reception ) {
			$shortcode_array['reception'] = true;

			if ( $this->reception_id ) {
				$shortcode_array['reception-id'] = $this->reception_id;
			}
		}

		// Floorplan setting.
		if ( true !== $this->admin && $this->floorplan_enabled ) {
			$shortcode_array['floorplan'] = true;
		}

		// Lobby setting.
		if ( $this->lobby ) {
			$shortcode_array['lobby'] = true;
		}

		if ( $this->reception_video ?? false ) {
			$shortcode_array['reception-video'] = $this->reception_video;
		}

		return $this->render_shortcode( self::MYVIDEOROOM_APP_SHORTCODE, $shortcode_array, $text_safe );
	}
	/**
	 * Render_shortcode for Visualiser.
	 *
	 * @param  string  $shortcode input from array assembler.
	 * @param  array   $params    status array.
	 * @param  ?string $text_safe flag to indicate text only and non rendered return - used for diplaying shortcode text.
	 * @return string
	 */
	protected function render_shortcode( string $shortcode, array $params, string $text_safe = null ): string {
		$output = $shortcode;

		foreach ( $params as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$output .= ' ' . $key . '=true';
				} else {
					$output .= ' ' . $key . '=false';
				}
			} else {

				$output .= ' ' . $key . '="' . $value . '"';
			}
		}
		// Function Change to allow just the return of the Shortcode text rather than execution.
		if ( 'shortcode-view-only' === $text_safe ) {
			return $output;
		}

		$output = '[' . $output . ']';

		return \do_shortcode( $output );
	}
}
