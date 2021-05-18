<?php
/**
 * Default settings and params
 *
 * @package MyVideoRoomExtrasPlugin\Core
 */

namespace MyVideoRoomPlugin\Core;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\UserRoles;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Library\MeetingIdGenerator;
use MyVideoRoomPlugin\Shortcode as Shortcode;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Setup\Setup;

/**
 * Class SiteDefaults
 */
class SiteDefaults extends Shortcode {

	// All Up Site Default Master Setting.
	const ROOM_NAME_SITE_DEFAULT = 'site-default-settings';
	const SHORTCODE_PREFIX       = 'mvr_';

	// Default User ID to Use for Room Site Defaults .
	const USER_ID_SITE_DEFAULTS = 443556987;

	// Default Database Table Names.
	const TABLE_NAME_MODULE_CONFIG         = 'myvideoroom_module_config';
	const TABLE_NAME_ROOM_MAP              = 'myvideoroom_room_post_mapping';
	const TABLE_NAME_USER_VIDEO_PREFERENCE = 'myvideoroom_user_video_preference';

	// Module Names and IDs.
	const MODULE_DEFAULT_VIDEO_NAME = 'default-video-module';
	const MODULE_DEFAULT_VIDEO_ID   = 1;
	const MODULE_CORE_PATH          = '/core/views/view-settings-core.php';

	// Listing Security Module ID in Core, so it can be checked for in Core Class to exit gracefully.
	const MODULE_SECURITY_ID = Dependencies::MODULE_SECURITY_ID;

	/**
	 * Initialise On Module Activation
	 * Once off functions for activating Module
	 */
	public function initialise_module() {
		Factory::get_instance( Setup::class )->install_room_post_mapping_table();
		Factory::get_instance( Setup::class )->install_user_video_preference_table();
		Factory::get_instance( Setup::class )->install_module_config_table();
		Factory::get_instance( Setup::class )->initialise_default_video_settings();
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_DEFAULT_VIDEO_NAME, self::MODULE_DEFAULT_VIDEO_ID, true, self::MODULE_CORE_PATH );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_DEFAULT_VIDEO_ID, true );
	}

	/**
	 * Runtime Functions.
	 */
	public function init() {
		$this->register_scripts_styles();
		$this->core_menu_setup();

	}

	/**
	 * Setup of Module Menu
	 */
	public function core_menu_setup() {
		add_action( 'mvr_module_submenu_add', array( self::class, 'core_menu_button' ) );
	}
	/**
	 * Render Module Menu.
	 */
	public function core_menu_button() {
		$name = self::esc_html_e( 'Video Default Settings', 'my-video-room' );
		$slug = self::MODULE_DEFAULT_VIDEO_NAME;
		//phpcs:ignore --WordPress.WP.I18n.NonSingularStringLiteralText - $name is a constant text literal already.
		$display = esc_html__( $name, 'myvideoroom' );
		echo '<a class="mvr-menu-header-item" href="?page=my-video-room-extras&tab=' . esc_html( $slug ) . '">' . esc_html( $display ) . '</a>';
	}

	/**
	 * Registers All Centrally Used Scripts and Styles.
	 *
	 * @return void
	 */
	private function register_scripts_styles() {
		// ToDO Fred to remove before final merge.
		wp_register_script(
			'myvideoroom-protect-input',
			plugins_url( '/../js/protect-input.js', __FILE__ ),
			null,
			$this->get_plugin_version() . gmdate( 'Ymdhms' ),
			true
		);

		wp_register_script(
			'myvideoroom-frame-refresh',
			plugins_url( '/../js/mvr-frame-refresh.js', __FILE__ ),
			null,
			$this->get_plugin_version() . gmdate( 'Ymdhms' ),
			true
		);

		wp_register_script(
			'myvideoroom-admin-tabs',
			plugins_url( '/../js/tabbed.js', __FILE__ ),
			array( 'jquery' ),
			$this->get_plugin_version(),
			true
		);
		wp_register_script(
			'myvideoroom-outer-tabs',
			plugins_url( '/../js/outer-tabbed.js', __FILE__ ),
			array( 'jquery' ),
			$this->get_plugin_version() . gmdate( 'Ymdhms' ),
			true
		);

		wp_register_script(
			'myvideoroom-remove-admin-header',
			plugins_url( '/../js/mvr-remove-admin-header.js', __FILE__ ),
			array( 'jquery' ),
			$this->get_plugin_version() . gmdate( 'Ymdhms' ),
			true,
		);

		wp_register_style( 'mvr-menutab-header', plugins_url( '/../css/mvr-menutab.css', __FILE__ ), false, $this->get_plugin_version() . gmdate( 'Ymdhms' ) );
		wp_register_style( 'mvr-template', plugins_url( '/../css/mvr-template.css', __FILE__ ), false, $this->get_plugin_version() . gmdate( 'Ymdhms' ) );
		wp_register_style( 'mvr-remove-admin-bar', plugins_url( '/../css/mvr-admin-bar.css', __FILE__ ), false, $this->get_plugin_version() . gmdate( 'Ymdhms' ) );
	}

	/**
	 * Is_Elementor - checks if a Elementor is enabled.
	 *
	 * @return bool
	 */
	public function is_elementor_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active( 'elementor/elementor.php' ) ) {

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generates all default Room Names for All Functions that use Video Rooms
	 * Video functions call this function to get default room names to ensure all generate consistently
	 *
	 * @param string $type - type of room to name.
	 * @param mixed  $input_id - optional ID to pass for usage.
	 *
	 * @return string
	 */
	public function room_map( string $type, $input_id = null ) {
		if ( ! $type ) {
			return 'ERR: CC101 - No Room Type Provided';
		}

		switch ( $type ) {
			case 'sitevideo':
				return preg_replace( '/[^A-Za-z0-9\-]/', '', get_bloginfo( 'name' ) ) . '-' . preg_replace( '/[^A-Za-z0-9\-]/', '', $input_id );
			case 'userbr':
				$user_field = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_id( $input_id );

				$displayid    = $user_field->display_name;
				$output       = preg_replace( '/[^A-Za-z0-9\-]/', '', $displayid ); // remove special characters from username.
				$outmeetingid = Factory::get_instance( MeetingIdGenerator::class )->invite( $input_id, 'user', null );

				return 'Space-' . $output . '-' . $outmeetingid;

			case 'group':
				global $bp;

				return 'Group-' . $bp->groups->current_group->slug . '-Space';

			case 'mvr':
				$user       = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_id( (int) $input_id );
				$user_roles = Factory::get_instance( UserRoles::class, array( $user ) );

				// IF staff member - then replace the ID with Owner ID.
				if ( $user && $user_roles->is_wcfm_shop_staff() ) {
					$parent_id = $user->_wcfm_vendor;

					$user_field = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_id( $parent_id );

					$input_id = $parent_id;
				}
				$displayid    = $user_field->display_name;
				$output       = preg_replace( '/[^A-Za-z0-9\-]/', '', $displayid ); // remove special characters from username.
				$outmeetingid = Factory::get_instance( MeetingIdGenerator::class )->invite( $input_id, 'user', null );

				return 'Space-' . $output . '-' . $outmeetingid;
		}
	}

	/**
	 * Splits Name of Meeting Rooms
	 *
	 * @param  string $name - the name to be split.
	 * @return string
	 */
	public function name_split( $name ) {
		if ( ! $name ) {
			return 'ERR: CC102 - No Name Provided';
		}

		$words = preg_split( '/[\s,_-]+/', $name );

		$acronym = '';

		foreach ( $words as $w ) {
			$acronym .= $w[0];
		}

		return $acronym;
	}
}
