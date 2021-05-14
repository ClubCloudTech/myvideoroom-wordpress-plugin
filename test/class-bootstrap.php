<?php
/**
 * Bootstrap the tests
 *
 * @package ClubCloudGamesPlugin\Test
 */

declare(strict_types=1);

namespace MyVideoRoomPluginTest;

/**
 * Class Bootstrap
 */
class Bootstrap {

	/**
	 * Bootstrap constructor.
	 */
	public function __construct() {
		//phpcs:ignore WordPress.PHP.IniSet.memory_limit_Blacklisted
		ini_set( 'memory_limit', '1GB' );

		include_once __DIR__ . '/globals/functions.php';
		include_once __DIR__ . '/class-globals.php';

		include_once __DIR__ . '/../my-video-room/index.php';
	}
}

new Bootstrap();
