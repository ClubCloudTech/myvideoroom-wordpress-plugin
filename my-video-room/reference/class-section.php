<?php
/**
 * A group of options/params that can be passed to a shortcode
 *
 * @package MyVideoRoomPlugin\Reference
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Reference;

/**
 * Class Section
 */
class Section {
	/**
	 * The name of the section
	 *
	 * @var ?string
	 */
	private ?string $name;

	/**
	 * The list of options in the section
	 *
	 * @var Option[]
	 */
	private array $options = array();

	/**
	 * Option constructor.
	 *
	 * @param ?string $name The name of the section.
	 */
	public function __construct( string $name = null ) {
		$this->name = $name;
	}

	/**
	 * Get the name of the section.
	 *
	 * @return ?string
	 */
	public function get_name(): ?string {
		return $this->name;
	}

	/**
	 * Get the contained options in the section
	 *
	 * @return Option[]
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Add a single option to the section
	 *
	 * @param Option $option The option to add.
	 *
	 * @return $this
	 */
	public function add_option( Option $option ): self {
		$this->options[] = $option;
		return $this;
	}

	/**
	 * Add a list of options to the array
	 *
	 * @param array $options The options to add.
	 *
	 * @return $this
	 */
	public function add_options( array $options ): self {
		foreach ( $options as $option ) {
			$this->add_option( $option );
		}
		return $this;
	}
}
