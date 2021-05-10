<?php
/**
 * Describes the configuration options for a plugin
 *
 * @package MyVideoRoomPlugin\Reference
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Reference;

/**
 * Class Shortcode
 */
class Shortcode {
	/**
	 * The shortcode tag
	 *
	 * @var string
	 */
	private string $shortcode_tag;

	/**
	 * The name of the shortcode
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * The description of the shortcode
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * The params for the example shortcode
	 *
	 * @var array
	 */
	private array $example_shortcode_params;

	/**
	 * An optional additional description of the shortcode
	 *
	 * @var ?string
	 */
	private ?string $example_description;

	/**
	 * A grouped list of options for the shortcode
	 *
	 * @var Section[]
	 */
	private array $sections = array();

	/**
	 * Option constructor.
	 *
	 * @param string  $shortcode_tag            The shortcode tag.
	 * @param string  $name                     The name of the shortcode.
	 * @param string  $description              The description of the shortcode.
	 * @param array   $example_shortcode_params The params for the example shortcode.
	 * @param ?string $example_description      An optional additional description of the shortcode.
	 */
	public function __construct(
		string $shortcode_tag,
		string $name,
		string $description,
		array $example_shortcode_params,
		?string $example_description = null
	) {
		$this->shortcode_tag            = $shortcode_tag;
		$this->name                     = $name;
		$this->description              = $description;
		$this->example_shortcode_params = $example_shortcode_params;
		$this->example_description      = $example_description;
	}

	/**
	 * Get the shortcode tag
	 *
	 * @return string
	 */
	public function get_shortcode_tag(): string {
		return $this->shortcode_tag;
	}

	/**
	 * Get the name of the shortcode
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the shortcode description
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Get the params for the example shortcode text
	 *
	 * @return array
	 */
	public function get_example_shortcode_params(): array {
		return $this->example_shortcode_params;
	}

	/**
	 * Get the description of the example
	 *
	 * @return string
	 */
	public function get_example_description(): ?string {
		return $this->example_description;
	}


	/**
	 * Add a group of options to the shortcode reference
	 *
	 * @param Section $section The section to add.
	 *
	 * @return Shortcode
	 */
	public function add_section( Section $section ): self {
		$this->sections[] = $section;
		return $this;
	}

	/**
	 * Get the sections in the reference
	 *
	 * @return Section[]
	 */
	public function get_sections(): array {
		return $this->sections;
	}

}
