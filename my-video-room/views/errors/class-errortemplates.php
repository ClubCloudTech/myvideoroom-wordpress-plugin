<?php
/**
 * Display Security Templates.
 *
 * @package my-video-room/views/errors/class-errortemplates.php
 */

namespace MyVideoRoomPlugin\Views\Errors;

/**
 * Class Error Templates
 * This class holds templates for Error Messages
 */
class ErrorTemplates {

	/**
	 * Blocked By Site Offline Template.
	 *
	 * @return string
	 */
	public static function invalid_room_name(): string {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>

		<div class="mvr-row mvr-background">
			<h2 class="mvr-header-text">
				<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This Room name Doesn\'t Exist', 'myvideoroom' );
				?>
			</h2>
			<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

			<p class="mvr-template-text">
				<?php
				esc_html_e( 'This Site Conference Room has been deleted in the database and no longer exists - please remove the room from Room Manager.', 'myvideoroom' );
				?>
			</p>
		</div>
		<?php

		return \ob_get_clean();
	}

}
