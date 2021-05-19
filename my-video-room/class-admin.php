<?php
/**
 * Manages the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Admin\Modules;
use MyVideoRoomPlugin\Admin\PageList;
use MyVideoRoomPlugin\Library\Activation;
use MyVideoRoomPlugin\Library\AvailableScenes;
use MyVideoRoomPlugin\Library\Endpoints;
use MyVideoRoomPlugin\Library\HttpGet;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Library\HttpPost;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Reference\Shortcode\App as AppShortcodeReference;
use MyVideoRoomPlugin\Reference\Shortcode\RoomInfo as RoomInfoReference;
use MyVideoRoomPlugin\ValueObject\GettingStarted;
use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Class Admin
 */
class Admin {
	/**
	 * A list of message to show
	 *
	 * @var array
	 */
	private array $notices = array();

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
		if ( \is_admin() && \current_user_can( 'manage_options' ) ) {
			\add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			\add_action( 'myvideoroom_admin_init', array( $this, 'init_admin' ) );
		}
	}

	/**
	 * Init the admin page
	 */
	public function init_admin() {
		$modules_message = Factory::get_instance( Modules::class )->update_active_modules();

		if ( $modules_message ) {
			$this->notices[] = $modules_message;
		}

		$activation_message = Factory::get_instance( Activation::class )->activate();

		if ( $activation_message ) {
			$this->notices[] = $activation_message;
		}

		$this->update_permissions();

		\add_action(
			'admin_enqueue_scripts',
			function () {
				$plugin_version = Factory::get_instance( Version::class )->get_plugin_version();

				\wp_enqueue_style(
					'myvideoroom-admin-css',
					\plugins_url( '/css/admin.css', __FILE__ ),
					false,
					$plugin_version,
				);

				\wp_enqueue_style(
					'myvideoroom-main-css',
					\plugins_url( '/css/shared.css', __FILE__ ),
					false,
					$plugin_version,
				);

				\wp_enqueue_script(
					'myvideoroom-admin-tabs',
					\plugins_url( '/js/tabbed.js', __FILE__ ),
					array( 'jquery' ),
					$plugin_version,
					true
				);
			}
		);

		\add_action(
			'admin_notices',
			function () {
				$notice_renderer = ( require __DIR__ . '/views/admin/admin-notice.php' );

				foreach ( $this->notices as $notice ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not required
					echo $notice_renderer( $notice );
				}
			}
		);
	}

	/**
	 * Update permissions
	 */
	private function update_permissions() {
		$post_library = Factory::get_instance( HttpPost::class );
		if ( $post_library->is_admin_post_request( 'update_permissions' ) ) {
			global $wp_roles;
			$all_roles = $wp_roles->roles;

			foreach ( \array_keys( $all_roles ) as $role_name ) {
				$role = \get_role( $role_name );

				if ( $post_library->get_checkbox_parameter( 'permissions_role_' . $role_name ) ) {
					$role->add_cap( Plugin::CAP_GLOBAL_HOST );
				} else {
					$role->remove_cap( Plugin::CAP_GLOBAL_HOST );
				}
			}

			$this->notices[] = new Notice(
				Notice::TYPE_SUCCESS,
				\esc_html__( 'Roles updated.', 'myvideoroom' ),
			);
		}
	}

	/**
	 * Add the admin menu page.
	 */
	public function add_admin_menu() {
		global $admin_page_hooks;

		if ( empty( $admin_page_hooks[ PageList::PAGE_SLUG_GETTING_STARTED ] ) ) {
			\add_menu_page(
				\esc_html__( 'MyVideoRoom', 'myvideoroom' ),
				\esc_html__( 'MyVideoRoom', 'myvideoroom' ),
				'manage_options',
				PageList::PAGE_SLUG_GETTING_STARTED,
				array( $this, 'create_getting_started_page' ),
				'dashicons-format-chat'
			);

			foreach ( $this->get_navigation_items() as $settings ) {
				$this->add_submenu_link(
					$settings->get_title(),
					$settings->get_slug(),
					function () use ( $settings ) {
						$this->render_admin_page( $settings->render_page() );
					}
				);
			}
		}
	}

	/**
	 * Get the navigation items
	 *
	 * @return \MyVideoRoomPlugin\Admin\Page[]
	 */
	public function get_navigation_items(): array {
		if ( ! $this->navigation_items ) {
			$this->navigation_items = Factory::get_instance( PageList::class )->get_page_list( $this );
		}

		return $this->navigation_items;
	}

	/**
	 * Add a submenu link
	 *
	 * @param string   $title    The title of the page.
	 * @param string   $slug     The slug of the page.
	 * @param callable $callback The callback to render the page.
	 */
	private function add_submenu_link( string $title, string $slug, callable $callback ) {
		\add_submenu_page(
			PageList::PAGE_SLUG_GETTING_STARTED,
			$title,
			$title,
			'manage_options',
			$slug,
			$callback
		);
	}

	// --

	/**
	 * Render an admin page
	 *
	 * @param string $page_contents The page contents.
	 */
	private function render_admin_page( string $page_contents ) {
		$activation_status = Factory::get_instance( Activation::class )->get_activation_status();
		$navigation_items  = $this->get_navigation_items();

		$http_get_library = Factory::get_instance( HttpGet::class );

		$action            = $http_get_library->get_text_parameter( 'action' );
		$current_page_slug = $http_get_library->get_text_parameter( 'page', PageList::PAGE_SLUG_GETTING_STARTED );
		$module_slug       = $http_get_library->get_text_parameter( 'module' );

		$module = null;

		if ( PageList::PAGE_SLUG_MODULES === $current_page_slug && Modules::MODULE_ACTION_DEACTIVATE !== $action ) {

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

	/**
	 * Creates Getting Started Page
	 *
	 * @return string
	 */
	public function create_getting_started_page(): string {
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
			$available_receptions
		);
	}

	/**
	 * Create the Shortcode Reference page contents.
	 *
	 * @return string
	 */
	public function create_shortcode_reference_page(): string {
		$shortcodes = array(
			( new AppShortcodeReference() )->get_shortcode_reference(),
			( new RoomInfoReference() )->get_shortcode_reference(),
		);

		\do_action(
			'myvideoroom_shortcode_reference',
			function ( \MyVideoRoomPlugin\Reference\Shortcode $new_shortcode ) use ( &$shortcodes ) {
				$shortcodes[] = $new_shortcode;
			}
		);

		return ( require __DIR__ . '/views/admin/reference.php' )( $shortcodes );
	}

	/**
	 * Create the settings page for the video
	 *
	 * @return string
	 */
	public function create_permissions_page(): string {
		global $wp_roles;
		$all_roles = $wp_roles->roles;

		return ( require __DIR__ . '/views/admin/permissions.php' )( $all_roles );
	}

	/**
	 * Create the modules page contents.
	 *
	 * @return string
	 */
	public function create_modules_page(): string {

		$http_get_library = Factory::get_instance( HttpGet::class );

		$module_slug = $http_get_library->get_text_parameter( 'module' );
		$action      = $http_get_library->get_text_parameter( 'action' );

		$module = Factory::get_instance( Module::class )->get_module( $module_slug );

		if (
			$module &&
			$module->is_active() &&
			$module->has_admin_page() &&
			Modules::MODULE_ACTION_DEACTIVATE !== $action
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
		$post_library = Factory::get_instance( HttpPost::class );

		if ( $post_library->is_admin_post_request( 'update_advanced_settings' ) ) {
			$reset_settings = $post_library->get_checkbox_parameter( 'delete_activation' );

			if ( $reset_settings ) {
				\delete_option( Plugin::SETTING_ACTIVATION_KEY );
				\delete_option( Plugin::SETTING_ACCESS_TOKEN );
				\delete_option( Plugin::SETTING_PRIVATE_KEY );
			}

			$server_endpoint = $post_library->get_text_parameter( 'server_domain' );
			\update_option( Plugin::SETTING_SERVER_DOMAIN, $server_endpoint );
		}

		$video_server = Factory::get_instance( Endpoints::class )->get_server_endpoint();

		return ( require __DIR__ . '/views/admin/advanced.php' )( $video_server );
	}
}
