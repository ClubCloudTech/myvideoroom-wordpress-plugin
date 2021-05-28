<?php
/**
 * Returns the list of available options for setting room permissions
 *
 * @package MyVideoRoomPlugin/Module/RoomBuilder/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\RoomBuilder\Settings;

use MyVideoRoomPlugin\Library\AppShortcodeConstructor;
use MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action\SubmitButton;

/**
 * Class Reference
 */
class Actions {

	/**
	 * Get the actions for the settings section
	 *
	 * @param ?AppShortcodeConstructor $app_config The current shortcode config.
	 *
	 * @return \MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action[]
	 */
	public function get_settings_actions( AppShortcodeConstructor $app_config = null ): array {
		$settings_actions = array(
			'roombuilder_show_preview' => new SubmitButton(
				'roombuilder_show_preview',
				\esc_html__( 'Preview room and shortcode', 'myvideoroom' )
			),
		);

		return \apply_filters( 'myvideoroom_roombuilder_actions_settings', $settings_actions, $app_config );
	}

	/**
	 * Get the actions for the preview section
	 *
	 * @param ?AppShortcodeConstructor $app_config The current shortcode config.
	 *
	 * @return \MyVideoRoomPlugin\Module\RoomBuilder\Settings\Action[]
	 */
	public function get_preview_actions( AppShortcodeConstructor $app_config = null ): array {
		$preview_actions = array();

		return \apply_filters( 'myvideoroom_roombuilder_actions_preview', $preview_actions, $app_config );
	}

}
