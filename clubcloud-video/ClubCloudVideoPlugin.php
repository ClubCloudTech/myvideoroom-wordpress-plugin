<?php

class ClubCloudVideoPlugin {
	public const PLUGIN_NAMESPACE = 'cc';

	public const SETTING_VIDEO_SERVER = self::PLUGIN_NAMESPACE . '_video_server_url';
	public const SETTING_PRIVATE_KEY = self::PLUGIN_NAMESPACE . '_private_key';

	public const SETTINGS = [
		self::SETTING_VIDEO_SERVER,
		self::SETTING_PRIVATE_KEY,
	];

	public function __construct() {
		$privateKey = get_option( self::SETTING_PRIVATE_KEY );

		add_action( 'admin_init', [ $this, 'registerSettings' ] );

		new ClubCloudVideoPlugin_Admin();
		new ClubCloudVideoPlugin_AppShortcode( $privateKey,  );
		new ClubCloudVideoPlugin_ReceptionWidgetShortcode( $privateKey );
		new ClubCloudVideoPlugin_JWT( $privateKey);
	}

	// ---

	public static function init() {
		return new self();
	}

	public function registerSettings() {
		foreach ( self::SETTINGS as $setting ) {
			register_setting( self::PLUGIN_NAMESPACE, $setting );
		}
	}
}
