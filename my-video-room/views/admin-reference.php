<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare(strict_types=1);

use MyVideoRoomPlugin\AppShortcode;
use MyVideoRoomPlugin\MonitorShortcode;

return function (): string {
	?>
<br>
<div class="outer-box-wrap">
	<table style="width:100%">
		<tr>
			<th class="mvr-header-table">
				<h1 class="mvr-heading-head-top"><?php esc_html_e( 'Shortcode Reference', 'myvideoroom' ); ?></h1>
			</th>
			<th class="mvr-visualiser-image">
				<img src="<?php echo esc_url( plugins_url( '/img/mvr-imagelogo.png', realpath( __DIR__ . '/' ) ) ); ?>"
					alt="My Video Room Extras" width="90" height="90" />
			</th>
		</tr>
	</table>
	<div class="mvr-tab-align">
		<a id="myvideoroomapp"></a>
		<h2><?php esc_html_e( 'App Shortcode', 'myvideoroom' ); ?></h2>
		<p>
			<?php
			    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The translated text will be escaped, but we want to render the link correctly.
				printf(
					/* translators: %s is the text "WordPress Shortcodes" and links to the WordPress help page for shortcodes */
					esc_html__( 'You can use the following %s to add the My Video Room app to a page.', 'myvideoroom' ),
					'<a href="https://support.wordpress.com/shortcodes/" target="_blank">' . esc_html__( 'WordPress Shortcodes', 'myvideoroom' ) . '</a>'
				);
			?>
		</p>

		<h3><?php esc_html_e( 'My Video Room App', 'myvideoroom' ); ?></h3>
		<p><?php esc_html_e( 'This shows the video app', 'myvideoroom' ); ?></p>
		<code class="myvideoroom-admin-code">
			[<?php echo esc_html( AppShortcode::SHORTCODE_TAG ); ?>
				name="<?php esc_html_e( 'My Video Room', 'myvideoroom' ); ?>"
				layout="clubcloud"
				lobby=true
				admin=true
			]
		</code><br />
		<br />
		<p>
			<?php
			esc_html_e(
				'This will show the video with a room name of "The Meeting Room", using the default "clubcloud" layout.The lobby will be enabled, but the user viewing this page will be an admin of the video.',
				'myvideoroom'
			);
			?>

		</p>

		<table class="wp-list-table widefat plugins">
			<thead>
				<tr>
					<th class="manage-column column-name column-primary"><?php esc_html_e( 'Param', 'myvideoroom' ); ?>
					</th>
					<th class="manage-column column-name column-primary">
						<?php esc_html_e( 'Details', 'myvideoroom' ); ?>
					</th>
					<th class="manage-column column-name column-primary">
						<?php esc_html_e( 'Default', 'myvideoroom' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="active">
					<th class="manage-column column-name column-primary" colspan="4">
						<strong><?php esc_html_e( 'Main settings:', 'myvideoroom' ); ?></strong>
					</th>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>name</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The name of the room', 'myvideoroom' ); ?></p>

						<p>
							<?php
							esc_html_e(
								'All shortcodes on the same domain that share a room name will put users into the same video group. This allows you to have different entry points for admins and non admins.',
								'myvideoroom'
							);
							?>
						</p>

						<p><?php esc_html_e( 'The room name will be visible to users inside the video.', 'myvideoroom' ); ?>
						</p>
					</td>
					<td><?php echo esc_html( get_bloginfo( 'name' ) ); ?></td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>layout</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The id of the layout to display', 'myvideoroom' ); ?></p>

						<p>
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The translated text will be escaped, but we want to render the link correctly.
							printf(
								/* translators: %s is a link to the available layouts */
								esc_html__( 'A list of available layouts are available here: %s', 'myvideoroom' ),
								'<a href="https://rooms.clubcloud.tech/views/layouts">https://rooms.clubcloud.tech/views/layouts</a>'
							);
							?>
						</p>

						<p>
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The translated text will be escaped, but we want to render the link correctly.
							printf(
								/* translators: %s is a link to the available layouts in JSON format */
								esc_html__( 'The layout list is also available in a JSON format: %s', 'myvideoroom' ),
								'<a href="https://rooms.clubcloud.tech/layouts">https://rooms.clubcloud.tech/layouts</a>'
							);
							?>
						</p>
					</td>
					<td>"boardroom"</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>admin</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'Whether the user should be an admin', 'myvideoroom' ); ?></p>

						<p>
							<?php
							esc_html_e(
								'Admins have the ability to add users to rooms, and move users between rooms.',
								'myvideoroom'
							);
							?>
						</p>

						<p><?php esc_html_e( 'You need at least one admin to start a video session.', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>false</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>user-name</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'Allows override of the displayed user\'s name in the video participant list.', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>(<?php esc_html_e( 'For logged in users will display their "Display Name". For guests will prompt for a name.', 'myvideoroom' ); ?>)
					</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>loading-text</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'Text to show while the app is loading', 'myvideoroom' ); ?></p>
					</td>
					<td>"<?php esc_html_e( 'Loading...', 'myvideoroom' ); ?>"</td>
				</tr>

			</tbody>

			<tbody>
				<tr class="active">
					<th class="manage-column column-name column-primary" colspan="4">
						<strong><?php esc_html_e( 'Admin settings:', 'myvideoroom' ); ?></strong>
					</th>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>lobby</em></th>
					<td class="column-description">
						<p>
							<?php esc_html_e( 'Whether the lobby inside the video app should be enabled for non admin users', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>false</td>
				</tr>
			</tbody>

			<tbody>
				<tr class="active">
					<th class="manage-column column-name column-primary" colspan="4">
						<strong><?php esc_html_e( 'Non-admin settings:', 'myvideoroom' ); ?></strong>
					</th>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>reception</em></th>
					<td class="column-description">
						<p>
							<?php esc_html_e( 'Whether the reception before entering the app should be enabled', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>false</td>
				</tr>
				<tr class="inactive">
					<th class="column-primary"><em>reception-id</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The id of the reception image to use', 'myvideoroom' ); ?></p>

						<p>
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The translated text will be escaped, but we want to render the link correctly.
							printf(
							/* translators: %s is a link to the available reception */
								esc_html__( 'A list of available reception are available here: %s', 'myvideoroom' ),
								'<a href="https://rooms.clubcloud.tech/views/reception">https://rooms.clubcloud.tech/views/reception</a>'
							);
							?>
						</p>

						<p>
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The translated text will be escaped, but we want to render the link correctly.
							printf(
								/* translators: %s is a link to the available reception in JSON format */
								esc_html__( 'The reception list is also available in a JSON format: %s', 'myvideoroom' ),
								'<a href="https://rooms.clubcloud.tech/reception">https://rooms.clubcloud.tech/reception</a>'
							);
							?>
						</p>
					</td>
					<td>"office"</td>
				</tr>
				<tr class="inactive">
					<th class="column-primary"><em>reception-video</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'A link to a video to play in the reception. Will only work if the selected reception supports video', 'myvideoroom' ); ?>
						</p>
					</td>
					<td><?php esc_html_e( '(Use reception setting)', 'myvideoroom' ); ?></td>
				</tr>
				<tr class="inactive">
					<th class="column-primary"><em>floorplan</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'Whether the floorplan should be shown', 'myvideoroom' ); ?></p>
					</td>
					<td>false</td>
				</tr>
			</tbody>
		</table>
		<br />
		<h3><?php esc_html_e( 'My Video Room Reception Widget', 'myvideoroom' ); ?></h3>
		<p><?php esc_html_e( 'This shows the number of people currently waiting in a room', 'myvideoroom' ); ?></p>
		<code class="myvideoroom-admin-code">
			[<?php echo esc_html( MonitorShortcode::SHORTCODE_TAG ); ?>
				name="<?php esc_html_e( 'My Video Room', 'myvideoroom' ); ?>"
				text-empty="<?php esc_html_e( 'Nobody is currently waiting', 'myvideoroom' ); ?>"
				text-single="<?php esc_html_e( 'One person is waiting in reception', 'myvideoroom' ); ?>"
				text-plural="<?php esc_html_e( '{{count}} people are waiting in reception', 'myvideoroom' ); ?>"
			]
		</code><br />
		<br />

		<table class="wp-list-table widefat plugins">
			<thead>
				<tr>
					<th class="manage-column column-name column-primary"><?php esc_html_e( 'Param', 'myvideoroom' ); ?>
					</th>
					<th class="manage-column column-name column-primary">
						<?php esc_html_e( 'Details', 'myvideoroom' ); ?>
					</th>
					<th class="manage-column column-name column-primary">
						<?php esc_html_e( 'Default', 'myvideoroom' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<tr class="inactive">
					<th class="column-primary"><em>name</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The name of the room', 'myvideoroom' ); ?></p>
					</td>
					<td><?php echo esc_html( get_bloginfo( 'name' ) ); ?></td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>text-empty</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The text to show when nobody is waiting', 'myvideoroom' ); ?></p>
					</td>
					<td>"<?php esc_html_e( 'Nobody is currently waiting', 'myvideoroom' ); ?>"</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>text-empty-plain</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The plain text to show when nobody is waiting', 'myvideoroom' ); ?></p>
						<p><?php esc_html_e( 'To be used in notifications where `text-empty` contains HTML', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>(text-empty)</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>text-single</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The text to show when a single person is waiting', 'myvideoroom' ); ?></p>
					</td>
					<td>"<?php esc_html_e( 'One person is waiting in reception', 'myvideoroom' ); ?>"</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>text-single-plain</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The plain text to show a single person is waiting', 'myvideoroom' ); ?>
						</p>
						<p><?php esc_html_e( 'To be used in notifications where `text-single` contains HTML', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>(text-single)</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>text-plural</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The text to show when a more than one person is waiting. "{{count}}" will be substituted with the actual count', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>"<?php esc_html_e( '{{count}} people are waiting in reception', 'myvideoroom' ); ?>"</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>text-plural-plain</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The text to show when a more than one person is waiting. "{{count}}" will be substituted with the actual count', 'myvideoroom' ); ?>
						</p>
						<p><?php esc_html_e( 'To be used in notifications where `text-plural` contains HTML', 'myvideoroom' ); ?>
						</p>
					</td>
					<td>(text-plural)</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>loading-text</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The text to show while the widget is loading', 'myvideoroom' ); ?></p>
					</td>
					<td>"<?php esc_html_e( 'Loading...', 'myvideoroom' ); ?>"</td>
				</tr>

				<tr class="inactive">
					<th class="column-primary"><em>type</em></th>
					<td class="column-description">
						<p><?php esc_html_e( 'The type of count to show:', 'myvideoroom' ); ?></p>

						<dl>
							<dt>"reception":</dt>
							<dd><?php esc_html_e( 'The number of people waiting in reception', 'myvideoroom' ); ?></dd>

							<dt>"seated":</dt>
							<dd><?php esc_html_e( 'The number of people currently seated', 'myvideoroom' ); ?></dd>

							<dt>"all":</dt>
							<dd><?php esc_html_e( 'The total number of people, including reception, seated and non-seated admins', 'myvideoroom' ); ?>
							</dd>
						</dl>

					</td>
					<td>"reception"</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

	<?php
	return ob_get_clean();
};
