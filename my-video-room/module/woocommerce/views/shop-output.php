<?php
/**
 * Outputs Formatted Table for WooCommerce Shopping Room Sync
 *
 * @package MyVideoRoomPlugin\Module\WooCommerce\Views\Shop-Output.php
 */

/**
 * Render the WooCommerce Basket Render Page
 *
 * @param string   $output   - The Product Archive.
 * @param string $last_queue -  The display table with the last basket queue shared in the room.
 * @param int    $shop_count -  The count of last items in shop to place in Div for user change tracking.
 * @param bool   $host_status -  Whether user is host.
 * @param string $room_admin -  The room admin tab (for hosts).
 *
 * @return string
 */
return function (
	$output,
	string $last_queue = null,
	int $shop_count = null,
	bool $host_status = null,
	string $room_admin = null
): string {

	ob_start();

	?>
<div id="basket-video-host-wrap-shop" class="mvr-nav-settingstabs-outer-wrap">
	<div class="mvr-storefront-master">
		<div id="storeid" data-last-storecount="<?php echo esc_attr( $shop_count ); ?>"></div>
		<div id="myvideoroom-roomstore-outer" class="mvr-admin-page-wrap">
			<h2 class="mvr-override-h2"><?php esc_html_e( 'Room Store and Basket', 'myvideoroom' ); ?></h2>
			<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper mvr-mobile-size">
				<ul class="mvr-ul-style-top-menu">
					<li>
						<a class="nav-tab nav-tab-active mvr-full-hide" href="#roomstore">
							<?php esc_html_e( 'Store', 'myvideoroom' ); ?>
						</a>
						<a class="nav-tab nav-tab-active mvr-mobile-hide" href="#roomstore">
							<?php esc_html_e( 'Room Store', 'myvideoroom' ); ?>
						</a>
					</li>

					<li>
						<a class="nav-tab mvr-full-hide" href="#basketprevious">
							<?php esc_html_e( 'Basket', 'myvideoroom' ); ?>
						</a>
						<a class="nav-tab mvr-mobile-hide" href="#basketprevious">
							<?php esc_html_e( 'Previous Basket', 'myvideoroom' ); ?>
						</a>
					</li>

					<?php
					if ( $host_status ) {
						?>
					<li>
						<a class="nav-tab mvr-full-hide" href="#roomcontrol">
							<?php esc_html_e( 'Manage', 'myvideoroom' ); ?>
						</a>
						<a class="nav-tab mvr-mobile-hide" href="#roomcontrol">
							<?php esc_html_e( 'Manage Room Store', 'myvideoroom' ); ?>
						</a>
					</li>
						<?php
					}
					?>
				</ul>
			</nav>
			<div id="video-host-wrap" class="mvr-admin-page-wrap">
				<article id="roomstore">
					<?php

					if ( strlen( $output ) > 100 ) {
						?>

					<table class="wp-list-table widefat plugins myvideoroom-table-adjust">
						<thead>
							<tr>
								<h2 class="mvr-override-h2">
									<?php
									esc_html_e( 'Room Store', 'myvideoroom' );
									?>
								</h2>
								<p>
									<?php
									esc_html_e( 'The Room store contains products the store owner wants to connect to this room. You can add products from here or add them via other baskets, search, or basket sync', 'myvideoroom' );
									?>
								</p>
							</tr>
						</thead>
						<tbody>
							<div class="mvr-woocommerce-overlay">
								<?php
								//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
								echo $output;
								?>
							</div>
						</tbody>
					</table>
						<?php
					} else {
						?>
					<p>
						<?php
								esc_html_e(
									'This room has no products available in its store category or tags.',
									'myvideoroom'
								);
						?>
					</p>


						<?php
					}
					?>
				</article>
				<article id="basketprevious">
					<?php
					if ( $last_queue ) {
						?>
					<h2>
						<?php
						esc_html_e( 'Previously Shared Basket', 'myvideoroom' );
						?>
					</h2>
					<p>
						<?php
						esc_html_e( 'This room previously shared a basket. You can grab a copy of its last products here.', 'myvideoroom' );
						?>
					</p>
					<div class="mvr-woocommerce-overlay">
						<?php
							//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted from WooCommerce no escape needed.
							echo $last_queue;
						?>
					</div>
						<?php
					} else {
						?>
					<p><?php esc_html_e( 'This room hasn\'t yet shared a basket', 'myvideoroom' ); ?></p>
						<?php
					}
					?>

				</article>
				<?php
				if ( $host_status ) {
					?>
				<article id="roomcontrol">
					<?php
						//phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped - This is pre-formatted no escape needed.
						echo $room_admin;
					?>
				</article>
					<?php
				}
				?>

			</div>

		</div>
	</div>
	<?php
	return ob_get_clean();
};
