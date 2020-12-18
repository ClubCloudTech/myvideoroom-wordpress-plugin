<?php

    class ClubCloudVideoPlugin_Admin {
        public function __construct() {
            add_action( 'admin_menu', [ $this, 'addAdminMenu' ] );
        }

        public function addAdminMenu() {
            add_menu_page(
                'ClubCloud Video Settings',
                'ClubCloud Video Settings',
                'manage_options',
                'clubcloud-video-settings',
                [ $this, 'createAdminPage' ],
                'dashicons-format-chat'
            );
        }

        public function createAdminPage() {
            require( __DIR__ . '/admin/page.php' );
        }
    }
