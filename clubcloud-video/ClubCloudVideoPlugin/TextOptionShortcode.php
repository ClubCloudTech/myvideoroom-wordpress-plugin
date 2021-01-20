<?php

class ClubCloudVideoPlugin_TextOptionShortcode extends ClubCloudVideoPlugin_Shortcode {
	const SHORTCODE_TAGS = [
		'clubcloud_text_option',
	];

	public function __construct( ) {
		foreach(self::SHORTCODE_TAGS as $shortcodeTag) {
			add_shortcode( $shortcodeTag, [ $this, 'createShortcode' ] );
		}
	}

	public function createShortcode(): string {
		$error = 'The clubcloud_text_option should be called from within the clubcloud_monitor shortcode';

		if (
			defined(WP_DEBUG) &&
			WP_DEBUG &&
			defined(WP_DEBUG_LOG) &&
			WP_DEBUG_LOG
		) {
			error_log($error);
		}


		if (
			defined(WP_DEBUG) &&
			WP_DEBUG &&
			defined(WP_DEBUG_DISPLAY) &&
			WP_DEBUG_DISPLAY
		) {
			return '<span style="color: red;">' . $error . '</span>';
		}


		return '';
	}

}
