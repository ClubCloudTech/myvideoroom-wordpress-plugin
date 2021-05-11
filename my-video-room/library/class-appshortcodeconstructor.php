<?php
/**
 * Represents a MyVideoRoom app shortcode
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\AppShortcode;

/**
 * Class AppShortcodeConstructor
 */
class AppShortcodeConstructor extends ShortcodeConstructor {
	/**
	 * The name of the room
	 *
	 * @var ?string
	 */
	private ?string $name = null;

	/**
	 * The layout of the room
	 *
	 * @var ?string;
	 */
	private ?string $layout = null;

	/**
	 * Is this user a video host?
	 *
	 * @var bool
	 */
	private ?bool $host = null;

	/**
	 * Is the reception enabled
	 *
	 * @var bool
	 */
	private bool $reception = false;

	/**
	 * Is the floorplan enabled for guests
	 *
	 * @var bool
	 */
	private bool $floorplan_enabled = true;

	/**
	 * The layout of the reception
	 *
	 * @var ?string
	 */
	private ?string $reception_id = null;

	/**
	 * The URL of the reception Video
	 *
	 * @var ?string
	 */
	private ?string $reception_video = null;

	/**
	 * The name of the user, defaults to the WordPress name
	 *
	 * @var ?string
	 */
	private ?string $user_name = null;

	/**
	 * A random hash to ensure room uniqueness even with same name
	 *
	 * @var ?string
	 */
	private ?string $seed = null;

	private array $custom_settings = array();

	// --

	/**
	 * MyVideoRoomApp constructor.
	 */
	public function __construct() {
		parent::__construct( AppShortcode::SHORTCODE_TAG );
	}

	/**
	 * Create an instance - allows for easier chaining
	 *
	 * @return AppShortcodeConstructor
	 */
	public static function create_instance(): self {
		return new self();
	}

	public function add_custom_string_param( string $key, string $value ) {
		$this->custom_settings[ $key ] = $value;
	}

	/**
	 * Get the name of the room
	 *
	 * @return ?string
	 */
	public function get_name(): ?string {
		return $this->name;
	}

	/**
	 * Set the name of the room
	 *
	 * @param string $name The name of the room.
	 *
	 * @return $this
	 */
	public function set_name( string $name ): self {
		$this->name = $name;
		return $this;
	}

	/**
	 * Get the layout
	 *
	 * @return ?string
	 */
	public function get_layout(): ?string {
		return $this->layout;
	}

	/**
	 * Set the id of the layout
	 *
	 * @param string $layout The id of the layout.
	 *
	 * @return $this
	 */
	public function set_layout( string $layout ): self {
		$this->layout = $layout;
		return $this;
	}

	/**
	 * Should the user be a video host
	 *
	 * @return ?bool
	 */
	public function is_host(): ?bool {
		return $this->host;
	}

	/**
	 * Is the reception enabled
	 *
	 * @return bool
	 */
	public function is_reception_enabled(): bool {
		return $this->reception;
	}

	/**
	 * Is the floorplan enabled
	 *
	 * @return bool
	 */
	public function is_floorplan_enabled(): bool {
		return ! $this->is_host() && $this->floorplan_enabled;
	}

	/**
	 * Get the reception id
	 *
	 * @return ?string
	 */
	public function get_reception_id(): ?string {
		return $this->reception_id;
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
	 * Get the reception video url
	 *
	 * @return ?string
	 */
	public function get_reception_video(): ?string {
		if ( $this->is_host() ) {
			return null;
		} else {
			return $this->reception_video;
		}
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
	 * Set_reception_video_url.
	 *
	 * @param string $reception_video_url - Sets it in object.
	 *
	 * @return self - the URL object
	 */
	public function set_reception_video_url( string $reception_video_url ): self {
		$this->reception_video = $reception_video_url;

		return $this;
	}

	/**
	 * Set this user as a video host
	 *
	 * @return $this
	 */
	public function set_as_host(): self {
		$this->host = true;
		return $this;
	}

	/**
	 * Set this user as a guest
	 *
	 * @return $this
	 */
	public function set_as_guest(): self {
		$this->host = false;
		return $this;
	}

	/**
	 * Delegate permissions to WordPress roles.
	 *
	 * @return self
	 */
	public function delegate_is_host_to_wordpress(): self {
		$this->host = null;
		return $this;
	}

	/**
	 * Disable floorplan
	 *
	 * @return self
	 */
	public function disable_floorplan(): self {
		$this->floorplan_enabled = false;

		return $this;
	}

	/**
	 * Set the user name
	 *
	 * @param string $user_name The name of the user.
	 *
	 * @return $this
	 */
	public function set_user_name( string $user_name ): self {
		$this->user_name = $user_name;
		return $this;
	}

	/**
	 * Get the user name
	 *
	 * @return string|null
	 */
	public function get_user_name(): ?string {
		return $this->user_name;
	}

	/**
	 * Set a random seed to guarantee room uniqueness
	 *
	 * @param string $seed A random string.
	 *
	 * @return $this
	 */
	public function set_seed( string $seed ): self {
		$this->seed = $seed;
		return $this;
	}

	/**
	 * Get a random seed to guarantee room uniqueness
	 *
	 * @return string|null
	 */
	public function get_seed(): ?string {
		return $this->seed;
	}

	/**
	 * Assembles and constructs shortcode parameters from object array
	 * Returns the array of processed deduplicated options
	 *
	 * @return string
	 */
	public function output_shortcode_text(): string {
		$shortcode_array = array();

		if ( $this->get_layout() ) {
			$shortcode_array['layout'] = $this->get_layout();
		}

		if ( $this->get_name() ) {
			$shortcode_array['name'] = $this->get_name();
		}

		if ( true === $this->is_host() ) {
			$shortcode_array['host'] = true;
		} elseif ( false === $this->host ) {
			$shortcode_array['host'] = false;
		}

		if ( ! $this->is_host() && $this->is_reception_enabled() ) {
			$shortcode_array['reception'] = true;

			if ( $this->reception_id ) {
				$shortcode_array['reception-id'] = $this->get_reception_id();
			}
		}

		if ( $this->is_floorplan_enabled() ) {
			$shortcode_array['floorplan'] = true;
		}

		if ( $this->get_reception_video() ) {
			$shortcode_array['reception-video'] = $this->get_reception_video();
		}

		if ( $this->get_user_name() ) {
			$shortcode_array['user-name'] = $this->get_user_name();
		}

		if ( $this->get_seed() ) {
			$shortcode_array['seed'] = $this->get_seed();
		}

		foreach ( $this->custom_settings as $key => $custom_setting ) {
			$shortcode_array[ $key ] = $custom_setting;
		}

		$shortcode_array = apply_filters( 'myvideoroom_appshortcode_output', $shortcode_array );

		return $this->get_shortcode_text( $shortcode_array );
	}
}
