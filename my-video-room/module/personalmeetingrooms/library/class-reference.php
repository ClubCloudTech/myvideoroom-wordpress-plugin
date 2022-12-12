<?php
/**
 * Shortcode reference for PersonalMeetingRooms
 *
 * @package my-video-room/module/personalmeetingrooms/library/class-reference.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library;

use MyVideoRoomPlugin\Reference\Option;
use MyVideoRoomPlugin\Reference\Section;
use MyVideoRoomPlugin\Reference\Shortcode;

/**
 * Class Reference Documentation for Shortcodes in Personal Meeting Rooms.
 */
class Reference {

	/**
	 * Get the shortcode reference
	 *
	 * @return Shortcode
	 */
	public function get_shortcode_reference(): Shortcode {
		$shortcode_reference = new Shortcode(
			Module::SHORTCODE_TAG,
			\esc_html__( 'Personal Meeting Rooms', 'myvideoroom' ),
			\esc_html__( 'Allows hosts to invite guests to their personal meeting room. This should be added to the same page as the personal meeting room.', 'myvideoroom' )
		);

		$main_section = new Section();

		$main_section->add_options(
			array(
				new Option(
					'icon',
					array( \esc_html__( 'If we should show icons instead of text labels.', 'myvideoroom' ) ),
					'false'
				),

				new Option(
					'invert',
					array( \esc_html__( 'If the color of the icons should be inverted.', 'myvideoroom' ) ),
					'false'
				),

			)
		);

		$shortcode_reference->add_section( $main_section );
		return $shortcode_reference;
	}

}
