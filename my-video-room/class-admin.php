<?php
/**
 * Manages the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\Modules;
use MyVideoRoomPlugin\Visualiser\ShortcodeRoomVisualiser;

/**
 * Class Admin
 */
class Admin extends Shortcode {

	const MODULE_ACTION_ACTIVATE   = 'activate';
	const MODULE_ACTION_DEACTIVATE = 'deactivate';

	/**
	 * Initialise the menu item.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_style(
					'myvideoroom-admin-css',
					plugins_url( '/css/admin.css', __FILE__ ),
					false,
					$this->get_plugin_version(),
				);

				wp_enqueue_style(
					'myvideoroom-main-css',
					plugins_url( '/css/shared.css', __FILE__ ),
					false,
					$this->get_plugin_version(),
				);

				wp_enqueue_script(
					'myvideoroom-admin-tabs',
					plugins_url( '/js/tabbed.js', __FILE__ ),
					array( 'jquery' ),
					$this->get_plugin_version(),
					true
				);
			}
		);
	}

	/**
	 * Add the admin menu page.
	 */
	public function add_admin_menu() {
		global $admin_page_hooks;

		if ( empty( $admin_page_hooks['my-video-room-global'] ) ) {
			add_menu_page(
				esc_html__( 'MyVideoRoom', 'myvideoroom' ),
				esc_html__( 'MyVideoRoom', 'myvideoroom' ),
				'manage_options',
				'my-video-room',
				array( $this, 'create_getting_started_page' ),
				'dashicons-format-chat'
			);

			foreach ( $this->get_menu_pages() as $slug => $settings ) {
				$this->add_submenu_link(
					$settings['link'] ?? $settings['title'],
					$slug,
					function () use ( $settings ) {
						$page = $settings['callback']();
						$this->render_admin_page( $page[0], $page[1] ?? array() );
					}
				);
			}
		}
	}

	/**
	 * Get all the menu pages
	 *
	 * @return array[]
	 */
	private function get_menu_pages(): array {
		$default = array(
			'my-video-room'                     => array(
				'title'    => esc_html__( 'Getting Started', 'myvideoroom' ),
				'callback' => array( $this, 'create_getting_started_page' ),
			),

			'my-video-room-builder'             => array(
				'title'    => esc_html__( 'Room Builder', 'myvideoroom' ),
				'callback' => array( $this, 'create_room_builder_page' ),
			),

			'my-video-room-templates'           => array(
				'title'    => esc_html__( 'Room Templates', 'myvideoroom' ),
				'callback' => array( $this, 'create_templates_page' ),
			),

			'my-video-room-shortcode-reference' => array(
				'title'    => esc_html__( 'Shortcode Reference', 'myvideoroom' ),
				'callback' => array( $this, 'create_shortcode_reference_page' ),
			),

			'my-video-room-permissions'         => array(
				'title'    => esc_html__( 'Room Permissions', 'myvideoroom' ),
				'callback' => array( $this, 'create_permissions_page' ),
			),

			'my-video-room-modules'             => array(
				'title'    => esc_html__( 'Modules', 'myvideoroom' ),
				'callback' => array( $this, 'create_modules_page' ),
			),

			'my-video-room-advanced'            => array(
				'title'      => esc_html__( 'Advanced', 'myvideoroom' ),
				'title_icon' => 'admin-generic',
				'callback'   => array( $this, 'create_advanced_settings_page' ),
			),

		);

		return \apply_filters( 'myvideoroom_admin_pages', $default );
	}

	/**
	 * Add a submenu link
	 *
	 * @param string   $title    The title of the page.
	 * @param string   $slug     The slug of the page.
	 * @param callable $callback The callback to render the page.
	 */
	private function add_submenu_link( string $title, string $slug, callable $callback ) {
		add_submenu_page(
			'my-video-room',
			$title,
			$title,
			'manage_options',
			$slug,
			$callback
		);
	}

	/**
	 * Render an admin page
	 *
	 * @param string $page The page contents.
	 * @param array  $messages A list of messages.
	 */
	private function render_admin_page( string $page, $messages = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$activation_key = get_option( Plugin::SETTING_ACTIVATION_KEY );

		if ( $activation_key ) {
			$messages = array_merge( $this->activate( $activation_key ), $messages );
		} elseif (
			get_option( Plugin::SETTING_PRIVATE_KEY ) &&
			get_option( Plugin::SETTING_ACCESS_TOKEN )
		) {
			$messages = array_merge( $this->validate(), $messages );
		} else {
			$messages = array_merge(
				array(
					'type'    => 'notice-warning',
					'message' => esc_html__( 'MyVideoRoom is not currently activated. Please enter your activation key to get started.', 'myvideoroom' ),
				),
				$messages
			);
		}

		echo '<div class="myvideoroom-admin">';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
		echo ( require __DIR__ . '/views/admin/header.php' )( $this->get_menu_pages(), $messages );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
		echo $page;

		echo '</div>';
	}

	// --

	/**
	 * Creates Getting Started Page
	 *
	 * @return array
	 */
	public function create_getting_started_page(): array {
		return array( ( require __DIR__ . '/views/admin/getting-started.php' )() );
	}

	/**
	 * Create_room_builder_page and send it to Visualiser Control Panel.
	 *
	 * @return array
	 */
	public function create_room_builder_page(): array {
		// we only enqueue the scripts if the shortcode is called to prevent it being added to all admin pages.
		do_action( 'myvideoroom_enqueue_scripts' );

		return array( Factory::get_instance( ShortcodeRoomVisualiser::class )->output_shortcode() );
	}

	/**
	 * Create Template Reference Page
	 *
	 * @return array
	 */
	public function create_templates_page(): array {
		$available_layouts    = Factory::get_instance( AvailableScenes::class )->get_available_layouts();
		$available_receptions = Factory::get_instance( AvailableScenes::class )->get_available_receptions();

		return array( ( require __DIR__ . '/views/admin/template-browser.php' )( $available_layouts, $available_receptions ) );
	}

	/**
	 * Create the Shortcode Reference page contents.
	 *
	 * @return array
	 */
	public function create_shortcode_reference_page(): array {
		return array( ( require __DIR__ . '/views/admin/reference.php' )() );
	}


	/**
	 * Create the settings page for the video
	 *
	 * @return array
	 */
	public function create_permissions_page(): array {
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$messages  = array();

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'update_caps', 'nonce' );

			foreach ( $all_roles as $key => $single_role ) {
				$role_object = get_role( $key );

				if ( isset( $_POST[ 'role_' . $key ] ) ) {
					$role_object->add_cap( Plugin::CAP_GLOBAL_ADMIN );
				} else {
					$role_object->remove_cap( Plugin::CAP_GLOBAL_ADMIN );
				}
			}

			$messages[] = array(
				'type'    => 'notice-success',
				'message' => esc_html__( 'Roles updated.', 'myvideoroom' ),
			);
		}

		return array( ( require __DIR__ . '/views/admin/permissions.php' )( $all_roles ), $messages );
	}

	/**
	 * Create the modules page contents.
	 *
	 * @return array
	 */
	public function create_modules_page(): array {
		$modules           = Factory::get_instance( Modules::class )->get_modules();
		$activated_modules = explode( ',', get_option( Plugin::SETTING_ACTIVATED_MODULES ) ?? '' );

		$messages = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$module = sanitize_text_field( wp_unslash( $_GET['module'] ?? '' ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$action = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );
		$nonce  = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) );

		if ( $action && ! wp_verify_nonce( $nonce, 'module_' . $action ) ) {
			$messages[] = array(
				'type'    => 'notice-error',
				'message' => esc_html__(
					'Something went wrong, please reload the page then try again',
					'myvideoroom'
				),
			);

			return array(
				( require __DIR__ . '/views/admin/modules.php' )( $modules, $activated_modules ),
				$messages,
			);
		}

		if ( $module ) {
			switch ( $action ) {
				case self::MODULE_ACTION_ACTIVATE:
					if ( ! in_array( $module, $activated_modules, true ) ) {
						$activated_modules[] = $module;
					}
					update_option( Plugin::SETTING_ACTIVATED_MODULES, implode( ',', $activated_modules ) );

					$messages[] = array(
						'type'    => 'notice-success',
						'message' => esc_html__( 'Module activated', 'myvideoroom' ),
					);

					return array(
						( require __DIR__ . '/views/admin/module.php' )( $modules[ $module ] ),
						$messages,
					);
				case self::MODULE_ACTION_DEACTIVATE:
					if ( in_array( $module, $activated_modules, true ) ) {
						$activated_modules = array_diff( $activated_modules, array( $module ) );
					}
					update_option( Plugin::SETTING_ACTIVATED_MODULES, implode( ',', $activated_modules ) );

					$messages[] = array(
						'type'    => 'notice-success',
						'message' => esc_html__( 'Module deactivated', 'myvideoroom' ),
					);

					break;
				default:
					return array(
						( require __DIR__ . '/views/admin/module.php' )( $modules[ $module ] ),
						$messages,
					);
			}
		}

		return array(
			( require __DIR__ . '/views/admin/modules.php' )( $modules, $activated_modules ),
			$messages,
		);
	}

	/**
	 * Create the admin main page contents.
	 *
	 * @return array
	 */
	public function create_advanced_settings_page(): array {
		delete_option( Plugin::SETTING_ACTIVATION_KEY );

		$messages = array();

		$video_server = Factory::get_instance( Endpoints::class )->get_server_endpoint();

		return array(
			( require __DIR__ . '/views/admin/advanced.php' )( $video_server ),
			$messages,
		);
	}

	/**
	 * Attempt to activate using an activation key
	 *
	 * @param string $activation_key The activation key.
	 *
	 * @return array
	 */
	private function activate( string $activation_key ): array {
		$host = $this->get_host();

		$endpoints = new Endpoints();
		$url       = $endpoints->get_licence_endpoint();

		$opts = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $activation_key,
				'content-type'  => 'application/json',
			),
			'body'    => wp_json_encode(
				array(
					'host' => $host,
				)
			),
		);

		$licence_data = wp_remote_post( $url, $opts );

		$json    = null;
		$licence = null;

		if ( $licence_data ) {
			$licence = wp_remote_retrieve_body( $licence_data );
		}

		if ( $licence ) {
			$json = json_decode( $licence, true );
		}

		if ( ( $json['privateKey'] ?? false ) && ( $json['accessToken'] ?? false ) ) {
			update_option( Plugin::SETTING_PRIVATE_KEY, $json['privateKey'] );
			update_option( Plugin::SETTING_ACCESS_TOKEN, $json['accessToken'] );

			$concurrent_strings = $this->get_concurrent_strings( $json['maxConcurrentUsers'], $json['maxConcurrentRooms'] );

			$messages[] = array(
				'type'    => 'notice-success',
				'message' => sprintf(
				/* translators: First %s is text representing allowed number of users, second %s refers to the allowed number of rooms */
					esc_html__( 'MyVideoRoom has been activated. Your current licence allows for %1$s and %2$s', 'myvideoroom' ),
					$concurrent_strings['maxConcurrentUsers'],
					$concurrent_strings['maxConcurrentRooms'],
				),
			);

		} else {
			$messages[] = array(
				'type'    => 'notice-error',
				'message' => esc_html__( 'Failed to activate the MyVideoRoom licence, please check your activation key and try again.', 'myvideoroom' ),
			);
		}

		return $messages;
	}
	/**
	 * Validate that the current host has a licence
	 *
	 * @return array
	 */
	private function validate(): array {
		$host = $this->get_host();

		$access_token = get_option( Plugin::SETTING_ACCESS_TOKEN );

		$endpoints = new Endpoints();
		$url       = $endpoints->get_licence_endpoint() . '/' . $host;

		$opts = array(
			'headers' => array(
				//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'Authorization' => 'Basic ' . base64_encode( $host . ':' . $access_token ),
				'content-type'  => 'application/json',
			),
		);

		$licence_data = wp_remote_get( $url, $opts );

		$json    = null;
		$licence = null;

		if ( $licence_data ) {
			$licence = wp_remote_retrieve_body( $licence_data );
		}

		if ( $licence ) {
			$json = json_decode( $licence, true );
		}

		if (
			$json &&
			array_key_exists( 'maxConcurrentUsers', $json ) &&
			array_key_exists( 'maxConcurrentRooms', $json )
		) {
			if ( 0 === $json['maxConcurrentUsers'] || 0 === $json['maxConcurrentRooms'] ) {
				$messages[] = array(
					'type'    => 'notice-warning',
					'message' => esc_html__( 'MyVideoRoom is currently unlicensed.', 'myvideoroom' ),
				);
			} else {

				$concurrent_strings = $this->get_concurrent_strings( $json['maxConcurrentUsers'], $json['maxConcurrentRooms'] );

				$messages[] = array(
					'type'    => 'notice-success',
					'message' => sprintf(
					/* translators: First %s is text representing allowed number of users, second %s refers to the allowed number of rooms */
						esc_html__( 'MyVideoRoom is currently active. Your current licence allows for %1$s and %2$s', 'myvideoroom' ),
						$concurrent_strings['maxConcurrentUsers'],
						$concurrent_strings['maxConcurrentRooms'],
					),
				);
			}
		} else {
			$messages[] = array(
				'type'    => 'notice-error',
				'message' => esc_html__( 'Failed to validate your MyVideoRoom licence, please check try reloading this page, if this message remains please re-activate your subscription.', 'myvideroom' ),
			);
		}

		return $messages;
	}

	/**
	 * Convert number of users and rooms to strings
	 *
	 * @param int|null $max_concurrent_users The maximum number of concurrent users - or null for unlimited.
	 * @param int|null $max_concurrent_rooms The maximum number of concurrent rooms - or null for unlimited.
	 *
	 * @return string[]
	 */
	private function get_concurrent_strings( int $max_concurrent_users = null, int $max_concurrent_rooms = null ): array {
		if ( $max_concurrent_users ) {
			$max_concurrent_users_text = sprintf(
				esc_html(
				/* translators: %d is an number representing the number allowed current users */
					_n(
						'a maximum of %d concurrent user',
						'a maximum of %d concurrent users',
						$max_concurrent_users,
						'myvideoroom'
					)
				),
				$max_concurrent_users
			);
		} else {
			$max_concurrent_users_text = 'unlimited concurrent users';
		}

		if ( $max_concurrent_rooms ) {
			$max_concurrent_rooms_text = sprintf(
				esc_html(
				/* translators: %d is an number representing the number allowed current rooms */
					_n(
						'a maximum of %d concurrent room',
						'a maximum of %d concurrent rooms',
						$max_concurrent_rooms,
						'myvideoroom'
					)
				),
				$max_concurrent_rooms
			);
		} else {
			$max_concurrent_rooms_text = 'unlimited concurrent rooms';
		}

		return array(
			'maxConcurrentUsers' => $max_concurrent_users_text,
			'maxConcurrentRooms' => $max_concurrent_rooms_text,
		);
	}
}
