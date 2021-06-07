<?php

namespace MyVideoRoomPlugin\Module\StoredRooms;

use MyVideoRoomPlugin\Library\AppShortcodeConstructor;

class StoredAppShortcode extends AppShortcodeConstructor {

	private ?string $id;

	public static function create_from_shortcode_constructor( ?string $id, AppShortcodeConstructor $shortcode_constructor ): self {
		$stored_app_shortcode = new self( $id );

		if ( $shortcode_constructor->get_layout() ) {
			$stored_app_shortcode->set_layout( $shortcode_constructor->get_layout() );
		}

		if ( $shortcode_constructor->get_name() ) {
			$stored_app_shortcode->set_name( $shortcode_constructor->get_name() );
		}

		if ( $shortcode_constructor->is_floorplan_enabled() ) {
			$stored_app_shortcode->enable_floorplan();
		} else {
			$stored_app_shortcode->disable_floorplan();
		}

		if ( $shortcode_constructor->is_reception_enabled() ) {
			$stored_app_shortcode->enable_reception();
		} else {
			$stored_app_shortcode->disable_reception();
		}

		if ( $shortcode_constructor->get_reception_id() ) {
			$stored_app_shortcode->set_reception_id( $shortcode_constructor->get_reception_id() );
		}

		if ( $shortcode_constructor->get_reception_video() ) {
			$stored_app_shortcode->set_reception_video_url( $shortcode_constructor->get_reception_video() );
		}

		return $stored_app_shortcode;
	}

	public function __construct( ?string $id ) {
		$this->id = $id;
		parent::__construct();
	}

	public function get_id(): ?string {
		return $this->id;
	}

	public function set_id( string $id ): self {
		$this->id = $id;
		return $this;
	}
}
