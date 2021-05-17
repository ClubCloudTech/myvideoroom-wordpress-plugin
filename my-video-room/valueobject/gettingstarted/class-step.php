<?php
/**
 * Represents a getting started step
 *
 * @package MyVideoRoomPlugin\ValueObject
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\ValueObject\GettingStarted;

/**
 * Class Notice
 */
class Step {

	/**
	 * The title
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * The description
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * Getting started constructor.
	 *
	 * @param string $title       The title.
	 * @param string $description The description.
	 */
	public function __construct( string $title, string $description ) {
		$this->title       = $title;
		$this->description = $description;
	}

	/**
	 * Get the title
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Update the description
	 *
	 * @param string $description The new description.
	 *
	 * @return $this
	 */
	public function set_description( string $description ): self {
		$this->description = $description;

		return $this;
	}

}
