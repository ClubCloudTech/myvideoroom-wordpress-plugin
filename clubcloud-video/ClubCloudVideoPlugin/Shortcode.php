<?php

class ClubCloudVideoPlugin_Shortcode {

	protected function formatText(string $text = null)
	{
		return $text ? base64_encode( htmlspecialchars_decode( $text ) ) : null;
	}
}
