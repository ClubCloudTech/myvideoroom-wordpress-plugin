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
use MyVideoRoomPlugin\Core\DAO\ModuleConfig;
use MyVideoRoomPlugin\Core\Library\ShortcodeDocuments;
use MyVideoRoomPlugin\Core\Setup\RoomAdmin;
use MyVideoRoomPlugin\Core\Setup\Setup;
use MyVideoRoomPlugin\Core\Shortcode\UserVideoPreference;
use MyVideoRoomPlugin\Core\SiteDefaults;


return function (
	string $active_tab,
	array $tabs,
	array $messages = array(),
	array $module_tabs = array()
): string {

	$render = require __DIR__ . '/header/header.php';
	//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
	echo $render( $messages, $module_tabs );

	ob_start();

	// Check to see if Default settings exist on entry- reinitialise if missing.
	if ( ! Factory::get_instance( RoomAdmin::class )->check_default_settings_exist() ) {
		Factory::get_instance( Setup::class )->initialise_default_video_settings();
	}

	?>
<div class="mvr-outer-box-wrap">
		<h1><?php echo esc_html__( 'Video Room Site Default Configuration', 'my-video-room' ); ?></h1>
		<?php
		$security_enabled = Factory::get_instance( ModuleConfig::class )->module_activation_status( SiteDefaults::MODULE_SECURITY_ID );
		if ( $security_enabled ) {
			echo esc_html( Factory::get_instance( \MyVideoRoomExtrasPlugin\Modules\Security\Templates\SecurityButtons::class )->site_wide_enabled() );
		}

		echo '<p>';
		echo esc_html__(
			'The Following Settings define site wide video default parameters in case other modules have not set parameters there. These defaults will be used 
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

