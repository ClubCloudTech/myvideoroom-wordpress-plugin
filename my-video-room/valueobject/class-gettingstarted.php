<?php
/**
 * Represents a shortcode
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\ValueObject;

use MyVideoRoomPlugin\Library\AdminNavigation;
use MyVideoRoomPlugin\ValueObject\GettingStarted\Step;

/**
 * Class GettingStarted
 */
class GettingStarted {

	/**
	 * The list of steps
	 *
	 * @var Step[]
	 */
	private array $steps;

	/**
	 * GettingStarted constructor.
	 */
	public function __construct() {
		$this->steps = array(
			new Step(
				esc_html__( 'Activate the plugin' ),
				sprintf(
				/* translators: %s is the text "MyVideoRoom by ClubCloud" and links to the MyVideoRoom Website */
					esc_html__(
						'Visit %s to Get Your License Key. Then enter your key below to activate your subscription',
						'myvideoroom'
					),
					'<a href="https://clubcloud.tech/pricing/">' .
					esc_html__( 'MyVideoRoom by ClubCloud', 'myvideoroom' ) . '</a>'
				),
			),

			new Step(
				esc_html__( 'Design your room' ),
				sprintf(
				/* translators: %s is the text "templates" and links to the Template Section */
					esc_html__( 'Learn about using %s for video layouts and receptions to find something you like.', 'myvideoroom' ),
					'<a href="' . esc_url( menu_page_url( AdminNavigation::PAGE_SLUG_ROOM_TEMPLATES, false ) ) . '">' .
					esc_html__( 'templates', 'myvideoroom' ) .
					'</a>'
				),
			),

			new Step(
				esc_html__( 'Set permissions' ),
				sprintf(
				/* translators: %s is the text "Room Permissions" and links to the Permissions Section */
					esc_html__( 'Visit the %s page to plan how you want to give access to your rooms.', 'myvideoroom' ),
					'<a href="' . esc_url( menu_page_url( AdminNavigation::PAGE_SLUG_PERMISSIONS, false ) ) . '">' .
					esc_html__( 'room permission', 'myvideoroom' ) . '</a>'
				),
			),
		);

		\do_action(
			'myvideoroom_admin_getting_started_steps',
			$this
		);
	}

	/**
	 * Returns the getting started steps
	 *
	 * @return Step[]
	 */
	public function get_steps(): array {
		return $this->steps;
	}

	/**
	 * Get a step
	 *
	 * @param int $index The index of the step to get.
	 *
	 * @return Step
	 */
	public function get_step( int $index ): Step {
		return $this->steps [ $index - 1 ];
	}
}
