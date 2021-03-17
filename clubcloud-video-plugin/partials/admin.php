<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package ClubCloudVideoPlugin\Admin
 */

declare(strict_types=1);

use ClubCloudVideoPlugin\AppShortcode;
use ClubCloudVideoPlugin\MonitorShortcode;
use ClubCloudVideoPlugin\Plugin;

?>

<div class="wrap">
	<h1>ClubCloud Video Short Code Settings</h1>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="?page=clubcloud-video&amp;tab=getting_started">Getting Started</a>
		<a class="nav-tab" href="?page=clubcloud-video&amp;tab=reference">Reference</a>
	</h2>

	<ul>
	<?php
	foreach ( $messages as $message ) {
		echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';

	}

	if ( esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) ) ) {
		$video_server = esc_attr( get_option( Plugin::SETTING_SERVER_DOMAIN ) );
	} else {
		$video_server = 'clubcloud.tech';
	};
	?>
	</ul>

	<h2>Settings</h2>
	<form method="post" action="options.php">
		<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<fieldset>
			<table class="form-table" role="presentation">
				<tbody>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>">
							ClubCloud Server Domain
						</label>
					</th>
					<td>
						<input
								type="text"
								name="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>"
								value="<?php echo esc_attr( $video_server ); ?>"
								id="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>"
								size="100"
						/>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>">
							ClubCloud Activation Key
						</label>
					</th>
					<td>
						<input
								type="text"
								name="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
								value="<?php echo esc_attr( get_option( Plugin::SETTING_ACTIVATION_KEY ) ); ?>"
								placeholder="
								<?php
								if ( get_option( Plugin::SETTING_PRIVATE_KEY ) ) {
									echo '(hidden)';
								} else {
									echo '(Provided by ClubCloud)'; }
								?>
								"
								id="<?php echo esc_attr( Plugin::SETTING_ACTIVATION_KEY ); ?>"
								size="100"
						/>
					</td>
				</tr>
				</tbody>
			</table>
		</fieldset>

		<?php submit_button(); ?>
	</form>

	<h2>App ShortCode</h2>
	<p>You can use the following
		<a href="https://support.wordpress.com/shortcodes/" target="_blank">ShortCodes</a> to add the ClubCloud video app to a page.
	</p>

	<h3>ClubCloud App</h3>
	<p>This shows the video app</p>
	<code>
		[<?php echo esc_html( AppShortcode::SHORTCODE_TAGS[0] ); ?>
			name="The Meeting Room"
			layout="clubcloud"
			lobby=true
			admin=true
		]
	</code><br />
	<br />
	<p>
		This will show the video with a room name of "The Meeting Room", using the default "clubcloud" layout.
		The lobby will be enabled, but the user viewing this page will be an admin of the video.
	</p>

	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
				<th class="manage-column column-name column-primary">Param</th>
				<th class="manage-column column-name column-primary">Details</th>
				<th class="manage-column column-name column-primary">Required</th>
				<th class="manage-column column-name column-primary">Default</th>
			</tr>
		</thead>
		<tbody>
			<tr class="active">
				<th class="manage-column column-name column-primary" colspan="4">
					<strong>Main settings:</strong>
				</th>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>name</em></th>
				<td class="column-description">
					<p>The name of the room</p>

					<p>
						All shortcodes on the same domain that share a room name will put users into the same video group.
						This allows you to have different entry points for admins and non admins.
					</p>

					<p>
						The room name will be visible to users inside the video.
					</p>
				</td>
				<td>required</td>
				<td></td>
			</tr>
			<tr class="inactive">
				<th class="column-primary"><em>layout</em></th>
				<td class="column-description">
					<p>The id of the layout to display</p>

					<p>
						A list of available layouts are available here: <a href="https://rooms.clubcloud.tech/layouts.html">https://rooms.clubcloud.tech/layouts.html</a>
					</p>

					<p>
						The layout list is also available in a JSON format: <a href="https://rooms.clubcloud.tech/layouts">https://rooms.clubcloud.tech/layouts</a>
					</p>
				</td>
				<td>required</td>
				<td></td>
			</tr>
			<tr class="inactive">
				<th class="column-primary"><em>admin</em></th>
				<td class="column-description">
					<p>Whether the user should be an admin</p>

					<p>
						Admins have the ability to add users to rooms, and move users between rooms.
					</p>

					<p>
						You need at least one admin to start a video session.
					</p>
				</td>
				<td>optional</td>
				<td>false</td>
			</tr>
			<tr class="inactive">
				<th class="column-primary"><em>loading-text</em></th>
				<td class="column-description"><p>Text to show while the app is loading</p></td>
				<td>optional</td>
				<td>"Loading..."</td>
			</tr>
		</tbody>

		<tbody>
			<tr class="active">
				<th class="manage-column column-name column-primary" colspan="4">
					<strong>Admin settings:</strong>
				</th>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>lobby</em></th>
				<td class="column-description"><p>Whether the lobby inside the video app should be enabled for non admin users</p></td>
				<td>optional</td>
				<td>false</td>
			</tr>
		</tbody>

		<tbody>
			<tr class="active">
				<th class="manage-column column-name column-primary" colspan="4">
					<strong>Non-admin settings:</strong>
				</th>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>reception</em></th>
				<td class="column-description"><p>Whether the reception before entering the app should be enabled</p></td>
				<td>optional</td>
				<td>false</td>
			</tr>
			<tr class="inactive">
				<th class="column-primary"><em>reception-id</em></th>
				<td class="column-description">
					<p>The id of the reception image to use</p>


					<p>
						A list of available reception images are available here: <a href="https://rooms.clubcloud.tech/receptions.html">https://rooms.clubcloud.tech/receptions.html</a>
					</p>

					<p>
						The reception image list is also available in a JSON format: <a href="https://rooms.clubcloud.tech/receptions">https://rooms.clubcloud.tech/receptions</a>
					</p>
				</td>
				<td>optional</td>
				<td>"office"</td>
			</tr>
			<tr class="inactive">
				<th class="column-primary"><em>reception-video</em></th>
				<td class="column-description">
					<p>A link to a video to play in the reception. Will only work if the selected reception supports video</p>
				</td>
				<td>optional</td>
				<td>(Use reception setting)</td>
			</tr>
			<tr class="inactive">
				<th class="column-primary"><em>floorplan</em></th>
				<td class="column-description">
					<p>Whether the floorplan should be shown</p>
				</td>
				<td>optional</td>
				<td>false</td>
			</tr>
		</tbody>
	</table>
	<br />

	<h3>ClubCloud Reception Widget</h3>
	<p>This shows the number of people currently waiting in a room</p>
	<code>
		[<?php echo esc_html( MonitorShortcode::SHORTCODE_TAGS[0] ); ?>
			name="ClubCloud.tech"
			text-empty="Nobody is currently waiting"
			text-single="One person is waiting in reception"
			text-plural="{{count}} people are waiting in reception"
		]
	</code><br/>
	<br />

	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
				<th class="manage-column column-name column-primary">Param</th>
				<th class="manage-column column-name column-primary">Details</th>
				<th class="manage-column column-name column-primary">Required</th>
				<th class="manage-column column-name column-primary">Default</th>
			</tr>
		</thead>

		<tbody>
			<tr class="inactive">
				<th class="column-primary"><em>name</em></th>
				<td class="column-description"><p>The name of the room</p></td>
				<td>required</td>
				<td></td>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>text-empty</em></th>
				<td class="column-description"><p>The text to show when nobody is waiting</p></td>
				<td>optional</td>
				<td>"Nobody is currently waiting"</td>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>text-single</em></th>
				<td class="column-description"><p>The text to show when a single person is waiting</p></td>
				<td>optional</td>
				<td>"One person is waiting in reception"</td>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>text-plural</em></th>
				<td class="column-description"><p>The text to show when a more than one person is waiting. "{{count}}" will be substituted with the actual count</p></td>
				<td>optional</td>
				<td>"{{count}} people are waiting in reception"</td>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>loading-text</em></th>
				<td class="column-description"><p>The text to show while the widget is loading</p</td>
				<td>optional</td>
				<td>"Loading..."</td>
			</tr>

			<tr class="inactive">
				<th class="column-primary"><em>type</em></th>
				<td class="column-description">
					<p>The type of count to show:</p>

					<dl>
						<dt>"reception":</dt>
						<dd>The number of people waiting in reception</dd>

						<dt>"seated":</dt>
						<dd>The number of people currently seated</dd>

						<dt>"all":</dt>
						<dd>The total number of people, including reception, seated and non-seated admins</dd>
					</dl>

				</td>
				<td>optional</td>
				<td>"reception"</td>
			</tr>
		</tbody>
	</table>
</div>
