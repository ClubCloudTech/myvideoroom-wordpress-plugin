<?php

class ClubCloudVideoPlugin_Endpoints {

	private string $videoEndpoint;
	private string $appEndpoint;
	private string $stateEndpoint;
	private string $roomsEndpoint;

	public function __construct( ) {
		$devMode = ($_GET["dev"] ?? false) === 'true';

		$this->videoEndpoint = 'meet.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );

		if ( $devMode ) {
			$this->appEndpoint =  'http://localhost:3000';
			$this->stateEndpoint =  'http://localhost:4001';
			$this->roomsEndpoint =  'http://localhost:4002';
		} else {

			$this->appEndpoint =  'https://app.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );
			$this->stateEndpoint =  'https://state.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );
			$this->roomsEndpoint =  'https://rooms.' . get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER );
		}
	}

	public function getVideoEndpoint(): string {
		return $this->videoEndpoint;
	}

	public function getAppEndpoint(): string {
		return $this->appEndpoint;
	}

	public function getStateEndpoint(): string {
		return $this->stateEndpoint;
	}

	public function getRoomsEndpoint(): string {
		return $this->roomsEndpoint;
	}



}
