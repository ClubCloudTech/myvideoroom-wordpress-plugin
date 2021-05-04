<?php
/**
 * Manages the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

/**
 * Class Admin
 */
class Admin extends Shortcode {

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
					'myvideoroom-tabbed-css',
					plugins_url( '/css/tabbed.css', __FILE__ ),
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
				esc_html__( 'My Video Room', 'myvideoroom' ),
				esc_html__( 'My Video Room', 'myvideoroom' ),
				'manage_options',
				'my-video-room-global',
				array( $this, 'create_admin_page' ),
				'dashicons-format-chat'
			);

			$this->add_submenu_link(
				esc_html__( 'My Video Room Settings', 'myvideoroom' ),
				'my-video-room-global',
				array( $this, 'create_admin_page' )
			);

			$this->add_submenu_link(
				esc_html__( 'Room Builder', 'myvideoroom' ),
				'my-video-room-roombuilder',
				array( $this, 'create_room_builder_page' )
			);

			$this->add_submenu_link(
				esc_html__( 'Room Templates', 'myvideoroom' ),
				'my-video-room-templates',
				array( $this, 'create_template_page' )
			);

			$this->add_submenu_link(
				esc_html__( 'Shortcode Reference', 'myvideoroom' ),
				'my-video-room',
				array( $this, 'create_video_admin_page' )
			);

			$this->add_submenu_link(
				esc_html__( 'Video Security', 'myvideoroom' ),
				'my-video-room-security',
				array( $this, 'create_settings_admin_page' )
			);

			$this->add_submenu_link(
				esc_html__( 'Help/Getting Started', 'myvideoroom' ),
				'my-video-room-getting-started',
				array( $this, 'create_helpgs_page' )
			);
		}

		do_action( 'myvideoroom_admin_menu', 'my-video-room-global' );
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
			'my-video-room-global',
			$title,
			$title,
			'manage_options',
			$slug,
			$callback
		);
	}

	/**
	 * Render the admin header
	 *
	 * @param string $page     The contents of the page.
	 * @param array  $messages List of message to display in the header.
	 */
	private function render_admin_page( string $page, array $messages = array() ) {
		$activation_key = get_option( Plugin::SETTING_ACTIVATION_KEY );

		if ( $activation_key ) {
			$messages = array_merge( $messages, $this->activate( $activation_key ) );
		} elseif (
			get_option( Plugin::SETTING_PRIVATE_KEY ) &&
			get_option( Plugin::SETTING_ACCESS_TOKEN )
		) {
			$messages = array_merge( $messages, $this->validate() );
		} else {
			$messages[] = array(
				'type'    => 'notice-warning',
				'message' => esc_html__( 'My Video Room is not currently activated. Please enter your activation key to get started.', 'myvideoroom' ),
			);
		}

		echo '<div class="myvideoroom-admin">';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
		echo ( require __DIR__ . '/views/admin-header.php' )( $messages );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
		echo $page;

		echo '</div>';
	}

	/**
	 * Create the admin main page contents.
	 */
	public function create_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		delete_option( Plugin::SETTING_ACTIVATION_KEY );

		if ( esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) ) ) {
			$video_server = esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) );
		} else {
			$video_server = 'clubcloud.tech';
		}

		$available_myvideoroom_plugins = $this->get_available_myvideoroom_plugins();
		$installed_myvideoroom_plugins = $this->installed_myvideoroom_plugin();
		$active_myvideoroom_plugins    = $this->active_myvideoroom_plugin();
		$view                          = require __DIR__ . '/views/admin.php';

		$this->render_admin_page(
			$view( $video_server, $available_myvideoroom_plugins, $installed_myvideoroom_plugins, $active_myvideoroom_plugins )
		);
	}

	/**
	 * Create the Shortcode Reference page contents.
	 */
	public function create_video_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->render_admin_page( ( require __DIR__ . '/views/admin-reference.php' )() );
	}

	/**
	 * Create the settings page for the video
	 */
	public function create_settings_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

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

		$this->render_admin_page( ( require __DIR__ . '/views/admin-settings.php' )( $all_roles ), $messages );
	}

	/**
	 * Create_room_builder_page and send it to Visualiser Control Panel.
	 *
	 * @return void - sends admin page.
	 */
	public function create_room_builder_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->render_admin_page( ( require __DIR__ . '/views/visualiser/view-roombuilder.php' )() );
	}

	/**
	 * Creates Help/Getting Started Page
	 *
	 * @return void - sends Help page.
	 */
	public function create_helpgs_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->render_admin_page( ( require __DIR__ . '/views/admin-getting-started.php' )() );
	}

	/**
	 * Create Template Reference Page
	 *
	 * @return void - sends Template page.
	 */
	public function create_template_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->render_admin_page( ( require __DIR__ . '/views/admin-template-browser.php' )() );
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
					esc_html__( 'My Video Room has been activated. Your current licence allows for %1$s and %2$s', 'myvideoroom' ),
					$concurrent_strings['maxConcurrentUsers'],
					$concurrent_strings['maxConcurrentRooms'],
				),
			);

		} else {
			$messages[] = array(
				'type'    => 'notice-error',
				'message' => esc_html__( 'Failed to activate the My Video Room licence, please check your activation key and try again.', 'myvideoroom' ),
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
					'message' => esc_html__( 'My Video Room is currently unlicensed.', 'myvideoroom' ),
				);
			} else {

				$concurrent_strings = $this->get_concurrent_strings( $json['maxConcurrentUsers'], $json['maxConcurrentRooms'] );

				$messages[] = array(
					'type'    => 'notice-success',
					'message' => sprintf(
					/* translators: First %s is text representing allowed number of users, second %s refers to the allowed number of rooms */
						esc_html__( 'My Video Room is currently active. Your current licence allows for %1$s and %2$s', 'myvideoroom' ),
						$concurrent_strings['maxConcurrentUsers'],
						$concurrent_strings['maxConcurrentRooms'],
					),
				);
			}
		} else {
			$messages[] = array(
				'type'    => 'notice-error',
				'message' => esc_html__( 'Failed to validate your My Video Room licence, please check try reloading this page, if this message remains please re-activate your subscription.', 'myvideroom' ),
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

	/**
	 * Get all installed MyVideoRoom plugins
	 *
	 * @return array
	 */
	private function installed_myvideoroom_plugin(): array {
		return array_filter(
			array_map(
				fn ( string $path) => preg_replace( '/(-[0-9]+|)\/.*$/', '', $path ),
				array_keys( get_plugins() )
			),
			fn( $id) => strpos( $id, 'my-video-room' ) === 0 || strpos( $id, 'myvideoroom' ) === 0
		);
	}

	/**
	 * Get all active MyVideoRoom plugins
	 *
	 * @return array
	 */
	private function active_myvideoroom_plugin(): array {
		return array_filter(
			array_map(
				fn ( string $path) => preg_replace( '/(-[0-9]+|)\/.*$/', '', $path ),
				get_option( 'active_plugins' ),
			),
			fn( $id) => strpos( $id, 'my-video-room' ) === 0 || strpos( $id, 'myvideoroom' ) === 0
		);
	}

	/**
	 * Get all available My Video Room plugins
	 *
	 * @return array[]
	 */
	private function get_available_myvideoroom_plugins(): array {
		return array(
			'my-video-room'        => array(
				'name'    => esc_html__( 'My Video Room', 'myvideoroom' ),
				'visible' => true,
			),
			'my-video-room-extras' => array(
				'name'    => esc_html__( 'My Video Room Extras', 'myvideoroom' ),
				'visible' => true,
			),
		);
	}
}
