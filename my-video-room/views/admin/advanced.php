<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

use MyVideoRoomPlugin\Plugin;

/**
 * Strips leading tabs from code, and renders in a HTML code block
 *
 * @param string $code The code to render.
 */
function render_code( string $code ) {
	$code_lines = explode( "\n", $code );

	preg_match_all( '/\t/', $code_lines[1], $matches );

	$indent_size = count( $matches[0] );

	$output_code = array_map(
		function ( $line ) use ( $indent_size ) {
			return preg_replace( '/^\t{' . $indent_size . '}/', '', $line );
		},
		$code_lines
	);

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<code>' . trim( implode( "\n", $output_code ) ) . '</code>';
}

/**
 * Render the admin page
 *
 * @param string $video_server The host of the video server
 */
return function (
	string $video_server,
	int $id_index = 0
): string {

	ob_start();

	?>

	<h2><?php esc_html_e( 'Advanced Settings and Information', 'myvideoroom' ); ?></h2>

	<nav class="nav-tab-wrapper myvideoroom-nav-tab-wrapper">
		<ul>
			<li>
				<a class="nav-tab nav-tab-active" href="#myvideoroom-development-<?php echo esc_attr( $id_index ); ?>">
					<?php esc_html_e( 'Module API/Development', 'myvideoroom' ); ?>
				</a>
			</li>

			<li>
				<a class="nav-tab" href="#myvideoroom-custom-licencing-<?php echo esc_attr( $id_index ); ?>">
					<?php esc_html_e( 'Licencing', 'myvideoroom' ); ?>
				</a>
			</li>
		</ul>
	</nav>

	<article class="myvideoroom-api-reference" id="myvideoroom-development-<?php echo esc_attr( $id_index ); ?>">

		<p>
			<?php
			esc_html_e(
				'MyVideoRoom has been build in a way that allows you to extend the core functionality by adding your own custom modules.',
				'myvideoroom'
			);
			?>
		</p>

		<p>
			<?php
			esc_html_e(
				'Modules can be added to WordPress as their own plugin, but instead registering themselves to the normal WordPress actions for initializing plugins, they should instead use the MyVideoRoom actions.',
				'myvideoroom'
			);
			?>
		</p>

		<p>
			<?php
			esc_html_e(
				'A very basic example is as follows:',
				'myvideoroom'
			);
			?>
		</p>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		render_code(
			"
			add_action(
				'myvideoroom_init',
				function () {
					\MyVideoRoomPlugin\Library\Module::register(
						'my-custom-module', // " . esc_html__( 'a unique slug for for the module', 'myvideoroom' ) . "'
						__( 'My Custom Module', 'mycustommodule' ), //  " . esc_html__( 'the name of the module', 'myvideoroom' ) . "'
						array( // " . esc_html__( 'a list of translated description paragraphs', 'myvideoroom' ) . "'
							__(
								'A custom module to extend MyVideoRoom.',
								'mycustommodule'
							)
						),
						fn() => MyCustomModuleInit() // " . esc_html__( 'a callback to initialize your module', 'myvideoroom' ) . "'
					);
				}
			);"
		);
		?>

		<h3><?php esc_html_e( 'API Reference', 'myvideoroom' ); ?></h3>

		<section>
			<h4><?php esc_html_e( 'Actions', 'myvideoroom' ); ?></h4>
			<p>
				<?php
				esc_html_e(
					'Actions added my MyVideoRoom for module development',
					'myvideoroom'
				);
				?>
			</p>

			<dl>
				<dt>myvideoroom_init</dt>
				<dd>
					<p><?php esc_html_e( 'Called once the core of MyVideoRoom is loaded.', 'myvideoroom' ); ?></p>
					<p>
						<?php
						printf(
							/* translators: %s is the code to register the plugin */
							esc_html__(
								'You can register your module with MyVideoRoom by calling %s in this callback.',
								'myvideoroom'
							),
							'<code class="inline">\MyVideoRoomPlugin\Library\Module::register()</code>'
						);
						?>
					</p>
				</dd>

				<dt>myvideoroom_admin_menu</dt>
				<dd>
					<p><?php esc_html_e( 'Called when the MyVideoRoom menu is being loaded.', 'myvideoroom' ); ?></p>
					<p>
						<?php
						esc_html_e(
							'The first parameter passed to the action is a callback to register an additional item into the menu.',
							'myvideoroom'
						);
						?>
					</p>
					<p>
						<?php
						render_code(
							'
							function (
								string $slug,			// ' . esc_html__( 'The page slug to generate a url.', 'myvideoroom' ) . ' 
								string $title,			// ' . esc_html__( 'The title of the admin page.', 'myvideoroom' ) . ' 
								callable $callback,		// ' . esc_html__( 'A callback returning a string containing the admin page content.', 'myvideoroom' ) . ' 
								int $offset = -1,		// ' . esc_html__( 'The position of the admin menu.', 'myvideoroom' ) . ' 
								string $dashicon = null // ' . esc_html__( 'An optional icon to show instead of the title.', 'myvideoroom' ) . ' 
							) {}'
						);
						?>
					</p>
				</dd>

				<dt>myvideoroom_admin_getting_started_steps</dt>
				<dd>
					<p><?php esc_html_e( 'Called when MyVideoRoom is preparing the getting started steps', 'myvideoroom' ); ?></p>
					<p>
						<?php
						printf(
							/* translators: %s is the namespace of the getting started class */
							esc_html__(
								'The first parameter passed to the action is the getting started object - @see %s.',
								'myvideoroom'
							),
							'\MyVideoRoomPlugin\ValueObject\GettingStarted'
						);
						?>
					</p>
				</dd>

				<dt>myvideoroom_shortcode_reference</dt>
				<dd>
					<p><?php esc_html_e( 'Called when MyVideoRoom is preparing the shortcode reference', 'myvideoroom' ); ?></p>
					<?php
					printf(
					/* translators: %s is the namespace of the getting started class */
						esc_html__(
							'The first parameter passed to the action is a callback that can be passed a shortcode reference object - @see %s.',
							'myvideoroom'
						),
						'\MyVideoRoomPlugin\Reference\Shortcode'
					);
					?>
				</dd>
			</dl>

			<dl>
				<dt>myvideoroom_enqueue_scripts</dt>
				<dd>
					<p>
					<?php
					esc_html_e(
						'Can be called to load the MyVideoRoom scripts. Required to load the MyVideoApp in the admin pages',
						'myvideoroom'
					);
					?>
					</p>
				</dd>
			</dl>
		</section>

		<h4><?php esc_html_e( 'Module methods' ); ?></h4>
		<p>
			<?php
			esc_html_e(
				'Additional methods that can be called on the module to add additional functionality.',
				'myvideoroom'
			);
			?>
		</p>
		<dl>
			<dt>add_compatibility_hook</dt>
			<dd>
				<p>
					<?php
					esc_html_e(
						'Register a callback to determine if the module is compatible with the current install of WordPress.',
						'myvideoroom'
					);
					?>
				</p>
				<code>function(): bool {}</code>
			</dd>

			<dt>add_admin_page_hook</dt>
			<dd>
				<p>
					<?php
					esc_html_e(
						'Register a callback that returns a admin settings page as a string.',
						'myvideoroom'
					);
					?>
				</p>
				<code>function(): string {}</code>
			</dd>

			<dt>add_activation_hook</dt>
			<dd>
				<p>
					<?php
					esc_html_e(
						'Register a callback that is run when the module is activated.',
						'myvideoroom'
					);
					?>
				</p>
				<code>function(): bool {}</code>
			</dd>

			<dt>add_deactivation_hook</dt>
			<dd>
				<p>
					<?php
					esc_html_e(
						'Register a callback that is run when the module is deactivated.',
						'myvideoroom'
					);
					?>
				</p>
				<code>function(): bool {}</code>
			</dd>
		</dl>
	</article>

	<article id="myvideoroom-custom-licencing-<?php echo esc_attr( $id_index ); ?>">
		<form method="post">
			<?php settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

			<fieldset>
				<table class="form-table" role="presentation">
					<tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN . '_' . $id_index ); ?>">
								<?php esc_html_e( 'ClubCloud Server Domain', 'myvideoroom' ); ?>
							</label>
						</th>

						<td>
							<input
									type="text"
									name="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN ); ?>"
									value="<?php echo esc_attr( $video_server ); ?>"
									id="<?php echo esc_attr( Plugin::SETTING_SERVER_DOMAIN . '_' . $id_index ); ?>"
							/>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="delete_activation_<?php echo esc_attr( $id_index ); ?>">
								<?php esc_html_e( 'Delete activation settings', 'myvideoroom' ); ?>
							</label>
						</th>

						<td>
							<input
									type="checkbox"
									name="delete_activation"
									value="on"
									id="delete_activation_<?php echo esc_attr( $id_index ); ?>"
							/>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>

			<?php wp_nonce_field( 'update_settings', 'myvideoroom_custom_settings_nonce' ); ?>
			<?php submit_button(); ?>
		</form>
	</article>
	<?php
	return ob_get_clean();
};
