<?php
/**
 * Allow testing of global
 *
 * @package ClubCloudGamesPlugin\Test
 */

namespace MyVideoRoomPluginTest;

/**
 * Class Globals
 */
class Globals {

	/**
	 * List of injected mocks
	 *
	 * @var array
	 */
	private static array $injections = array();

	/**
	 * Number of times a global function has been called since reset
	 *
	 * @var int
	 */
	private static int $call_count = 0;

	/**
	 * Inject a mock for a global function
	 *
	 * @param string     $name      The name of the global function to mock.
	 * @param callable[] $callbacks An array of functions to execute instead.
	 */
	public static function inject( string $name, array $callbacks ) {
		self::$injections[ $name ] = $callbacks;
	}

	/**
	 * Reset all injections and call count
	 */
	public static function reset() {
		self::$injections = array();
		self::$call_count = 0;
	}

	/**
	 * Get the number of times a global function has been called since reset
	 *
	 * @return int
	 */
	public static function get_call_count(): int {
		return self::$call_count;
	}

	/**
	 * Magic __call to capture all global methods
	 *
	 * @param string $name The global function name.
	 * @param array  $args A list of arguments for the function.
	 *
	 * @return mixed
	 * @throws \Exception Thrown when the function has not been mocked.
	 */
	public function __call( string $name, array $args ) {
		if ( self::$injections[ $name ] ?? false ) {
			if ( is_array( self::$injections[ $name ] ) ) {
				$callback = array_shift( self::$injections[ $name ] );
			} else {
				$callback = self::$injections[ $name ];
				unset( self::$injections[ $name ] );
			}

			++self::$call_count;

			return $callback( ...$args );
		}

		throw new \Exception( 'Global function ' . $name . ' should be mocked' );
	}
}
