<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomPlugin\Module\StoredRooms
 *
 * @package MyVideoRoomPlugin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\StoredRooms;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action\SubmitButton;

/**
 * Class Module
 */
class Module {

	/**
	 * Module constructor.
	 */
	public function __construct() {
		\add_filter(
			'myvideoroom_roombuilder_create_shortcode',
			array(
				$this,
				'generate_shortcode_constructor',
			),
			0,
			1
		);

		\add_filter( 'myvideoroom_roombuilder_actions_settings', array( $this, 'add_roombuilder_submit' ), 10, 1 );

		\add_filter( 'myvideoroom_appshortcode_output', array( $this, 'modify_shortcode_output' ), 10, 1 );
	}

	/**
	 * Add the submit button to the room builder
	 *
	 * @param \MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action[] $submit_buttons The current submit buttons.
	 *
	 * @return \MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action[]
	 */
	public function add_roombuilder_submit( array $submit_buttons ): array {
		$submit_buttons['storedrooms_persist_room'] = new SubmitButton(
			'storedrooms_persist_room',
			\esc_html__( 'Save room configuration', 'myvideoroom' ),
			'secondary'
		);

		return $submit_buttons;
	}

	/**
	 * Get the correct shortcode constructor
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function generate_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor ): AppShortcodeConstructor {

		$post_library = Factory::get_instance( HttpPost::class );

		if (
			$post_library->is_post_request( 'storedrooms_persist_room' )
		) {
			$_POST['myvideoroom_action_roombuilder_show_preview'] = 'true';
			$_POST['nonce_roombuilder_show_preview']              = wp_create_nonce( 'roombuilder_show_preview' );

			$shortcode_constructor->add_custom_string_param( 'id', '1000' );
		}

		return $shortcode_constructor;
	}

	public function modify_shortcode_output( array $params ): array {
		$filtered = $params;

		if ( $params['id'] ?? null ) {
			$filtered = array( 'id' => $params['id'] );

			if ( ($params['host'] ?? null) === true || ($params['host'] ?? null) === false ) {
				$filtered['host'] = $params['host'];
			}
		}

		return $filtered;
	}
}
