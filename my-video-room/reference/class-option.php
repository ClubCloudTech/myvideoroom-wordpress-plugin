<?php
/**
 * A single option/param that can be passed to a shortcode
 *
 * @package MyVideoRoomPlugin\Reference
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Reference;

/**
 * Class Option
 */
class Option {
	/**
	 * The shortcode param
	 *
	 * @var string
	 */
	private string $param;

	/**
	 * The description of the param.
	 * Each element in the array will be outputted as a paragraph
	 *
	 * @var array
	 */
	private array $description;

	/**
	 * The default value of the param
	 *
	 * @var string
	 */
	private string $default;

	/**
	 * Option constructor.
	 *
	 * @param string $param       The shortcode param.
	 * @param array  $description An array containing description of the param.
	 * @param string $default     The default value of the param.
	 */
	public function __construct( string $param, array $description, string $default ) {
		$this->param       = $param;
		$this->description = $description;
		$this->default     = $default;
	}

	/**
	 * Get the shortcode param
	 *
	 * @return string
	 */
	public function get_param(): string {
		return $this->param;
	}

	/**
	 * Get the shortcode description
	 *
	 * @return array
	 */
	public function get_description(): array {
		return $this->description;
	}

	/**
	 * Get the default value of the param
	 *
	 * @return string
	 */
	public function get_default(): string {
		return $this->default;
	}

}
