<?php
/**
 * The shortcode reference
 *
 * @package MyVideoRoomPlugin/Reference/Main
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\SiteVideo;

use MyVideoRoomPlugin\Reference\Option;
use MyVideoRoomPlugin\Reference\Section;
use MyVideoRoomPlugin\Reference\Shortcode;
use MyVideoRoomPlugin\Module\SiteVideo\RoomInfo as RoomInfoShortcode;

/**
 * Class ShortcodeReference
 */
class ShortcodeReference {

	/**
	 * Get the shortcode reference
	 *
	 * @return Shortcode
	 */
	public function get_shortcode_reference(): Shortcode {
		$shortcode_reference = new Shortcode(
			RoomInfoShortcode::SHORTCODE_TAG,
			\esc_html__( 'Room Info', 'myvideoroom' ),
			\esc_html__( 'Returns a variety of useful Information about a room that you can place in your pages.', 'myvideoroom' ),
			array(
				'room' => 'meet-center',
				'type' => 'title',
			),
			sprintf(
			/* translators: First %s is the parameter "title" and second is the name of the room */
				\esc_html__( 'This will return the "%1$s" of the "%2$s" room as a string.', 'myvideoroom' ),
				'title',
				'meet-center'
			)
		);

		$main_section = new Section();
		$main_section->add_options(
			array(
				new Option(
					'room',
					array(
						\esc_html__( 'One of the following', 'myvideoroom' ),
						'
							<ul>
								<li>meet-center</li>
								<li>bookings-center</li>
								<li>site-video-room</li>
							</ul>
						',
					),
					'(' . \esc_html__( 'none' ) . ')'
				),

				new Option(
					'type',
					array(
						\esc_html__( 'One of the following', 'myvideoroom' ),
						'
							<dl>
								<dt>title</dt>
								<dd>' . \esc_html__( 'Room Name (with spaces)', 'myvideoroom' ) . '</dd>
								
								<dt>slug</dt>
								
								<dd>' . \sprintf(
									/* translators: %s is the site URL */
									esc_html__(
										'The post slug (eg- %s has slug of Jones)',
										'myvideoroom'
									),
									get_site_url() . '/jones'
								)
									. '</dd>
								
								<dt>post_id</dt>
								<dd>' . \esc_html__( 'The WordPress Post ID of a room', 'myvideoroom' ) . '</dd>
							
								<dt>url</dt>
								<dd>' . \esc_html__( 'The full URL of the room', 'myvideoroom' ) . '</dd>
							</dl>
						',
					),
					'(' . \esc_html__( 'none' ) . ')'
				),
			)
		);
		$shortcode_reference->add_section( $main_section );

		return $shortcode_reference;
	}

}
