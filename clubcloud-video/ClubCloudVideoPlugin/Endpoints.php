<?php

class ClubCloudVideoPlugin_Endpoints {

	const CLUBCLOUD_DOMAIN = 'clubcloud.tech';

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

			$this->appEndpoint =  'https://app.' . self::CLUBCLOUD_DOMAIN;
			$this->stateEndpoint =  'https://state.' . self::CLUBCLOUD_DOMAIN;
			$this->roomsEndpoint =  'https://rooms.' . self::CLUBCLOUD_DOMAIN;
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

	public function getSubscribedVideoEndpoint(): string {
		return 'meet.' . self::CLUBCLOUD_DOMAIN;
	}



}
