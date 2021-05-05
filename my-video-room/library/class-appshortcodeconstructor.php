<?php
/**
 * Represents a myvideoroom app shortcode
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

namespace MyVideoRoomPlugin\Library;

use MyVideoRoomPlugin\AppShortcode;
use function do_shortcode;

/**
 * Class MyVideoRoomApp
 */
class AppShortcodeConstructor {
	/**
	 * The name of the room
	 *
	 * @var ?string
	 */
	private ?string $name = null;

	/**
	 * The layout of the room
	 *
	 * @var ?string = null;
	 */
	private ?string $layout;

	/**
	 * Is this user an admin?
	 *
	 * @var bool
	 */
	private ?bool $admin = null;

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

	/**
	 * MyVideoRoomApp constructor.
	 */
	public function __construct() {}

	/**
	 * Create an instance - allows for easier chaining
	 *
	 * @return AppShortcodeConstructor
	 */
	public static function create_instance(): self {
		return new self();
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
	 * Should the user be an admin
	 *
	 * @return ?bool
	 */
	public function is_admin(): ?bool {
		return $this->admin;
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
		return ! $this->is_admin() && $this->floorplan_enabled;
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
		if ( $this->is_admin() ) {
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
	 * Set this user as an admin
	 *
	 * @return $this
	 */
	public function enable_admin(): self {
		$this->admin = true;
		return $this;
	}

	/**
	 * Set this user as an non admin
	 *
	 * @return $this
	 */
	public function disable_admin(): self {
		$this->admin = false;
		return $this;
	}

	/**
	 * Delegate permissions to WordPress roles.
	 *
	 * @return self
	 */
	public function delegate_admin_to_wordpress(): self {
		$this->admin = null;
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
	 * @param bool $text_safe - passes to main constructor a variable that instructs it to not render the shortcode and instead returns the string only (used for visualiser).
	 *
	 * @return string
	 */
	public function output_shortcode( bool $text_safe = false ): string {
		$shortcode_array = array(
			'layout' => $this->get_layout(),
		);

		if ( $this->get_name() ) {
			$shortcode_array['name'] = $this->get_name();
		}

		if ( true === $this->is_admin() ) {
			$shortcode_array['admin'] = true;
		} elseif ( false === $this->admin ) {
			$shortcode_array['admin'] = false;
		}

		if ( ! $this->is_admin() && $this->is_reception_enabled() ) {
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

		return $this->render_shortcode( $shortcode_array, $text_safe );
	}

	/**
	 * Output the shortcode
	 *
	 * @param array $params    A list of params.
	 * @param bool  $no_render Prevents rendering of the shortcode and just returns the shortcode string.
	 *
	 * @return string
	 */
	private function render_shortcode( array $params, bool $no_render = false ): string {
		$output = AppShortcode::SHORTCODE_TAG;

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

		$output = '[' . $output . ']';

		// Allow just the return of the shortcode text rather than execution.
		if ( $no_render ) {
			return $output;
		}

		return do_shortcode( $output );
	}
}
