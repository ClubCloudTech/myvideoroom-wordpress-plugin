<?php
/**
 * Integration Addin functionality for BuddyPress
 *
 * @package MyVideoRoomPlugin\Modules\BuddyPress
 */

namespace MyVideoRoomPlugin\Module\BuddyPress;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressHelpers;
use MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressSecurity;
use MyVideoRoomPlugin\Module\BuddyPress\Library\BuddyPressVideo;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Library\Module;
use MyVideoRoomPlugin\Module\PersonalMeetingRooms\MVRPersonalMeeting;
use MyVideoRoomPlugin\Shortcode\App;

/**
 * Class BuddyPress
 */
class BuddyPress {
	const SHORTCODE_TAG = App::SHORTCODE_TAG . '_';
	// Constants For Buddypress Video Modules.
	const MODULE_BUDDYPRESS_NAME                   = 'buddypress-module';
	const MODULE_BUDDYPRESS_SLUG                   = 'buddypress';
	const MODULE_BUDDYPRESS_DISPLAY                = 'BuddyPress Settings';
	const MODULE_BUDDYPRESS_ID                     = 434;
	const MODULE_BUDDYPRESS_ADMIN_LOCATION         = '/modules/buddypress/views/view-settings-buddypress.php';
	const MODULE_BUDDYPRESS_VIDEO_SLUG             = 'myvideoroom';
	const MODULE_BUDDYPRESS_GROUP_NAME             = 'buddypress-group-module';
	const MODULE_BUDDYPRESS_GROUP_ID               = 837;
	const MODULE_BUDDYPRESS_USER_NAME              = 'buddypress-user-module';
	const MODULE_BUDDYPRESS_USER_ID                = 956;
	const MODULE_BUDDYPRESS_FRIENDS_NAME           = 'buddypress-friends-module';
	const MODULE_BUDDYPRESS_FRIENDS_ID             = 117;
	const ROOM_NAME_BUDDYPRESS_GROUPS_SITE_DEFAULT = 'site-default-bp-groups';
	const ROOM_NAME_BUDDYPRESS_GROUPS              = 'video-bp-groups';
	const DISPLAY_NAME_BUDDYPRESS_GROUPS           = 'Group ';
	const SETTING_IS_FRIEND                        = 'is_friend';
	const SETTING_DO_NOT_DISTURB                   = 'Do-Not-Disturb';
	const SETTING_STEALTH                          = 'Stealth-Remove-Video';
	const SETTING_DEFAULT_TAB_NAME                 = 'MyVideoRoom';

	/**
	 * Initialise On Module Activation
	 * Once off functions for activating Module
	 */
	public function activate_module() {
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_BUDDYPRESS_NAME, self::MODULE_BUDDYPRESS_ID, true, self::MODULE_BUDDYPRESS_ADMIN_LOCATION );
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_BUDDYPRESS_GROUP_NAME, self::MODULE_BUDDYPRESS_GROUP_ID, true, self::MODULE_BUDDYPRESS_ADMIN_LOCATION );
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_BUDDYPRESS_USER_NAME, self::MODULE_BUDDYPRESS_USER_ID, true, self::MODULE_BUDDYPRESS_ADMIN_LOCATION );
		Factory::get_instance( ModuleConfig::class )->register_module_in_db( self::MODULE_BUDDYPRESS_FRIENDS_NAME, self::MODULE_BUDDYPRESS_FRIENDS_ID, true, self::MODULE_BUDDYPRESS_ADMIN_LOCATION );

		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_BUDDYPRESS_ID, true );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_BUDDYPRESS_GROUP_ID, true );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_BUDDYPRESS_FRIENDS_ID, true );
		Factory::get_instance( ModuleConfig::class )->update_enabled_status( self::MODULE_BUDDYPRESS_USER_ID, true );

		// Initialize default tab options for User and Group Tab names, and bypass if already created.
		$user_check = \get_option( 'myvideoroom-buddypress-user-tab' );
		if ( ! $user_check ) {
			\update_option( 'myvideoroom-buddypress-user-tab', self::SETTING_DEFAULT_TAB_NAME );
		}
		$group_check = \get_option( 'myvideoroom-buddypress-group-tab' );
		if ( ! $group_check ) {
			\update_option( 'myvideoroom-buddypress-group-tab', self::SETTING_DEFAULT_TAB_NAME );
		}
	}
	/**
	 * Is Buddypress Active - checks if BuddyPress is enabled.
	 *
	 * @return bool
	 */
	public function is_buddypress_active() {
		return Factory::get_instance( Dependencies::class )->is_buddypress_active();
	}
	/**
	 * Can Module Be Activated- checks if BuddyPress is enabled, and checks Personal Video Module state.
	 *
	 * @return bool
	 */
	public function can_module_be_activated():bool {
		$buddypress_state     = $this->is_buddypress_active();
		$personal_video_state = Factory::get_instance( Module::class )->is_module_active_simple( MVRPersonalMeeting::MODULE_PERSONAL_MEETING_NAME );
		if ( $buddypress_state && $personal_video_state ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Install - initialisation function of class - used to call Shortcodes or main class functions.
	 *
	 * @return void|null.
	 */
	public function init() {
		$is_module_enabled     = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( self::MODULE_BUDDYPRESS_ID );
		$is_buddypress_enabled = $this->can_module_be_activated();
		if ( ! $is_module_enabled || ! $is_buddypress_enabled ) {
			return null;
		}

		add_shortcode( self::SHORTCODE_TAG . 'bpgroupname', array( $this, 'bp_groupname_shortcode' ) );

		$is_user_module_enabled  = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( self::MODULE_BUDDYPRESS_USER_ID );
		$is_group_module_enabled = Factory::get_instance( ModuleConfig::class )->is_module_activation_enabled( self::MODULE_BUDDYPRESS_GROUP_ID );

		if ( $is_module_enabled && $is_buddypress_enabled ) {
			if ( $is_user_module_enabled ) {
				$this->setup_root_nav_action();
			}
			if ( $is_group_module_enabled && function_exists( 'bp_is_groups_component' ) && bp_is_groups_component() ) {
				$this->setup_group_nav_action();
			}
		}

		// Render Other BuddyPress Shortcodes.
		Factory::get_instance( BuddyPressVideo::class )->init();

		// Action Hooks for Security.
		add_action( 'myvideoroom_security_preference_form', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_security_menu_hook' ), 10, 3 );
		add_filter( 'myvideoroom_security_render_block', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_security_friends_group_filter_hook' ), 10, 5 );
		add_filter( 'myvideoroom_security_change_room_name', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_change_room_name' ) );

		// Change UserIDs for Groups.
		add_filter( 'myvideoroom_security_change_user_id', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_change_user_id' ) );
		add_filter( 'myvideoroom_security_choosesettings_change_user_id', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_change_user_id' ) );
		add_filter( 'myvideoroom_video_choosesettings_change_user_id', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_change_user_id' ) );

		// Security Engine.
		add_action( 'myvideoroom_security_block_disabled_module', array( Factory::get_instance( BuddyPressSecurity::class ), 'mvrbp_disabled_module_block' ), 10, 1 );
	}

	/**
	 * Render BuddyPress Admin Page for Myvideoroom Modules Management Page.
	 *
	 * @return string
	 */
	public function render_buddypress_admin_page(): string {
		return ( require __DIR__ . '/views/view-settings-buddypress.php' )();
	}

	/**
	 * Naming Screen Functions Section - This section hosts the page construction templates for each named clickable function.
	 * Insert each function that the constructor above instantiates inside each separate template function
	 */

	/**
	 * Supports Naming of Groups, and returning of information.
	 *
	 * @param array $params - the array for the shortcode.
	 * @return ?string
	 */
	public function bp_groupname_shortcode( $params = array() ): ?string {
		if ( ! $this->is_buddypress_active() ) {
			return null;
		}
		global $bp;
		$type       = $params['type'] ?? 'name';
		$group_link = $bp->root_domain . '/' . \bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
		switch ( $type ) {
			case 'name':
				return $bp->groups->current_group->name;
			case 'group_video':
				return $group_link . self::MODULE_BUDDYPRESS_VIDEO_SLUG;
		}
	}

	/**
	 * Main Constructor - Adds Tabs for the User Video Room.
	 *
	 * @return null|void
	 */
	public function setup_root_nav_action() {
		if ( ! $this->is_buddypress_active() ) {
			return null;
		}

		$hide_tab_from_user = Factory::get_instance( BuddyPressSecurity::class )->block_friends_display();
		if ( ! $hide_tab_from_user ) {
			$tab_name = \get_option( 'myvideoroom-buddypress-user-tab' );
			if ( ! $tab_name ) {
				$tab_name = self::SETTING_DEFAULT_TAB_NAME;
			}
			// Setup My Video Tab. Section 1.
			\bp_core_new_nav_item(
				array(
					'name'                    => $tab_name,
					'slug'                    => self::MODULE_BUDDYPRESS_VIDEO_SLUG,
					'show_for_displayed_user' => true,
					'screen_function'         => array( $this, 'myvideo_render_main_screen_function' ),
					'item_css_id'             => 'far fa-address-card',
					'position'                => 1,
				)
			);
		}
	}
	/**
	 * User Main Screen Function.
	 * This function loads all tabs and subtabs by mounting template and then action.
	 *
	 * @return null|void
	 */
	public function myvideo_render_main_screen_function() {
		add_action( 'bp_template_content', array( Factory::get_instance( BuddyPressVideo::class ), 'bp_boardroom_video_host' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Default User Room Display Function.
	 *
	 * @return null|void
	 */
	public function setup_group_nav_action() {
		if ( ! $this->is_buddypress_active() || ! function_exists( 'bp_is_groups_component' ) || ! bp_is_groups_component() ) {
			return null;
		}
		global $bp;

		if ( function_exists( 'bp_is_active' ) && \bp_is_active( 'groups' ) && $bp->groups && $bp->groups->current_group ) {
			$tab_name = \get_option( 'myvideoroom-buddypress-group-tab' );
			if ( ! $tab_name ) {
				$tab_name = self::SETTING_DEFAULT_TAB_NAME;
			}

			$group_link = $bp->root_domain . '/' . $bp->groups->root_slug . '/' . $bp->groups->current_group->slug . '/';
			\bp_core_new_subnav_item(
				array(
					'name'            => $tab_name,
					'slug'            => self::MODULE_BUDDYPRESS_VIDEO_SLUG,
					'parent_url'      => $group_link,
					'parent_slug'     => $bp->groups->current_group->slug,
					'screen_function' => array( $this, 'group_video_main_screen_function' ),
					'user_has_access' => Factory::get_instance( BuddyPressHelpers::class )->bp_is_room_active( $bp->groups->current_group->slug, $bp->groups->current_group->creator_id ),
					'position'        => 300,
					'item_css_id'     => 'group-css',
				)
			);
		}
	}

	/**
	 * This function renders the group Video Meet tab function
	 *
	 * @return void
	 */
	public function group_video_main_screen_function():void {
		\add_action( 'bp_template_content', array( Factory::get_instance( BuddyPressVideo::class ), 'groupmeet_switch' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}
}
