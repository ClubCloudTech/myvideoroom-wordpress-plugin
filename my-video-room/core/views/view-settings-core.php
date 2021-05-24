<?php
/**
 * Outputs the configuration settings for the video plugin
 *
 * @package MyVideoRoomExtrasPlugin\Views\Public\Admin
 */

/**
 * Render the admin page
 *
 * @param string $active_tab
 * @param array $tabs
 * @param array $messages
 *
 * @return string
 */

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\DAO\ModuleConfig;
use MyVideoRoomPlugin\Library\Dependencies;
use MyVideoRoomPlugin\Library\ShortcodeDocuments;
use MyVideoRoomPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\SiteDefaults;


return function (
	array $messages = array()
): string {
	wp_enqueue_style( 'myvideoroom-template' );
	wp_enqueue_style( 'myvideoroom-menutab-header' );
	$path   = '/core/views/header/header.php';
	$render = require WP_PLUGIN_DIR . '/my-video-room' . $path;
	//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Output already rendered safe upstream.
	echo $render( $messages, $module_tabs );
	ob_start();

	?>
<div class="mvr-outer-box-wrap">
		<h1><?php esc_html_e( 'Video Room Site Default Configuration', 'my-video-room' ); ?></h1>
		<?php
		$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( Dependencies::MODULE_SECURITY_ID );
		if ( $security_enabled ) {
			echo esc_html( Factory::get_instance( \MyVideoRoomPlugin\Module\Security\Templates\SecurityButtons::class )->site_wide_enabled() );
		}
		echo '<p>';
		esc_html_e(
			'The following settings define site wide video default parameters in case other modules have not set parameters there. These defaults will be used 
			if a user has not selected a setting for the room configuration, or it hasnt been defined at the module level. You can use the Template Browser tab
			to view room selection templates.',
			'my-video-room'
		);
		echo '<br></p>';
		?>
	</div>
<div class="mvr-outer-box-wrap">

	<?php
	$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings(
		SiteDefaults::USER_ID_SITE_DEFAULTS,
		SiteDefaults::ROOM_NAME_SITE_DEFAULT,
		array( 'basic', 'premium' )
	);
	//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
	echo $layout_setting;
	?>
</div>

<hr>
	<div>
		<?php
			Factory::get_instance( ShortcodeDocuments::class )->render_general_shortcode_docs();
		?>
	</div>


	<?php
	return ob_get_clean();
};

