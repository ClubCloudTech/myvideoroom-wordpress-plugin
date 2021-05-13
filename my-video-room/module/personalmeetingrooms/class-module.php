<?php
/**
 * The entry point for the CustomPermissions module
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms;

use MyVideoRoomPlugin\AppShortcode;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Plugin;

/**
 * Class Module
 */
class Module {

	const SETTING_URL_PARAM = Plugin::PLUGIN_NAMESPACE . '_url_param';
	const SHORTCODE_TAG     = AppShortcode::SHORTCODE_TAG . '_personal_invite';

	/**
	 * MonitorShortcode constructor.
	 */
	public function __construct() {
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );

		add_filter( 'myvideoroom_shortcode_constructor', array( $this, 'modify_shortcode_constructor' ), 0, 2 );

		$roombuilder_is_active = Factory::get_instance( \MyVideoRoomPlugin\Library\Module::class )
										->is_module_active( 'roombuilder' );

		if ( $roombuilder_is_active ) {
			new RoomBuilder();
		}
	}

	/**
	 * Show a configuration page for user to visualise shortcodes
	 *
	 * @param array|string $attributes Attributes passed from the shortcode to this function.
	 *
	 * @return string
	 */
	public function output_shortcode( $attributes = array() ): string {
		global $wp;

		if ( ! $attributes ) {
			$attributes = array();
		}

		$host = \wp_get_current_user();

		if ( 0 === $host->ID ) {
			return '';
		}

		$meeting_hash = Factory::get_instance( MeetingIdGenerator::class )->get_meeting_hash_from_user_id( $host->ID );

		$url_param = get_option( self::SETTING_URL_PARAM );

		if ( ! $url_param ) {
			$url_param = 'invite';
		}

		$base_url = home_url( $wp->request );
		$params   = array( $url_param => $meeting_hash );
		$url      = add_query_arg( $params, $base_url );

		return ( require __DIR__ . '/views/invite.php' )(
			$url
		);
	}

	/**
	 * Is the current user a host, based on the the string passed to the shortcode, and the current users id and groups
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 * @param array                   $attr The shortcode attributes.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function modify_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor, array $attr = array() ): AppShortcodeConstructor {
		$host_attribute = $attr['host'] ?? null;

		if ( is_string( $host_attribute ) && strpos( $host_attribute, 'personalmeetingroom' ) === 0 ) {

			$url_param = get_option( self::SETTING_URL_PARAM );

			if ( ! $url_param ) {
				$url_param = 'invite';
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
			$invite_id = sanitize_text_field( wp_unslash( $_GET[ $url_param ] ?? null ) );

			if ( $invite_id ) {
				$host_id = Factory::get_instance( MeetingIdGenerator::class )->get_user_id_from_meeting_hash( $invite_id );
				$host    = \get_user_by( 'id', $host_id );

				$shortcode_constructor->set_as_guest();
			} else {
				$host = \wp_get_current_user();
				$shortcode_constructor->set_as_host();
			}

			if ( 0 !== $host->ID ) {
				$shortcode_constructor->set_name(
					sprintf(
						/* translators: %s is the name of the host */
						esc_html__(
							'Personal space for %s',
							'myvideoroom'
						),
						$host->display_name
					)
				);
			} else {
				$shortcode_constructor->set_error( esc_html__( 'This room cannot be found', 'myvideoroom' ) );
			}
		}

		return $shortcode_constructor;
	}
}
