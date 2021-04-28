<?php
/**
 * Outputs the header for admin pages
 *
 * @package MyVideoRoomPlugin\Header
 */

return function (
	string $active_tab,
	array $tabs,
	array $messages = array()
): string {
	ob_start();
	?>
	<table style="width:100%">
			<tr>
					<th style="width:30%" >
						<img
								src="<?php echo esc_url( plugins_url( '/mvr-imagelogo.png', realpath( __DIR__ . '/../' ) ) ); ?>"
								alt="My Video Room Extras"
								width="120"
								height="120"
						/>
					</th>

					<th style="width:70%">
						<h1>My Video Room Settings and Configuration</h1>
					</th>
			</tr>
	</table>


	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab_key => $tab_name ) {
			$active = '';
			if ( $active_tab === $tab_key ) {
				$active = ' nav-tab-active';
			}

			echo '<a class="nav-tab' . esc_attr( $active ) . '" href="?page=my-video-room-extras&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_name ) . '</a>';
		}
		?>
	</h2>

	<ul>
		<?php
		foreach ( $messages as $message ) {
			echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
		}
		?>
	</ul>
	<?php
	return ob_get_clean();
};


