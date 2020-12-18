<?php

    class ClubCloudVideoPlugin {
        public const PLUGIN_NAMESPACE = 'cc';

        public const SETTING_VIDEO_SERVER_URL = self::PLUGIN_NAMESPACE . '_video_server_url';
        public const SETTING_ROOM_SERVER_URL = self::PLUGIN_NAMESPACE . '_room_server_url';
        public const SETTING_APP_SERVER_URL = self::PLUGIN_NAMESPACE . '_app_url';

        public const SETTING_WEB_TOKEN_KEY = self::PLUGIN_NAMESPACE . '_web_token_key';
        public const SETTING_SHARED_SECRET = self::PLUGIN_NAMESPACE . 'clubcloud_shared_secret';

        public const SETTINGS = [
            self::SETTING_VIDEO_SERVER_URL,
            self::SETTING_ROOM_SERVER_URL,
            self::SETTING_APP_SERVER_URL,
            self::SETTING_WEB_TOKEN_KEY,
            self::SETTING_SHARED_SECRET,

        ];

        public static function init()
        {
            return new self();
        }

        // ---

        public function __construct()
        {
            $webTokenKey = getenv('CLUBCLOUD_WEB_TOKEN_KEY') ?: get_option(self::SETTING_WEB_TOKEN_KEY);
            $sharedSecret = getenv('CLUBCLOUD_SHARED_SECRET') ?: get_option(self::SETTING_SHARED_SECRET);

            add_action('admin_init', [$this, 'registerSettings']);

            new ClubCloudVideoPlugin_Admin();
            new ClubCloudVideoPlugin_Shortcode($sharedSecret);
            new ClubCloudVideoPlugin_JWT($sharedSecret, $webTokenKey);
        }

        public function registerSettings()
        {
            foreach (self::SETTINGS as $setting) {
                register_setting(self::PLUGIN_NAMESPACE, $setting);
            }
        }
    }