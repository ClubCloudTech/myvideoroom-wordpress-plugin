<?php
/**
 * Button Helper Functions
 *
 * @package MyVideoRoomPlugin/Library/class-notificationhelpers.php
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\WooCommerce\Library;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Module\WooCommerce\Library\HostManagement;
use MyVideoRoomPlugin\Module\WooCommerce\WooCommerce;

/**
 * Class Button Helpers. Helps Render Buttons
 */
class NotificationHelpers {

	/**
	 * Render Save Category Button
	 *
	 * @param string $title - Message to Display on Button.
	 * @param string $target_focus_class - the class message to inject (sends a class name to trigger JS focus on).
	 * @param string $message - the message to display on button.
	 * @param string $iconclass - the dashicon or icon class to use for button icon.
	 * @param string $style - a stylesheet to pass to the button (optional).
	 * @return ?string
	 */
	public function render_client_change_notification( string $title = null, string $target_focus_class, string $message, string $iconclass, string $style = null ): string {

			return '
			<button id="mvr-button-client-change_' . strval( wp_rand( 1, 20000 ) ) . '" class="myvideoroom-notification-area myvideoroom-button-link ' . $style . '"
			title="' . $title . '"
			data-target=' . $target_focus_class . '
			>
			<i class="myvideoroom-dashicons ' . $iconclass . ' myvideoroom-button-link">
				</i> ' . $message . '
				<i id="myvideoroom-button-dismiss-alert" class="myvideoroom-dashicons dashicons-dismiss myvideoroom-button-dismiss"
				title="' . esc_html__( 'Hide this notification', 'myvideoroom' ) . '"></i></button>';
	}

}
