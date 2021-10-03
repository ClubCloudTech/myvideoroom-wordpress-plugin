<?php
/**
 * Shortcode reference for PersonalMeetingRooms
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms\Library;

use MyVideoRoomPlugin\Reference\Option;
use MyVideoRoomPlugin\Reference\Section;
use MyVideoRoomPlugin\Reference\Shortcode;
use MyVideoRoomPlugin\Library\ShortcodeDocuments;

/**
 * Class Reference
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
			\esc_html__( 'Personal meeting rooms invite ', 'myvideoroom' ),
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
		// Factory::get_instance( ShortcodeDocuments::class )->render_personalmeeting_shortcode_docs();
		return $shortcode_reference;
	}

}
