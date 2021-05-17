<?php
/**
 * A page in the MyVideoRoom admin section
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Admin;

/**
 * Class NavigationItem
 */
class Page {

	/**
	 * The unique slug for the page
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * The title for the page
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * The callback to render the page
	 *
	 * @var callable
	 */
	private $callback;

	/**
	 * An optional dashicon for the page
	 *
	 * @var ?string
	 */
	private ?string $icon;

	/**
	 * NavigationItem constructor.
	 *
	 * @param string   $slug     The unique slug for the page.
	 * @param string   $title    The title for the page.
	 * @param callable $callback The callback to render the page.
	 * @param ?string  $icon     An optional dashicon for the page.
	 */
	public function __construct( string $slug, string $title, callable $callback, string $icon = null ) {
		$this->slug     = $slug;
		$this->title    = $title;
		$this->callback = $callback;
		$this->icon     = $icon;
	}

	/**
	 * Get the unique slug for the page.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Get the title for the page.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Get the optional icon to show in the top nav instead of the title.
	 *
	 * @return ?string
	 */
	public function get_icon(): ?string {
		return $this->icon;
	}


	/**
	 * Render the admin page
	 *
	 * @return string
	 */
	public function render_page(): string {
		return ( $this->callback )();
	}


}
