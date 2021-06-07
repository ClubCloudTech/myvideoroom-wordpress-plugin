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
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action\HiddenField;
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

		\add_filter( 'myvideoroom_shortcode_constructor', array( $this, 'modify_shortcode_constructor' ), 10, 2 );
	}

	/**
	 * Add the submit button to the room builder
	 *
	 * @param \MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action[] $submit_buttons The current submit buttons.
	 *
	 * @return \MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action[]
	 */
	public function add_roombuilder_submit( array $submit_buttons ): array {
		$get_library  = Factory::get_instance( HttpGet::class );
		$post_library = Factory::get_instance( HttpPost::class );

		if ( $post_library->is_post_request( 'storedrooms_persist_room' ) ) {
			$stored_id = $post_library->get_string_parameter( 'storedrooms_stored_id' );
		} else {
			$stored_id = $get_library->get_string_parameter( 'stored-id', null );
		}

		if ( $stored_id ) {
			$action_text = \esc_html__( 'Update room configuration', 'myvideoroom' );

			$submit_buttons['storedrooms_stored_id'] = new HiddenField(
				'storedrooms_stored_id',
				$stored_id
			);
		} else {
			$action_text = \esc_html__( 'Save room configuration', 'myvideoroom' );
		}

		$submit_buttons['storedrooms_persist_room'] = new SubmitButton(
			'storedrooms_persist_room',
			$action_text,
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
		$get_library  = Factory::get_instance( HttpGet::class );

		$get_stored_id = $get_library->get_string_parameter( 'stored-id', null );

		if (
			$post_library->is_post_request( 'storedrooms_persist_room' )
		) {
			$stored_id = $post_library->get_string_parameter( 'storedrooms_stored_id' );

			$_POST['myvideoroom_action_roombuilder_show_preview'] = 'true';
			$_POST['myvideoroom_nonce_roombuilder_show_preview']  = wp_create_nonce( 'roombuilder_show_preview' );

			$stored_app_shortcode = StoredAppShortcode::create_from_shortcode_constructor( $stored_id, $shortcode_constructor );
			$stored_app_shortcode = Factory::get_instance( Dao::class )->persist( $stored_app_shortcode );

			$shortcode_constructor->add_custom_string_param( 'id', $stored_app_shortcode->get_id() );
			$_POST['myvideoroom_storedrooms_stored_id'] = $stored_app_shortcode->get_id();
		} elseif ( $get_stored_id ) {
			$_SERVER['REQUEST_METHOD']                            = 'POST';
			$_POST['myvideoroom_action_roombuilder_show_preview'] = 'true';
			$_POST['myvideoroom_nonce_roombuilder_show_preview']  = wp_create_nonce( 'roombuilder_show_preview' );

			$shortcode_constructor = Factory::get_instance( Dao::class )->get_by_id( $get_stored_id );
			$shortcode_constructor->add_custom_string_param( 'id', $shortcode_constructor->get_id() );
		}

		return $shortcode_constructor;
	}

	/**
	 * Modify the shortcode output to add the stored rooms id
	 *
	 * @param array $params The list of shortcode key=>values.
	 *
	 * @return array
	 */
	public function modify_shortcode_output( array $params ): array {
		$filtered = $params;

		if ( $params['id'] ?? null ) {
			$filtered = array( 'id' => $params['id'] );

			if ( ( $params['host'] ?? null ) === true || ( $params['host'] ?? null ) === false ) {
				$filtered['host'] = $params['host'];
			}
		}
		return $filtered;
	}

	/**
	 * Is the current user a host, based on the the string passed to the shortcode, and the current users id and groups
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 * @param array                   $params                The parameters passed to the shortcode.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function modify_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor, array $params ): AppShortcodeConstructor {
		if ( $params['id'] ?? null ) {
			$stored_app_shortcode = Factory::get_instance( Dao::class )->get_by_id( $params['id'] );

			if ( $stored_app_shortcode ) {

				if ( ( $params['host'] ?? null ) === 'true' ) {
					$stored_app_shortcode->set_as_host();
				}

				if ( ( $params['host'] ?? null ) === 'false' ) {
					$stored_app_shortcode->set_as_guest();
				}

				return $stored_app_shortcode;
			}
		}

		return $shortcode_constructor;
	}
}
