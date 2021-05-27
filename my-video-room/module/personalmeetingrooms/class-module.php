<?php
/**
 * The entry point for the CustomPermissions module
 *
 * @package MyVideoRoomPlugin/Module/Monitor
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\PersonalMeetingRooms;

use MyVideoRoomPlugin\Admin;
use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\Ajax;
use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Plugin;
use MyVideoRoomPlugin\Shortcode\App;
use WP_Error;

/**
 * Class Module
 */
class Module {

	const SETTING_URL_PARAM = Plugin::PLUGIN_NAMESPACE . '_url_param';
	const SHORTCODE_TAG     = App::SHORTCODE_TAG . '_personal_invite';

	const INVITE_EMAIL_ACTION = 'personalmeetingrooms_invite';

	/**
	 * MonitorShortcode constructor.
	 */
	public function __construct() {
		\add_shortcode( self::SHORTCODE_TAG, array( $this, 'output_shortcode' ) );

		\add_action( 'wp_ajax_myvideroom_personalmeetingrooms_invite', array( $this, 'process_ajax_request' ) );

		\add_filter( 'myvideoroom_shortcode_constructor', array( $this, 'modify_shortcode_constructor' ), 0, 2 );

		\add_action( 'wp_enqueue_scripts', fn() => $this->enqueue_scripts_and_styles() );

		\add_action(
			Admin::ACTION_SHORTCODE_REFERENCE,
			function ( callable $add_reference ) {
				$add_reference( ( new Reference() )->get_shortcode_reference() );
			}
		);

		$roombuilder_is_active = Factory::get_instance( \MyVideoRoomPlugin\Library\Module::class )
			->is_module_active( 'roombuilder' );
		if ( $roombuilder_is_active ) {
			new RoomBuilder();
		}
	}

	/**
	 * Enqueue required scripts and styles
	 */
	private function enqueue_scripts_and_styles() {
		$plugin_version = Factory::get_instance( Version::class )->get_plugin_version();

		\wp_enqueue_style(
			'myvideoroom-personalmeetingrooms-invite-css',
			\plugins_url( '/css/invite.css', \realpath( __FILE__ ) ),
			false,
			$plugin_version,
		);

		\wp_enqueue_script(
			'myvideoroom-personalmeetingrooms-invite-js',
			\plugins_url( '/js/invite.js', \realpath( __FILE__ ) ),
			array( 'jquery' ),
			$plugin_version,
			true
		);

		\wp_localize_script(
			'myvideoroom-personalmeetingrooms-invite-js',
			'myvideroom_personalmeetingrooms_invite',
			array( 'ajax_url' => \admin_url( 'admin-ajax.php' ) )
		);
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

		list( $message, $success ) = $this->process_email_send();

		$meeting_hash = Factory::get_instance( MeetingIdGenerator::class )->get_meeting_hash_from_user_id( $host->ID );

		$url_param = \get_option( self::SETTING_URL_PARAM );

		if ( ! $url_param ) {
			$url_param = 'invite';
		}

		$show_icons         = ( 'true' === ( $attributes['icon'] ?? '' ) );
		$invert_icon_colors = ( 'true' === ( $attributes['invert'] ?? '' ) );

		$base_url = \home_url( $wp->request );
		$params   = array( $url_param => $meeting_hash );
		$url      = \add_query_arg( $params, $base_url );

		return ( require __DIR__ . '/views/invite.php' )(
			$url,
			$show_icons,
			$invert_icon_colors,
			$success,
			$message
		);
	}

	/**
	 * Check if this was a email send request
	 *
	 * @return ?array
	 */
	private function process_email_send(): ?array {
		$post_library = Factory::get_instance( HttpPost::class );
		if (
			$post_library->is_post_request( self::INVITE_EMAIL_ACTION )
		) {
			if ( $post_library->is_nonce_valid( self::INVITE_EMAIL_ACTION ) ) {
				return array(
					false,
					\esc_html__( 'Something went wrong, please reload the page and try again', 'myvideoroom' ),
				);
			} else {
				$email       = $post_library->get_string_parameter( self::INVITE_EMAIL_ACTION . '_address' );
				$invite_link = $post_library->get_string_parameter( self::INVITE_EMAIL_ACTION . '_link' );

				$result = $this->send_invite_email( $email, $invite_link );

				if ( $result ) {
					return array(
						true,
						\esc_html__( 'Email sent successfully.', 'myvideoroom' ),
					);
				} else {
					return array(
						true,
						\esc_html__( 'Email failed to send. Please try again.', 'myvideoroom' ),
					);
				}
			}
		}

		return array(
			null,
			null,
		);
	}

	/**
	 * Send the email - returns true if successful, false if failed to send
	 *
	 * @param string $email_address The email address to send to.
	 * @param string $invite_link   The invite link to send.
	 *
	 * @return bool
	 */
	private function send_invite_email( string $email_address, string $invite_link ): bool {
		$site_name     = \get_bloginfo();
		$email_from    = \wp_get_current_user()->display_name;
		$email_subject = \sprintf(
		/* translators: %s is the name of the person sending the email */
			\esc_html__(
				'%s would like you to join a video meeting.',
				'my-video-room'
			),
			\esc_html( $email_from )
		);

		$message = ( require __DIR__ . '/views/emailmessage.php' )( $invite_link, $site_name, $email_from );
		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";

		\add_action( 'wp_mail_failed', array( $this, 'on_mail_failure' ) );
		$success = \wp_mail( $email_address, $email_subject, $message, $headers );
		\remove_action( 'wp_mail_failed', array( $this, 'on_mail_failure' ) );

		return $success;
	}

	/**
	 * Is the current user a host, based on the the string passed to the shortcode, and the current users id and groups
	 *
	 * @param AppShortcodeConstructor $shortcode_constructor The shortcode constructor.
	 * @param array                   $attr                  The shortcode attributes.
	 *
	 * @return AppShortcodeConstructor
	 */
	public function modify_shortcode_constructor( AppShortcodeConstructor $shortcode_constructor, array $attr = array() ): AppShortcodeConstructor {
		$host_attribute = $attr['host'] ?? null;

		if ( \is_string( $host_attribute ) && \strpos( $host_attribute, 'personalmeetingroom' ) === 0 ) {

			$url_param = \get_option( self::SETTING_URL_PARAM );

			if ( ! $url_param ) {
				$url_param = 'invite';
			}

			$http_get_library = Factory::get_instance( HttpGet::class );
			$invite_id        = $http_get_library->get_string_parameter( $url_param );

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
					\sprintf(
					/* translators: %s is the name of the host */
						\esc_html__(
							'Personal space for %s',
							'myvideoroom'
						),
						$host->display_name
					)
				);
			} else {
				$shortcode_constructor->set_error( \esc_html__( 'This room cannot be found', 'myvideoroom' ) );
			}
		}

		return $shortcode_constructor;
	}

	/**
	 * Process an ajax request
	 */
	public function process_ajax_request() {
		$ajax_library = Factory::get_instance( Ajax::class );

		$nonce       = $ajax_library->get_text_parameter( 'nonce' );
		$email       = $ajax_library->get_text_parameter( 'email' );
		$invite_link = $ajax_library->get_text_parameter( 'link' );

		if ( ! \wp_verify_nonce( $nonce, self::INVITE_EMAIL_ACTION ) ) {
			$status   = 400;
			$response = array(
				'success' => false,
				'message' => \esc_html__( 'Something went wrong, please reload the page and try again', 'myvideoroom' ),
			);
		} else {
			$result = $this->send_invite_email( $email, $invite_link );

			if ( $result ) {
				$status   = 201;
				$response = array(
					'success' => true,
					'message' => \esc_html__( 'Email sent successfully.', 'myvideoroom' ),
				);
			} else {
				$status   = 400;
				$response = array(
					'success' => false,
					'message' => \esc_html__( 'Email failed to send. Please try again.', 'myvideoroom' ),
				);
			}
		}

		\wp_send_json( $response, $status );
	}

	/**
	 * On email failure
	 *
	 * @param WP_Error $wp_error The WordPress error message.
	 */
	public function on_mail_failure( WP_Error $wp_error ) {
		if (
			\defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			\defined( 'WP_DEBUG_LOG' ) &&
			WP_DEBUG_LOG
		) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is only in debug mode
			\error_log( $wp_error->get_error_message() );
		}
	}
}
