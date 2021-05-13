<?php
/**
 * Manages the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\Post;
use MyVideoRoomPlugin\ValueObject\GettingStarted;
use MyVideoRoomPlugin\Library\AdminNavigation;
use MyVideoRoomPlugin\Library\Activation;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\Endpoints;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Reference\Main\Reference;
use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Class Admin
 */
class Admin {

	const MODULE_ACTION_ACTIVATE   = 'activate';
	const MODULE_ACTION_DEACTIVATE = 'deactivate';

	/**
	 * A list of message to show
	 *
	 * @var array
	 */
	private array $notices = array();

	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * The list of navigation items
	 *
	 * @var array
	 */
	private array $navigation_items = array();

	/**
	 * Initialise the menu item.
	 */
	public function init() {
		$this->update_active_modules();

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		add_action(
			'admin_enqueue_scripts',
			function () {
				$plugin_version = Factory::get_instance( Version::class )->get_plugin_version();

				wp_enqueue_style(
					'myvideoroom-admin-css',
					plugins_url( '/css/admin.css', __FILE__ ),
					false,
					$plugin_version,
				);

				wp_enqueue_style(
					'myvideoroom-main-css',
					plugins_url( '/css/shared.css', __FILE__ ),
					false,
					$plugin_version,
				);

				wp_enqueue_script(
					'myvideoroom-admin-tabs',
					plugins_url( '/js/tabbed.js', __FILE__ ),
					array( 'jquery' ),
					$plugin_version,
					true
				);
			}
		);

		add_action(
			'admin_notices',
			function() {
				$notice_renderer = ( require __DIR__ . '/views/admin/admin-notice.php' );

				foreach ( $this->notices as $notice ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
					echo $notice_renderer( $notice );
				}
			}
		);
	}

	/**
	 * Add the admin menu page.
	 */
	public function add_admin_menu() {
		global $admin_page_hooks;

		if ( empty( $admin_page_hooks[ AdminNavigation::PAGE_SLUG_GETTING_STARTED ] ) ) {
			add_menu_page(
				esc_html__( 'MyVideoRoom', 'myvideoroom' ),
				esc_html__( 'MyVideoRoom', 'myvideoroom' ),
				'manage_options',
				AdminNavigation::PAGE_SLUG_GETTING_STARTED,
				array( $this, 'create_getting_started_page' ),
				'dashicons-format-chat'
			);

			foreach ( $this->get_navigation_items() as $slug => $settings ) {
				$this->add_submenu_link(
					$settings['link'] ?? $settings['title'],
					$slug,
					function () use ( $settings ) {
						$this->render_admin_page( $settings['callback'] );
					}
				);
			}
		}
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
			AdminNavigation::PAGE_SLUG_GETTING_STARTED,
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
	 * @param callable $page_callback The function to render the page.
	 */
	private function render_admin_page( callable $page_callback ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page_contents = $page_callback();

		$activation_status = Factory::get_instance( Activation::class )->get_activation_status();
		$navigation_items  = $this->get_navigation_items();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$action = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$current_page_slug = sanitize_text_field( wp_unslash( $_GET['page'] ?? AdminNavigation::PAGE_SLUG_GETTING_STARTED ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$module_slug = sanitize_text_field( wp_unslash( $_GET['module'] ?? '' ) );

		$module = null;

		if ( AdminNavigation::PAGE_SLUG_MODULES === $current_page_slug && self::MODULE_ACTION_DEACTIVATE !== $action ) {

			$module = Factory::get_instance( Module::class )->get_module( $module_slug );

			if (
				! $module ||
				! $module->is_active() ||
				! $module->has_admin_page()
			) {
				$module = null;
			}
		}

		$header = ( require __DIR__ . '/views/admin/header.php' )(
			$navigation_items,
			$activation_status,
			$current_page_slug,
			$module
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
		echo "<div class=\"wrap myvideoroom-admin\">${header}<main>{$page_contents}</main></div>";
	}

	// --

	/**
	 * Creates Getting Started Page
	 *
	 * @return string
	 */
	public function create_getting_started_page(): string {
		$activation_message = Factory::get_instance( Activation::class )->activate();

		if ( $activation_message ) {
			$this->notices[] = $activation_message;
		}

		$getting_started_steps = Factory::get_instance( GettingStarted::class );

		return ( require __DIR__ . '/views/admin/getting-started.php' )( $getting_started_steps );
	}


	/**
	 * Create Template Reference Page
	 *
	 * @return string
	 */
	public function create_templates_page(): string {
		$available_layouts    = Factory::get_instance( AvailableScenes::class )->get_available_layouts();
		$available_receptions = Factory::get_instance( AvailableScenes::class )->get_available_receptions();

		return ( require __DIR__ . '/views/admin/template-browser.php' )(
			$available_layouts,
			$available_receptions,
			self::$id_index++
		);
	}

	/**
	 * Create the Shortcode Reference page contents.
	 *
	 * @return string
	 */
	public function create_shortcode_reference_page(): string {
		$shortcodes = array(
			( new Reference() )->get_shortcode_reference(),
		);

		\do_action(
			'myvideoroom_shortcode_reference',
			function ( \MyVideoRoomPlugin\Reference\Shortcode $new_shortcode ) use ( &$shortcodes ) {
				$shortcodes[] = $new_shortcode;
			}
		);

		return ( require __DIR__ . '/views/admin/reference.php' )(
			$shortcodes,
			self::$id_index++
		);
	}


	/**
	 * Create the settings page for the video
	 *
	 * @return string
	 */
	public function create_permissions_page(): string {
		global $wp_roles;
		$all_roles = $wp_roles->roles;

		if ( Factory::get_instance( Post::class )->is_post_request() ) {
			check_admin_referer( 'update_caps', 'myvideoroom_permissions_nonce' );

			foreach ( array_keys( $all_roles ) as $role_name ) {
				$role = get_role( $role_name );

				if ( isset( $_POST[ 'role_' . $role_name ] ) ) {
					$role->add_cap( Plugin::CAP_GLOBAL_HOST );
				} else {
					$role->remove_cap( Plugin::CAP_GLOBAL_HOST );
				}
			}

			$this->notices[] = new Notice(
				Notice::TYPE_SUCCESS,
				esc_html__( 'Roles updated.', 'myvideoroom' ),
			);
		}

		return ( require __DIR__ . '/views/admin/permissions.php' )( $all_roles );
	}

	/**
	 * Update the activate modules
	 */
	public function update_active_modules() {
		$page        = sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) );
		$module_slug = sanitize_text_field( wp_unslash( $_GET['module'] ?? '' ) );
		$action      = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );

		if ( AdminNavigation::PAGE_SLUG_MODULES !== $page || ! $module_slug || ! $action ) {
			return;
		}

		$module = Factory::get_instance( Module::class )->get_module( $module_slug );

		if ( ! $module ) {
			return;
		}

		check_admin_referer( 'module_' . $action );

		switch ( $action ) {
			case self::MODULE_ACTION_ACTIVATE:
				$activation_status = Factory::get_instance( Module::class )->activate_module( $module );

				if ( $activation_status ) {
					$this->notices[] = new Notice(
						Notice::TYPE_SUCCESS,
						esc_html__( 'Module activated', 'myvideoroom' ),
					);
				} else {
					$this->notices[] = new Notice(
						Notice::TYPE_ERROR,
						esc_html__( 'Module activation failed', 'myvideoroom' ),
					);
				}

				break;
			case self::MODULE_ACTION_DEACTIVATE:
				$activation_status = Factory::get_instance( Module::class )->deactivate_module( $module );

				if ( $activation_status ) {
					$this->notices[] = new Notice(
						Notice::TYPE_SUCCESS,
						esc_html__( 'Module deactivated', 'myvideoroom' ),
					);
				} else {
					$this->notices[] = new Notice(
						Notice::TYPE_ERROR,
						esc_html__( 'Module deactivation failed', 'myvideoroom' ),
					);
				}
				break;
		}
	}

	/**
	 * Create the modules page contents.
	 *
	 * @return string
	 */
	public function create_modules_page(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$module_slug = sanitize_text_field( wp_unslash( $_GET['module'] ?? '' ) );
		$module      = Factory::get_instance( Module::class )->get_module( $module_slug );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not required
		$action = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );

		if (
			$module &&
			$module->is_active() &&
			$module->has_admin_page() &&
			self::MODULE_ACTION_DEACTIVATE !== $action
		) {
			return ( require __DIR__ . '/views/admin/module.php' )( $module );
		}

		$modules = Factory::get_instance( Module::class )->get_all_modules();
		return ( require __DIR__ . '/views/admin/modules.php' )( $modules );
	}

	/**
	 * Create the admin main page contents.
	 *
	 * @return string
	 */
	public function create_advanced_settings_page(): string {
		if ( Factory::get_instance( Post::class )->is_post_request() ) {
			check_admin_referer( 'update_settings', 'myvideoroom_custom_settings_nonce' );

			$reset_settings = sanitize_text_field( wp_unslash( $_POST['delete_activation'] ?? '' ) ) === 'on';

			if ( $reset_settings ) {
				delete_option( Plugin::SETTING_ACTIVATION_KEY );
				delete_option( Plugin::SETTING_ACCESS_TOKEN );
				delete_option( Plugin::SETTING_PRIVATE_KEY );
			}

			$server_endpoint = sanitize_text_field( wp_unslash( $_POST['myvideoroom_settings_server_domain'] ?? '' ) );
			update_option( Plugin::SETTING_SERVER_DOMAIN, $server_endpoint );
		}

		$video_server = Factory::get_instance( Endpoints::class )->get_server_endpoint();

		return ( require __DIR__ . '/views/admin/advanced.php' )( $video_server, self::$id_index++ );
	}

	/**
	 * Get the navigation items
	 *
	 * @return array[]
	 */
	public function get_navigation_items(): array {
		if ( ! $this->navigation_items ) {
			$this->navigation_items = Factory::get_instance( AdminNavigation::class )->get_navigation_items( $this );
		}

		return $this->navigation_items;
	}
}
