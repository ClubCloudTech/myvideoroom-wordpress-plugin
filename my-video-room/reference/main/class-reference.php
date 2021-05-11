<?php
/**
 * The shortcode reference
 *
 * @package MyVideoRoomPlugin/Reference/Main
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Reference\Main;

use MyVideoRoomPlugin\AppShortcode;
use MyVideoRoomPlugin\Library\Endpoints;
use MyVideoRoomPlugin\Factory;
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
		$rooms_endpoint = Factory::get_instance( Endpoints::class )->get_rooms_endpoint();

		$layouts_html_endpoint = $rooms_endpoint . '/views/layout';
		$layouts_json_endpoint = $rooms_endpoint . '/layout';

		$receptions_html_endpoint = $rooms_endpoint . '/views/receptions';
		$receptions_json_endpoint = $rooms_endpoint . '/receptions';

		$shortcode_reference = new Shortcode(
			AppShortcode::SHORTCODE_TAG,
			__( 'Video app widget', 'myvideoroom' ),
			__( 'This shortcode will shows the video app on a post or page.', 'myvideoroom' ),
			array(
				'name'      => 'The Meeting Room',
				'reception' => true,
			),
			__(
				'This will show the video with a room name of "The Meeting Room". The reception will be enabled for guests.',
				'myvideoroom'
			),
		);

		// --
		// main section

		$main_section = new Section( 'Main settings' );
		$main_section->add_options(
			array(
				new Option(
					'name',
					array(
						esc_html__( 'The name of the room', 'myvideoroom' ),
						esc_html__(
							'All shortcodes on the same domain that share a room name will put users into the same video group. This allows you to have different entry points for hosts and guests.',
							'myvideoroom'
						),
						esc_html__( 'The room name will be visible to users inside the video.', 'myvideoroom' ),
					),
					get_bloginfo( 'name' )
				),

				new Option(
					'layout',
					array(
						esc_html__( 'The id of the layout to display', 'myvideoroom' ),
						sprintf(
						/* translators: %s is a link to the available layouts */
							esc_html__( 'A list of available layouts are available here: %s', 'myvideoroom' ),
							'<a href="' . $layouts_html_endpoint . '">' . $layouts_html_endpoint . '</a>'
						),
						sprintf(
						/* translators: %s is a link to the available layouts in JSON format */
							esc_html__( 'The layout list is also available in a JSON format: %s', 'myvideoroom' ),
							'<a href="' . $layouts_json_endpoint . '">' . $layouts_json_endpoint . '</a>'
						),
					),
					'"boardroom"'
				),

				new Option(
					'host',
					array(
						esc_html__( 'Whether the user should be a host of the room', 'myvideoroom' ),
						esc_html__(
							'Hosts have the ability to add users to rooms, and move users between rooms.',
							'myvideoroom'
						),
						esc_html__( 'You need at least one host to start a video session..', 'myvideoroom' ),
					),
					'false'
				),

				new Option(
					'user-name',
					array(
						esc_html__(
							'Allows override of the displayed user\'s name in the video participant list.',
							'myvideoroom'
						),
					),
					__(
						'(For logged in users will display their WordPress "Display Name". For guests will prompt for a name.)',
						'myvideoroom'
					),
				),

				new Option(
					'loading-text',
					array( esc_html__( 'Text to show while the app is loading', 'myvideoroom' ) ),
					__( '"Loading..."', 'myvideoroom' ),
				),
			)
		);
		$shortcode_reference->add_section( $main_section );

		// --
		// host section

		$host_section = new Section( 'Host settings' );
		$host_section->add_option(
			new Option(
				'lobby',
				array(
					esc_html__(
						'Whether the lobby inside the video app should be enabled for guests',
						'myvideoroom'
					),
				),
				'false'
			),
		);
		$shortcode_reference->add_section( $host_section );

		// --
		// guest section

		$host_section = new Section( 'Guest settings' );
		$host_section->add_options(
			array(
				new Option(
					'floorplan',
					array(
						esc_html__( 'Whether the floorplan should be shown for guests', 'myvideoroom' ),
					),
					'false'
				),

				new Option(
					'reception',
					array(
						esc_html__(
							'Whether the reception before entering the app should be enabled',
							'myvideoroom'
						),
					),
					'false'
				),

				new Option(
					'reception',
					array(
						esc_html__( 'The id of the reception image to use', 'myvideoroom' ),
						sprintf(
						/* translators: %s is a link to the available layouts */
							esc_html__( 'A list of available receptions are available here: %s', 'myvideoroom' ),
							'<a href="' . $receptions_html_endpoint . '">' . $receptions_html_endpoint . '</a>'
						),
						sprintf(
						/* translators: %s is a link to the available layouts in JSON format */
							esc_html__( 'The reception list is also available in a JSON format: %s', 'myvideoroom' ),
							'<a href="' . $receptions_json_endpoint . '">' . $receptions_json_endpoint . '</a>'
						),
					),
					'"boardroom"'
				),

				new Option(
					'reception-video',
					array(
						esc_html__(
							'A link to a video to play in the reception. Will only work if the selected reception supports video',
							'myvideoroom'
						),
					),
					'(Use reception setting)'
				),
			)
		);
		$shortcode_reference->add_section( $host_section );

		return $shortcode_reference;
	}

}
