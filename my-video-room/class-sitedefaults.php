<?php
/**
 * Default settings and params
 *
 * @package MyVideoRoomExtrasPlugin\Core
 */

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\UserRoles;
use MyVideoRoomPlugin\Library\Version;
use MyVideoRoomPlugin\Library\WordPressUser;
use MyVideoRoomPlugin\Library\MeetingIdGenerator;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\DAO\Setup;
use MyVideoRoomPlugin\Library\TemplateIcons;
use MyVideoRoomPlugin\Shortcode\App;

/**
 * Class SiteDefaults
 */
class SiteDefaults {
	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_invitemenu';
	// All Up Site Default Master Setting.
	const ROOM_NAME_SITE_DEFAULT = 'site-default-settings';

	// Default User ID to Use for Room Site Defaults .
	const USER_ID_SITE_DEFAULTS = - 1;

	// Default Database Table Names.
	const TABLE_NAME_MODULE_CONFIG         = 'myvideoroom_module_config';
	const TABLE_NAME_ROOM_MAP              = 'myvideoroom_room_post_mapping';
	const TABLE_NAME_USER_VIDEO_PREFERENCE = 'myvideoroom_user_video_preference';

	// Module Names and IDs.
	const MODULE_DEFAULT_VIDEO_NAME = 'default-video-module';
	const MODULE_DEFAULT_VIDEO_ID   = 1;

	// Listing Security Module ID in Core, so it can be checked for in Core Class to exit gracefully.
	const MODULE_SECURITY_ID = Dependencies::MODULE_SECURITY_ID;

	/**
	 * If the initialised function already been run.
	 *
	 * @var bool
	 */
	private static bool $has_initialised = false;

	/**
	 * Initialise On Module Activation
	 * Once off functions for activating Module
	 */
	public function activate_module() {
		Factory::get_instance( Setup::class )->install_room_post_mapping_table();
		Factory::get_instance( Setup::class )->install_user_video_preference_table();
		Factory::get_instance( Setup::class )->install_module_config_table();
		Factory::get_instance( Setup::class )->initialise_default_video_settings();
		Factory::get_instance( ModuleConfig::class )->register_module_in_db(
			self::MODULE_DEFAULT_VIDEO_NAME,
			self::MODULE_DEFAULT_VIDEO_ID,
			true
		);

		Factory::get_instance( ModuleConfig::class )->update_enabled_status(
			self::MODULE_DEFAULT_VIDEO_ID,
			true
		);
	}

	/**
	 * Runtime Functions.
	 */
	public function init() {
		$this->register_enqueue_scripts_styles();
		\add_shortcode(
			self::SHORTCODE_TAG,
			array(
				Factory::get_instance( MeetingIdGenerator::class ),
				'invite_menu_shortcode',
			)
		);
	}

	/**
	 * Registers All Centrally Used Scripts and Styles.
	 *
	 * @return void
	 */
	private function register_enqueue_scripts_styles() {
		$plugin_version = Factory::get_instance( Version::class )->get_plugin_version();

		// --
		// javascript

		wp_register_script(
			'myvideoroom-protect-input',
			plugins_url( '/js/protect-input.js', __FILE__ ),
			null,
			$plugin_version,
			true
		);

		wp_register_script(
			'myvideoroom-admin-tabs',
			plugins_url( '/js/tabbed.js', __FILE__ ),
			array( 'jquery' ),
			$plugin_version,
			true
		);

		// --
		// css

		wp_register_style(
			'myvideoroom-menutab-header',
			plugins_url( '/css/menutab.css', __FILE__ ),
			false,
			$plugin_version
		);

		wp_register_style(
			'myvideoroom-template',
			plugins_url( '/css/template.css', __FILE__ ),
			false,
			$plugin_version
		);

		wp_register_style(
			'myvideoroom-admin-css',
			plugins_url( '/css/admin.css', __FILE__ ),
			false,
			$plugin_version
		);

		\wp_enqueue_style( 'myvideoroom-template' );
		\wp_enqueue_style( 'myvideoroom-menutab-header' );
		\wp_enqueue_style( 'myvideoroom-admin-css' );
		\wp_enqueue_script( 'myvideoroom-admin-tabs' );

	}

	/**
	 * Generates all default Room Names for All Functions that use Video Rooms
	 * Video functions call this function to get default room names to ensure all generate consistently
	 *
	 * @param string $type     - type of room to name.
	 * @param mixed  $input_id - optional ID to pass for usage.
	 *
	 * @return string
	 */
	public function room_map( string $type, $input_id = null ): string {
		switch ( $type ) {
			case 'sitevideo':
				return preg_replace( '/[^A-Za-z0-9\-]/', '', get_bloginfo( 'name' ) ) . '-' . preg_replace( '/[^A-Za-z0-9\-]/', '', $input_id );
			case 'userbr':
				$user_field = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_id( $input_id );

				$displayid    = $user_field->display_name;
				$output       = preg_replace( '/[^A-Za-z0-9\-]/', '', $displayid ); // remove special characters from username.
				$outmeetingid = Factory::get_instance( MeetingIdGenerator::class )->invite( $input_id, 'user' );

				return 'Space-' . $output . '-' . $outmeetingid;

			case 'group':
				global $bp;

				return 'Group-' . $bp->groups->current_group->slug . '-Space';

			case 'mvr':
				$user       = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_id( (int) $input_id );
				$user_roles = Factory::get_instance( UserRoles::class, array( $user ) );

				$output = '';

				// IF staff member - then replace the ID with Owner ID.
				if ( $user && $user_roles->is_wcfm_shop_staff() ) {
					$parent_id = $user->_wcfm_vendor;

					$user_field = Factory::get_instance( WordPressUser::class )->get_wordpress_user_by_id( $parent_id );

					$input_id = $parent_id;

					$displayid = $user_field->display_name;
					$output    = preg_replace( '/[^A-Za-z0-9\-]/', '', $displayid ); // remove special characters from username.
				}
				$outmeetingid = Factory::get_instance( MeetingIdGenerator::class )->invite( $input_id, 'user' );

				return 'Space-' . $output . '-' . $outmeetingid;

			default:
				return 'ERR: CC101 - No Room Type Provided';
		}
	}

	/**
	 * Splits Name of Meeting Rooms
	 *
	 * @param ?string $name The name to be split.
	 *
	 * @return string
	 */
	public function name_split( string $name = null ): string {
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

	/**
	 * Shortcode Initialise Filters Handler
	 * This function initialises all filters that must be created just in time for MyVideoRoom Pages
	 * Avoids initialising unnecessary filters and hooks when our shortcodes are not on page.
	 *
	 * @return bool
	 */
	public function shortcode_initialise_filters(): bool {
		if ( self::$has_initialised ) {
			return true;
		}

		self::$has_initialised = true;

		// Core Filters to add.

		// Add Icons to Video Headers of Room Video Status.
		\add_filter(
			'myvideoroom_template_icon_section',
			array(
				Factory::get_instance( TemplateIcons::class ),
				'add_default_video_icons_to_header',
			),
			10,
			4
		);

		// Hooks for Other Modules.
		\apply_filters( 'myvideoroom_shortcode_initialisation_filter_hook', '' );
		\do_action( 'myvideoroom_shortcode_initialisation_action_hook' );

		return true;
	}
}
