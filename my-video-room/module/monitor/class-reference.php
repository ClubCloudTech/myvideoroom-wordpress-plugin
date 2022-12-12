<?php
/**
 * The entry point for the Monitor module
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Monitor;

use MyVideoRoomPlugin\Reference\Option;
use MyVideoRoomPlugin\Reference\Section;
use MyVideoRoomPlugin\Reference\Shortcode;

/**
 * Class Reference
 */
class Reference {

	const MODULE_MONITOR_NAME = 'module-mvrmonitor';
	/**
	 * Get the shortcode reference
	 *
	 * @return Shortcode
	 */
	public function get_shortcode_reference(): Shortcode {
		$shortcode_reference = new Shortcode(
			Module::SHORTCODE_TAG,
			\esc_html__( 'Monitor widget', 'myvideoroom' ),
			\esc_html__( 'This shows the number of people currently waiting in a room', 'myvideoroom' ),
			array(
				'name'        => 'MyVideoRoom',
				'text-empty'  => \esc_html__( 'Nobody is currently waiting', 'myvideoroom' ),
				'text-single' => \esc_html__( 'One person is waiting in reception', 'myvideoroom' ),
				'text-plural' => \esc_html__( '{{count}} people are waiting in reception', 'myvideoroom' ),
			)
		);

		$main_section = new Section();

		$main_section->add_options(
			array(
				new Option(
					'name',
					array( \esc_html__( 'The name of the room', 'myvideoroom' ) ),
					\get_bloginfo( 'name' )
				),

				new Option(
					'text-empty',
					array( \esc_html__( 'The text to show when nobody is waiting', 'myvideoroom' ) ),
					'"' . \esc_html__( 'Nobody is currently waiting', 'myvideoroom' ) . '"',
				),

				new Option(
					'text-empty-plain',
					array(
						\esc_html__( 'The plain text to show when nobody is waiting', 'myvideoroom' ),
						\esc_html__( 'To be used in notifications where `text-empty` contains HTML', 'myvideoroom' ),
					),
					'(text-empty)',
				),

				new Option(
					'text-single',
					array( \esc_html__( 'The text to show when a single person is waiting', 'myvideoroom' ) ),
					'"' . \esc_html__( 'One person is waiting in reception', 'myvideoroom' ) . '"',
				),

				new Option(
					'text-single-plain',
					array(
						\esc_html__( 'The plain text to show a single person is waiting', 'myvideoroom' ),
						\esc_html__( 'To be used in notifications where `text-single` contains HTML', 'myvideoroom' ),
					),
					'(text-single)',
				),

				new Option(
					'text-plural',
					array(
						\esc_html__(
							'The text to show when a more than one person is waiting. "{{count}}" will be substituted with the actual count',
							'myvideoroom'
						),
					),
					'"' . \esc_html__( '{{count}} people are waiting in reception', 'myvideoroom' ) . '"',
				),

				new Option(
					'text-plural-plain',
					array(
						\esc_html__(
							'The text to show when a more than one person is waiting. "{{count}}" will be substituted with the actual count',
							'myvideoroom'
						),
						\esc_html__(
							'To be used in notifications where `text-plural` contains HTML',
							'myvideoroom'
						),
					),
					'(text-plural)',
				),

				new Option(
					'loading-text',
					array( \esc_html__( 'The text to show while the widget is loading', 'myvideoroom' ) ),
					'"' . \esc_html__( 'Loading...', 'myvideoroom' ) . '"',
				),

				new Option(
					'type',
					array(
						\esc_html__( 'The type of count to show:', 'myvideoroom' ),

						'<dl>
							<dt>"reception":</dt>
							<dd>' . \esc_html__( 'The number of people waiting in reception', 'myvideoroom' ) . '</dd>
							
							<dt>"seated":</dt>
							<dd>' . \esc_html__( 'The number of people currently seated', 'myvideoroom' ) . '</dd>
							
							<dt>"all":</dt>
							<dd>' . \esc_html__( 'The total number of people, including reception, seated and non-seated hosts', 'myvideoroom' ) . '</dd>
						</dl>',
					),
					'reception',
				),
			)
		);

		$shortcode_reference->add_section( $main_section );

		return $shortcode_reference;
	}

}
