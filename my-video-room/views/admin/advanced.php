<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin;

use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Library\HttpPost;

/**
 * Render the admin page
 *
 * @param string $video_server The host of the video server
 */
return function (
	string $video_server,
	int $id_index = 0
): string {

	$html_lib = Factory::get_instance( HTML::class, array( 'advanced' ) );
	\ob_start();

	?>
<!-- Module Header -->
<div class="myvideoroom-menu-settings">
	<div class="myvideoroom-header-table-left">
		<h1><i
				class="myvideoroom-header-dashicons dashicons-forms"></i><?php esc_html_e( 'Advanced Settings and Information', 'myvideoroom' ); ?>
		</h1>
	</div>
	<div class="myvideoroom-header-table-right">
		<h3 class="myvideoroom-settings-offset"><i data-target=""
				class="myvideoroom-header-dashicons dashicons-admin-settings "
				title="<?php esc_html_e( 'Settings', 'myvideoroom' ); ?>"></i>
		</h3>
	</div>
</div>
<!-- Navigation Marker -->
<nav class="myvideoroom-nav-tab-wrapper nav-tab-wrapper">
	<ul class="mvr-ul-style-top-menu">
		<li>
			<a class="nav-tab nav-tab-active" href="#<?php echo \esc_attr( $html_lib->get_id( 'development' ) ); ?>">
				<?php \esc_html_e( 'Module API/Development', 'myvideoroom' ); ?>
			</a>
		</li>

		<li>
			<a class="nav-tab" href="#<?php echo \esc_attr( $html_lib->get_id( 'licencing' ) ); ?>">
				<?php \esc_html_e( 'Licencing', 'myvideoroom' ); ?>
			</a>
		</li>
	</ul>
</nav>

<article id="<?php echo \esc_attr( $html_lib->get_id( 'development' ) ); ?>" class="myvideoroom-api-reference">
	<!-- Module State and Description Marker -->
	<div class="myvideoroom-feature-outer-table">
		<div id="module-state" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'Architecture', 'myvideoroom' ); ?></h2>
			<div id="parentmodule">

			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<p>
				<?php
				\esc_html_e(
					'MyVideoRoom has been built in a way that allows you to extend the core functionality by adding your own custom modules.',
					'myvideoroom'
				);
				?>
			</p>

			<p>
				<?php
				\esc_html_e(
					'Modules can be added to WordPress as their own plugin, but instead registering themselves to the normal WordPress actions for initializing plugins, they should instead use the MyVideoRoom actions.',
					'myvideoroom'
				);
				?>
			</p>

			<p>
				<?php
				\esc_html_e(
					'A very basic example is as follows:',
					'myvideoroom'
				);
				?>
			</p>

			<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html_lib->render_code_block(
				"
			\add_action(
				'" . \esc_attr( Plugin::ACTION_INIT ) . "',
				function () {
					\MyVideoRoomPlugin\Library\Module::register(
						'my-custom-module', // " . \esc_html__( 'a unique slug for for the module', 'myvideoroom' ) . "'
						\\esc_html__( 'My Custom Module', 'mycustommodule' ), //  " . \esc_html__( 'the name of the module', 'myvideoroom' ) . "'
						array( // " . esc_html__( 'a list of translated description paragraphs', 'myvideoroom' ) . "'
							\\esc_html__(
								'A custom module to extend MyVideoRoom.',
								'mycustommodule'
							)
						),
						fn() => MyCustomModuleInit() // " . \esc_html__( 'a callback to initialize your module', 'myvideoroom' ) . "'
					);
				}
			);"
			);
			?>

		</div>
	</div>

	<!-- Module State and Description Marker -->
	<div class="myvideoroom-feature-outer-table">
		<div id="module-state" class="myvideoroom-feature-table-small">
			<h2><?php esc_html_e( 'API Reference', 'myvideoroom' ); ?></h2>
			<div id="parentmodule">

			</div>
		</div>
		<div class="myvideoroom-feature-table-large">
			<section>
				<h4><?php \esc_html_e( 'Actions', 'myvideoroom' ); ?></h4>
				<p>
					<?php
					\esc_html_e(
						'Actions added my MyVideoRoom for module development',
						'myvideoroom'
					);
					?>
				</p>

				<dl>
					<dt><?php echo \esc_attr( Plugin::ACTION_INIT ); ?></dt>
					<dd>
						<p><?php \esc_html_e( 'Called once the core of MyVideoRoom is loaded.', 'myvideoroom' ); ?></p>
						<p>
							<?php
							\printf(
							/* translators: %s is the code to register the plugin */
								\esc_html__(
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
						<p><?php \esc_html_e( 'Called when the MyVideoRoom menu is being loaded.', 'myvideoroom' ); ?>
						</p>
						<p>
							<?php
							\esc_html_e(
								'The first parameter passed to the action is a callback to register an additional item into the menu.',
								'myvideoroom'
							);
							?>
						</p>
						<p>
							<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $html_lib->render_code_block(
								'
					function (
						string $slug,			// ' . \esc_html__( 'The page slug to generate a url.', 'myvideoroom' ) . ' 
						string $title,			// ' . \esc_html__( 'The title of the admin page.', 'myvideoroom' ) . ' 
						callable $callback,		// ' . \esc_html__( 'A callback returning a string containing the admin page content.', 'myvideoroom' ) . ' 
						int $offset = -1,		// ' . \esc_html__( 'The position of the admin menu.', 'myvideoroom' ) . ' 
						string $dashicon = null // ' . \esc_html__( 'An optional icon to show instead of the title.', 'myvideoroom' ) . ' 
					) {}'
							);
							?>
						</p>
					</dd>

					<dt>myvideoroom_admin_getting_started_steps</dt>
					<dd>
						<p><?php \esc_html_e( 'Called when MyVideoRoom is preparing the getting started steps', 'myvideoroom' ); ?>
						</p>
						<p>
							<?php
							\printf(
							/* translators: %s is the namespace of the getting started class */
								\esc_html__(
									'The first parameter passed to the action is the getting started object - @see %s.',
									'myvideoroom'
								),
								'\MyVideoRoomPlugin\ValueObject\GettingStarted'
							);
							?>
						</p>
					</dd>

					<dt><?php echo esc_html( Admin::ACTION_SHORTCODE_REFERENCE ); ?>></dt>
					<dd>
						<p><?php \esc_html_e( 'Called when MyVideoRoom is preparing the shortcode reference', 'myvideoroom' ); ?>
						</p>
						<?php
						\printf(
						/* translators: %s is the namespace of the getting started class */
							\esc_html__(
								'The first parameter passed to the action is a callback that can be passed a shortcode reference object - @see %s.',
								'myvideoroom'
							),
							'\MyVideoRoomPlugin\Reference\Shortcode'
						);
						?>
					</dd>
				</dl>
			</section>

			<h4><?php \esc_html_e( 'Module methods' ); ?></h4>
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
						\esc_html_e(
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
						\esc_html_e(
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
						\esc_html_e(
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
						\esc_html_e(
							'Register a callback that is run when the module is deactivated.',
							'myvideoroom'
						);
						?>
					</p>
					<code>function(): bool {}</code>
				</dd>
			</dl>


		</div>
	</div>
	<!-- How it Works Marker -->


</article>

<article id="<?php echo \esc_attr( $html_lib->get_id( 'licencing' ) ); ?>">

<!-- Module State and Description Marker -->
<div class="myvideoroom-feature-outer-table">
	<div id="module-state" class="myvideoroom-feature-table-small">
		<h2><?php esc_html_e( 'Advanced Licensing', 'myvideoroom' ); ?></h2>
		<div id="parentmodule">

		</div>
	</div>
	<div class="myvideoroom-feature-table-large">
	<form method="post">
		<?php \settings_fields( Plugin::PLUGIN_NAMESPACE . '_' . Plugin::SETTINGS_NAMESPACE ); ?>

		<fieldset>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo \esc_attr( Plugin::SETTING_SERVER_DOMAIN . '_' . $id_index ); ?>">
								<?php \esc_html_e( 'ClubCloud Server Domain', 'myvideoroom' ); ?>
							</label>
						</th>

						<td>
							<input type="text" name="myvideoroom_server_domain"
								value="<?php echo \esc_attr( $video_server ); ?>"
								id="<?php echo \esc_attr( Plugin::SETTING_SERVER_DOMAIN . '_' . $id_index ); ?>" />
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="delete_activation_<?php echo \esc_attr( $id_index ); ?>">
								<?php \esc_html_e( 'Delete activation settings', 'myvideoroom' ); ?>
							</label>
						</th>

						<td>
							<input type="checkbox" name="myvideoroom_delete_activation" value="on"
								id="delete_activation_<?php echo \esc_attr( $id_index ); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo Factory::get_instance( HttpPost::class )->create_admin_form_submit( 'update_advanced_settings' );
		?>
	</form>

	</div>
</div>


</article>
	<?php
	return \ob_get_clean();
};
