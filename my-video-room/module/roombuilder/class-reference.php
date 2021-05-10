<?php
/**
 * The entry point for the Room Builder module
 *
 * @package MyVideoRoomPlugin/RoomBuilder/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder;

use MyVideoRoomPlugin\Reference\Option;
use MyVideoRoomPlugin\Reference\Section;
use MyVideoRoomPlugin\Reference\Shortcode;

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
			__( 'Room builder widget', 'myvideoroom' ),
			__(
				'A tool to explore the different options provided by MyVideoRoom, and to generate the correct app shortcode to output the room.',
				'myvideoroom'
			),
			array(
				'initial_preview' => true,
			)
		);

		$main_section = new Section();

		$main_section->add_option(
			new Option(
				'initial_preview',
				array(
					esc_html__(
						'If a preview should be loaded at the start, instead of waiting for for the user to make selections first.',
						'myvideoroom'
					),
				),
				'true'
			)
		);

		$shortcode_reference->add_section( $main_section );

		return $shortcode_reference;
	}

}
