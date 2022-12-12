<?php
/**
 * Outputs the header for admin pages
 *
 * @package MyVideoRoomPlugin\Header
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\ValueObject\Notice;

/**
 * Render a notice
 *
 * @param Notice $notice The notice.
 */
return function (
	Notice $notice
): string {
	\ob_start();

	?>
	<div class="notice notice-<?php echo \esc_attr( $notice->get_type() ); ?> is-dismissible">
		<p>
			<?php echo \esc_html( $notice->get_message() ); ?>
		</p>
	</div>
	<?php
	return \ob_get_clean();

};
