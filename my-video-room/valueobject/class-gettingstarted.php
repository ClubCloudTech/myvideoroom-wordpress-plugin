<?php
/**
 * Represents a shortcode
 *
 * @package MyVideoRoomPlugin\Shortcode
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\ValueObject;

use MyVideoRoomPlugin\Admin\PageList;
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
				\esc_html__( 'Activate The Plugin' ),
				\sprintf(
				/* translators: %s is the text "MyVideoRoom by ClubCloud" and links to the MyVideoRoom Website */
					\esc_html__(
						'Visit %s to Get Your License Key. Then enter your key below to activate your subscription',
						'myvideoroom'
					),
					'<a href="https://clubcloud.tech/pricing/">' .
					\esc_html__( 'ClubCloud.tech', 'myvideoroom' ) . '</a>'
					
				),
			),

			new Step(
				\esc_html__( 'Activate Your Modules' ),
				\sprintf(
				/* translators: %s is the text "templates" and links to the Template Section */
					\esc_html__( 'Activate the %s That you would like to use in MyVideoRoom', 'myvideoroom' ),
					'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_MODULES, false ) ) . '">' .
					\esc_html__( 'Modules', 'myvideoroom' ) .
					'</a>'
				),
			),

			new Step(
				\esc_html__( 'Access Your Video Rooms' ),
				\sprintf(
				/* translators: %s is the text "Room Permissions" and links to the Permissions Section */
					\esc_html__( 'Start Accessing or adding your active %s.', 'myvideoroom' ),
					'<a href="' . \esc_url( \menu_page_url( PageList::PAGE_SLUG_ROOM_MANAGER, false ) ) . '">' .
					\esc_html__( 'Video Rooms', 'myvideoroom' ) . '</a>'
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
