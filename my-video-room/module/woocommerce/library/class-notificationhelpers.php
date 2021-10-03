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
			<button id="mvr-button-client-change_' . strval( wp_rand( 1, 2000 ) ) . '" class="myvideoroom-notification-area myvideoroom-button-link ' . $style . '"
			title="' . $title . '"
			data-target=' . $target_focus_class . '
			>
			<i class="myvideoroom-dashicons ' . $iconclass . ' myvideoroom-button-link">
				</i> ' . $message . '
				<i id="myvideoroom-button-dismiss-alert" class="myvideoroom-dashicons dashicons-dismiss myvideoroom-button-dismiss"
				title="' . esc_html__( 'Hide this notification', 'myvideoroom' ) . '"></i></button>';
	}
	/**
	 * Render Accept Master Notification Popup
	 *
	 * @param string $inbound_notification - Inbound buttons from filter.
	 * @return ?string
	 */
	public function render_accept_master_notification( string $inbound_notification = null ): string {

		$title                 = esc_html__( 'The basket master status has been transferred', 'myvideoroom' );
		$target_focus_id       = 'mvr-shopping-basket';
		$message               = \esc_html__( ' Basket Owner Changed ', 'myvideoroom' );
		$iconclass             = 'dashicons-cart dashicons-cart';
		$inbound_notification .= $this->render_client_change_notification( $title, $target_focus_id, $message, $iconclass );

		return $inbound_notification;

	}

	/**
	 * Render Accept Master Notification Popup
	 *
	 * @return ?string
	 */
	public function render_security_update_notification(): string {
		$title           = esc_html__( 'Room Security Settings have Changed', 'myvideoroom' );
		$target_focus_id = 'mvr-shopping-basket';
		$message         = \esc_html__( ' Room Security Changed ', 'myvideoroom' );
		$iconclass       = 'dashicons-shield-alt';
		return $this->render_client_change_notification( $title, $target_focus_id, $message, $iconclass );

	}
}
